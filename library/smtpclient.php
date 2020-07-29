<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\smtpclient.php
//
// ======================================


class smtpclient
{
	// Connection information
	private $security = 'tls';
	private $port = 465;
	private $hostname = '';

	// sender inforamtion
	private $sender = '';// address, not name.
	private $password = '';
	private $user = '';// Name, not address.

	// Mail information
	private $recipients = [];
	private $attachments = [];
	private $headers = [];
	private $bodies = [];
	private $subject = '';


	public function __construct(string $hostname, int $port = 465, string $security = 'tls')
	{
		if(
			!extension_loaded('openssl') &&
			($security === 'tls' || $security === 'ssl')
		) {
			throw new Exception('OpenSSL not found. OpenSSL is needed for ssl/tls connections');
		}

		$this->security = strtolower($security);
		$this->hostname = strtolower($hostname);
		$this->port = $port;
	}


	/**
	* Sets a recipient
	*
	* @param string $recipient
	*		Recipient you want to set.
	*/
	public function setRecipient(string $recipient)
	{
		$this->recipients[] = $recipient;
	}

	/**
	* Removes all recipients
	*/
	public function clearRecipients()
	{
		$this->recipients = [];
	}


	/**
	* Sets the email subject
	*
	* @param string $subject
	*		Subject of emailExists
	*/
	public function setSubject(string $subject)
	{
		$this->subject = $subject;
	}

	/**
	* Sets the autenticated user
	*
	* @param string $sender
	*		The senders address (your address)
	* @param string $password
	*		Authentication password
	*/
	public function setUser(string $user, string $sender, string $password)
	{
		$this->sender = $sender;
		$this->user = $user;
		$this->password = $password;
	}

	/**
	* Sets the header to the html body
	*
	* @param string $name
	*		Name of the header
	* @param string $value
	*		Value of the header.
	*/
	public function setHeader(string $name, string $value)
	{
		$this->headers[strtolower($name)] = $value;
	}

	/**
	* Sets the body for the mail object
	*
	* @param string $body
	*		Body of the email.
	* @param string $contentType
	*		The type of content.
	* @param array $headers
	*		Additional headers (key is name)
	* @param array $charset
	*		The used charset
	*/
	public function setbody(string $body, string $contentType = 'text/plain', array $headers = [], string $charset = 'UTF-8')
	{
		// lowering header names
		$headers_ = [];
		foreach($headers as $hName => $hValue) {
			$headers_[strtolower($hname)] = $hValue;
		}
		$headers = $headers_;

		unset($headers['content-type']);

		// Adding body
		$this->bodies[] = [
			'body' => $body,
			'content-type' => $contentType,
			'headers' => $headers,
			'charset' => $charset
		];
	}

	/**
	* Sets the bodies/subject through a email_interface
	*
	* @param email_interface $mail
	*		The object we are getting the mail from
	*/
	public function set(email_interface $mail)
	{
		$this->setSubject($mail->buildSubject());
		$bodies = $mail->buildBodies();
		foreach($bodies as $body) {
			$this->setBody($body->body, $body->contentType);
		}
	}

	/**
	* Attaches a file to be sent
	*
	* @param string $options
	*		Options for the file
	*		location: The path at which this file exists (Required)
	*		filename: Name of the file (Required)
	*		inline: Whether or not this is an inline attachment, defualt false (optional)
	*		content-type: The content-type
	*/
	public function attachFile($options)
	{
		if(!isset($options['location'])) {
			throw new Exception('location not found');
		}

		if(!isset($options['filename'])) {
			throw new Exception('filename not found');
		}

		if(!file_exists($options['location'])) {
			throw new Exception('location doesnt exist');
		}

		if(!isset($options['content-type'])) {
			$options['content-type'] = 'application/octlet-stream';
		}

		$this->attachments[] = $options;
	}

	/**
	* Removes all attached files
	*/
	public function detatchFiles()
	{
		$this->attachments = [];
	}

	/**
	* Sends the email.
	*/
	public function send()
	{
		$sock = null;
		$hostname = $this->hostname;
		if($this->security === 'tls' || $this->security === 'ssl') {
			$hostname = "{$this->security}://{$hostname}";
		}
		if(!$sock = fsockopen($hostname, $this->port, $errorno, $errstr)) {
			throw new Exception("cannot connect to {$hostname}: ({$errorno}) {$errstr}");
		}

		$sendData = function($data) use(&$sock) {
			return fwrite($sock, $data);
		};
		$sendCommand = function(string $command, string $param = '') use(&$sock, &$sendData) {
			$cmd = ((strlen($param) > 0)
				? "{$command} {$param}\r\n"
				:"{$command}\r\n"
			);
			if(!$sendData($cmd)) {
				throw new Exception('sock write error');
			}
		};
		$readResponse = function() use(&$sock) {
			$dat = '----';
			while(isset($dat[3]) && $dat[3] === '-') {
				$dat = fgets($sock, 256);
			}
			if($p = strpos($dat, "\r\n")) {
				$datLen = $p;
				$dat = substr($dat, 0, $p);
			}
			else {
				throw new Exception('Line end not found');
			}

			if($datLen < 3) {
				throw new Exception('Response too short');
			}

			$statusCode = substr($dat, 0, 3);
			$statusMessage = '';
			if($datLen > 4) {
				$statusMessage = substr($dat, 4);
			}

			return [
				'code' => intval($statusCode),
				'param' => $statusMessage
			];
		};

		$awaitSuccess = function() use (&$sock, &$readResponse) {
			$res = $readResponse();
			return !in_array($res, [
				200,
				211,
				214,
				220,
				221,
				250,
				251,
				252,
				354
			]);
		};
		$awaitWarning = function() use (&$sock, &$readResponse) {
			$res = $readResponse();
			return ($res / 100 === 4);
		};
		$awaitError = function() use (&$sock, &$readResponse) {
			$res = $readResponse();
			return ($res / 100 === 5);
		};

		if(!$awaitSuccess()) {
			// Server returned non-success on initial packet...
			return ff_return(false);
		}

		$sendCommand('EHLO', gethostname());
		if(!$awaitSuccess()) {
			return ff_return(false);
		}

		if($this->user && $this->password) {
			$sendCommand('AUTH', 'LOGIN');
			if(!$awaitSuccess()) {
				return ff_return(false);
			}

			$sendCommand(base64_encode($this->user));
			if(!$awaitSuccess()) {
				return ff_return(false);
			}

			$sendCommand(base64_encode($this->password));
			if(!$awaitSuccess()) {
				return ff_return(false);
			}
		}

		$sendCommand('MAIL FROM:', "<{$this->sender}>");
		if(!$awaitSuccess()) {
			return ff_return(false);
		}

		$successRecipientCount = 0;
		foreach($this->recipients as $recipient) {
			$sendCommand('RCPT TO:', "<{$recipient}>");
			if($awaitSuccess()) {
				$successRecipientCount++;
			}
		}
		if($successRecipientCount === 0) {
			return ff_return(false, (object)['invalidrecipients' => 1]);
		}

		$sendCommand('DATA');
		if(!$awaitSuccess()) {
			return ff_return(false);
		}

		$boundary = cryptography::randomString(16);

		$this->headers['mime-version'] = '1.0';
		$this->headers['subject'] = $this->subject;
		$this->headers['to'] = '<'. implode('>, <', $this->recipients) .'>';
		if(count($this->recipients) > 1) {
			$this->headers['bcc'] = $this->headers['to'];
		}
		$this->headers['from'] = $this->sender;

		$__hs = &$this->headers;// Storing headers in a referenced variable... used for push headers.
		$pushHeaders = function() use(&$__hs, &$data) {
			$data = '';
			foreach($this->headers as $key => $value) {
				$data .= "{$key}: {$value}\r\n";
			}
			$data .= "\r\n";
		};

		$bodyCount = count($this->bodies);// https://www.youtube.com/watch?v=--YgtVuvWGo
		$attachmentCount = count($this->attachments);
		if(
			$attachmentCount === 0 &&
			$bodyCount === 0
		) {
			// no body? lol. therefore there cannot be a content type :|
			unset($this->headers['content-type']);
			$pushHeaders();

			// and done... lol.
		}
		else if(
			$attachmentCount === 0 &&
			$bodyCount === 1
		) {
			// One body, no attachments.
			$body = $this->bodies[0];
			$this->headers['content-type'] = $body['content-type'];
			$this->headers['content-transfer-encoding'] = 'quoted-printable';
			$pushHeaders();

			$data .= quoted_printable_encode($this->bodies[0]['body']);
		}
		else if(
			$attachmentCount === 1 &&
			$bodyCount === 0
		) {
			// One attachment, no body.
			$attachment = $this->attachments[0];
			$size = filesize($attachment['location']);
			$this->headers['content-type'] = $attachment['content-type'];
			$this->headers['content-transfer-encoding'] = 'base64';
			$this->headers['content-length'] = strval($size);
			$this->headers['content-disposition'] = ($attachment['inline'] ? 'inline' : 'attachment') ."; filename=\"{$attachment['filename']}\"\r\n";
			$pushHeaders();

			// Sending data.. jaja
			if($f = fopen($attachment['location'], 'r')) {
				$data .= base64_encode(fread($f, $size));
				fclose($f);
			}
		}
		else if(
			$attachmentCount === 0 &&
			$bodyCount >= 1
		) {
			$this->headers['content-type'] = "multipart/alternative; boundary=\"{$boundary}\"";
			$pushHeaders();

			foreach($this->bodies as $key => $body) {
				$data .= "--$boundary\r\n";
				$data .= "Content-Type: {$body['content-type']}; charset=\"{$body['charset']}\"\r\n";
				$data .= "Content-Transfer-Encoding: quoted-printable\r\n";
				foreach($body['headers'] as $key => $value) {
					$data .= "{$key}: {$value}\r\n";
				}

				$data .= "\r\n";
				$data .= quoted_printable_encode($body['body']);
				$data .= "\r\n";

				if($bodyCount - 1 == $key) {
					$data .= "--{$boundary}--\r\n";
				}
			}
		}
		else if(
			$attachmentCount >= 1 &&
			$bodyCount >= 0
		) {
			throw new Exception('Attachments & No Bodies not implemented');
		}
		else if(
			$attachmentCount >= 1 &&
			$bodyCount >= 1
		) {
			throw new Exception('attachments and bodies not implemented');
		}

		$data .= ".\r\n";
		$sendData($data);
		if(!$awaitSuccess()) {
			return ff_return(false);
		}

		$sendCommand('QUIT');
		fclose($sock);

		return ff_return(true);
	}
}

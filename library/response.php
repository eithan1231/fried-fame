<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\response.php
//
// ======================================


class response
{
	/**
	* List of response headers. Key is header name, value is value.
	*/
	private $headers = [];

	/**
	* The HTTP status (aka, first http header line)
	*/
	private $status = 200;
	private $statusDescriptor = 'Fried-Fame';

	/**
	* The used charset
	*/
	private $charset = FF_CHARSET;

	/**
	* The HTTP body content.
	*/
	private $body = '';

	/**
	* bool indicating whether or not content (headers, body) has been flushed
	*/
	private $hasFlushed = false;

	/**
	* List of response cookies
	*/
	private $cookies = [];

	/**
	* Boolean indicating whehter or not we are output buffering.
	*/
	private $outputBuffering = false;

	/**
	* The redirect path. Leave empty for no redirect.
	* NOTE: Redirect overrules all other headers, and bodys. So they WILL be ignored
	* on redirect.
	*/
	private $redirect = null;

	private $sendFile = null;

	function __construct()
	{
		$this->reset();
	}

	/**
	* Sets a response http header.
	*
	* @param string $name
	*		Name of HTTP header.
	* @param string $value
	*		Value of HTTP header.
	*/
	public function setHttpHeader(string $name, string $value)
	{
		$name = ff_stripEndingBlanks($name);
		$nameLen = strlen($name);
		$value = ff_stripEndingBlanks($value);
		$valueLen = strlen($value);

		if($nameLen <= 0 || $valueLen <= 0) {
			throw new Exception("Header name, or value, is too short.");
		}

		$this->headers[strtolower($name)] = $value;
	}

	public function setHeader(string $name, string $value)
	{
		return $this->setHttpHeader($name, $value);
	}

	/**
	* Sets http status
	*
	* @param int $status
	*		Status of the response.
	* @param string $descriptor
	*		The string descriptor
	*/
	public function setHttpStatus(int $status, string $descriptor = '')
	{
		if(strlen($descriptor) === 0) {
			$descriptor = ff_getHttpStatusDescriptor($status);
		}

		$this->status = $status;
		$this->statusDescriptor = "FF {$descriptor}";
	}

	/**
	* Function to check whether or not we have flushed the content/headers.
	*/
	private function hasFlushed()
	{
		return $this->hasFlushed || headers_sent();
	}

	/**
	* Appends the response body
	*
	* @param string $string
	*		The content you want to append
	*/
	public function appendBody(string $string)
	{
		$this->body .= $string;
	}

	/**
	* Sets the charset
	*/
	public function setCharset(string $charset)
	{
		if($this->hasFlushed()) {
			throw new Exception("Content has already been flushed");
		}

		$this->charset = $charset;
	}

	/**
	* Sets a cookie
	*
	* @param string $name
	*		Name of the cookie
	* @param string $value
	*		Value of the cookie
	* @param array $options
	*		Options for the cookie. will just append to header "; {Key}: {Value}". If
	*		you set expires in options, and its a numeric value, it will auto parse
	*		the date.
	*/
	public function setCookie(string $name, string $value, array $options = [])
	{
		if($this->hasFlushed()) {
			throw new Exception("Content has already been flushed");
		}

		$name = strtolower($name);
		$this->cookies[$name] = [
			'value' => $value,
			'options' => $options
		];
	}

	/**
	* Redirects the page..
	*
	* @param string $path
	*		The path to which we want to redirect.
	*/
	public function redirect(string $path, int $status = 302)
	{
		if($this->hasFlushed()) {
			throw new Exception("Content has already been flushed");
		}

		$this->redirect = [
			'path' => $path,
			'status' => $status
		];

		$this->clearBody();
	}

	/**
	* Cancels a redirect.
	*/
	public function cancelRedirect()
	{
		if($this->hasFlushed()) {
			throw new Exception("Content has already been flushed");
		}

		$this->redirect = null;
	}

	/**
	* Clears the response body buffer.
	*
	* NOTE: If content has already been sent (with flush function), this CANNOT
	* remove that data!
	*/
	public function clearBody()
	{
		$this->body = '';
		$this->sendFile = null;
	}

	/**
	* Remvoes a header from the response.
	*
	* @param string $headerName
	*		The name of the header we want to remove.
	*/
	public function removeHeader(string $headerName)
	{
		if($this->hasFlushed()) {
			throw new Exception('Content has already been flushed.');
		}

		$headerName = strtolower($headerName);
		if(isset($this->headers[$headerName])) {
			unset($this->headers[$headerName]);
		}
	}

	/**
	* Clears all registered headers
	*/
	public function clearHeaders()
	{
		foreach($this->headers as $key => $value) {
			$this->removeHeader($key);
		}
	}

	/**
	* Resets the state of the response. (clears everything)
	*/
	public function reset()
	{
		if($this->hasFlushed()) {
			throw new Exception("Cannot reset; Content has already been flushed.");
		}

		$this->clearHeaders();
		$this->clearBody();
		$this->setCharset(FF_CHARSET);
		$this->setHttpStatus(200);
		$this->setHttpHeader('Content-Type', 'text/plain; charset='. $this->charset);
	}

	/**
	* pushes JSON content to output buffer
	*
	* @param array $json
	*		Json object we want to sent
	* @param int $status
	*		The HTTP status we send
	*/
	public function json($json, $status = 200)
	{
		$this->clearBody();
		$this->setHttpHeader('content-type', 'application/json');
		$this->appendBody(json_encode($json));
		$this->setHttpStatus($status);
		return true;
	}

	/**
	* Make the response object collect from output buffer.
	*/
	public function startOutputBuffer()
	{
		if($this->outputBuffering) {
			return;
		}

		ob_start(function($data) {
			global $ff_response;
			$ff_response->appendBody($data);
			return '';
		});

		$this->outputBuffering = true;
	}

	/**
	* Stops output buffering.
	*/
	public function stopOutputBuffer()
	{
		if(!$this->outputBuffering) {
			return;
		}

		ob_end_flush();

		$this->outputBuffering = false;
	}

	/**
	* Appends file to output buffer
	*
	* @param string $file
	*		File to be appended to output buffer.
	*/
	public function appendFile(string $file)
	{
		if(file_exists($file)) {
			if($f = fopen($file, 'r')) {
				$this->appendBody(
					fread(
						$f,
						filesize($file)
					)
				);
				fclose($f);
				return true;
			}
			else {
				throw new Exception("Unable to open file");
			}
		}

		return false;
	}

	/**
	* Sends file rather than appending it to putbut buffer
	*
	* @param string $file
	*		File to be appended to output buffer.
	*/
	public function sendFile(string $file)
	{
		if(file_exists($file)) {
			$this->sendFile = $file;
			return true;
		}

		return false;
	}

	/**
	* Flushes the response (sends to client)
	*/
	public function flush()
	{
		global $ff_request;

		// Setting redirect data... then doing all else.
		if($this->redirect !== null && !$this->hasFlushed()) {
			// Redirect.
			$status = $this->redirect['status'];

			// Checking status (Redirect statuses are weird... stupid new http standards)
			switch($ff_request->getProtocol()) {
				case 'HTTP/0.9':
				case 'HTTP/1.0': {
					if($status === 303) {// Unsupported status.
						$status = 302;
					}

					if($status === 305) {// Unsupported status.
						// There is no similar to 305-Use-Proxy, so let's just do a temp
						// redirect.
						$status = 302;
					}
					break;
				}

				case 'HTTP/1.1':
				case 'HTTP/2.0': {
					if($status === 302) {// Unsupported in new versions
						$status = 303;
					}
					break;
				}
			}

			// Sending location header, before status.
			header('Location: '. $this->redirect['path']);

			// Set and send status
			$this->setHttpStatus($status);
		}

		if(!$this->hasFlushed()) {
			// Setting first-line header.
			header("{$ff_request->getProtocol()} {$this->status} {$this->statusDescriptor}");
			header('Status: '. $this->status);
			header('Status-Descriptor: '. $this->statusDescriptor);// never seen implemeted, oh well.

			// Setting content-length
			if($this->sendFile) {
				header('Content-Length: '. filesize($this->sendFile));
			}
			else {
				header('Content-Length: '. strlen($this->body));
			}

			// Setting cookies.
			foreach ($this->cookies as $name => $value) {
				header(ff_buildCookieHeader($name, $value['value'], $value['options']));
			}

			foreach ($this->headers as $key => $value) {
				// Skipping headers that are set elsewhere.
				if(
					$key === 'status-descriptor' ||
					$key === 'content-length' ||
					$key === 'status'
				) {
					continue;
				}

				header("{$key}: {$value}");
			}
		}

		// Sending the body (given it's not a head request)
		if($ff_request->getMethod() !== 'HEAD') {
			if($this->sendFile) {
				readfile($this->sendFile);
			}
			else {
				if($this->outputBuffering) {
					// We are output buffering, so let's stop quickly, output data, then continue.
					$this->stopOutputBuffer();
					echo $this->body;
					$this->startOutputBuffer();
				}
				else {
					echo $this->body;
				}
			}
		}

		$this->clearBody();

		$this->hasFlushed = true;
	}
}

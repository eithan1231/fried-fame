<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\request.php
//
// ======================================


/**
* Class that handles request,
*/

class request
{
	const METHOD_ANY = 0;
	const METHOD_GET = 1;
	const METHOD_POST = 2;

	/**
	* Whether or not this was run from command line.
	*/
	private $isFromCommandLine = false;

	/**
	* The request method used.
	*/
	private $method = 'GET';

	/**
	* The HTTP protocol that was used.
	*/
	private $protocol = 'HTTP/1.1';

	/**
	* The requested path on the current request.
	*/
	private $path = '/';

	/**
	* The query string for the current request.
	*/
	private $query = '';

	/**
	* The clients IP address.
	*/
	private $clientIp = '';

	/**
	* The requst content-type.
	*/
	private $contentType = '';

	/**
	* Content length header.
	*/
	private $contentLength = 0;

	/**
	* The raw post data.
	*/
	private $postData = '';

	/**
	* HTTP request headers. Name is key.
	*/
	private $headers = [];

	/**
	* The list of request cookies
	*/
	private $cookies = [];

	/**
	* Whether or not request was secure
	*/
	private $secure = false;

	private $files = [];

	function __construct()
	{
		global $ff_config;

		if(PHP_SAPI === 'cli' || empty($_SERVER['REMOTE_ADDR'])) {
			$this->method = 'GET';
			$this->protocol = 'CLI';
			$query = isset($argv) ?? http_build_query($argv);
			$this->contentType = '';
			$this->contentLength = 0;
			$this->postData = '';
			$this->path = '/';
			$this->clientIp = '127.0.0.1';
			$this->isFromCommandLine = true;

			return;
		}

		// Request method...
		$this->method = $_SERVER['REQUEST_METHOD'];

		// Protocol handling
		$this->protocol = (empty($_SERVER['SERVER_PROTOCOL'])
			? $this->protocol
			: $_SERVER['SERVER_PROTOCOL']
		);

		// Query string
		$this->query = $_SERVER['QUERY_STRING'];

		// Content type
		$this->contentType = (isset($_SERVER['HTTP_CONTENT_TYPE'])
			? $_SERVER['HTTP_CONTENT_TYPE']
			: ''
		);

		// Content Length
		$this->contentLength = (isset($_SERVER['HTTP_CONTENT_LENGTH'])
			?	intval($_SERVER['HTTP_CONTENT_LENGTH'])
			: 0
		);

		// Getting post data.
		if($this->contentLength < (1024 * 128)) {
			$this->postData = file_get_contents("php://input");
		}

		// Handling path
		$this->path = $_SERVER['REQUEST_URI'];
		if(($pos = strpos($this->path, '?')) !== false) {
			$this->path = substr($this->path, 0, $pos);
		}

		// Setting headers.
		foreach ($_SERVER as $key => $value) {
			if(substr($key, 0, 5) == "HTTP_") {
				$name = strtolower(substr($key, 5));
				$name = str_replace('_', '-', $name);

				$this->headers[$name] = $value;
			}
		}

		foreach($_COOKIE as $cookieName => $cookieValue) {
			$this->cookies[strtolower($cookieName)] = $cookieValue;
		}

		$this->clientIp = $_SERVER['REMOTE_ADDR'];
		if($ff_config->get('cloudflare-mode') && isset($this->headers['cf-connecting-ip'])) {
			$this->clientIp = $this->headers['cf-connecting-ip'];
		}
		if($ff_config->get('nginx-reverse-proxy-mode') && isset($this->headers['x-client-ip'])) {
			$this->clientIp = $this->headers['x-client-ip'];
		}

		if($ff_config->get('cloudflare-mode') && isset($this->headers['x-forwarded-proto'])) {
			// Cloudflare sets x-forwarded-proto with the protocol being used.
			$this->secure = ($this->headers['x-forwarded-proto'] === 'https');
		}
		else {
			// Checks if this was sent with HTTPS. Checks if this is behind nginx
			// reverse proxy, and also checks if request was done via SSL with this
			// instance of a HTTP server
			$this->secure = (
				// Local http server
				(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||

				// Nginx reverse-proxy
				(isset($this->headers['https']) && $this->headers['https'] == 'on')
			);
		}

		$this->files = $_FILES;
		foreach ($this->files as $file => &$value) {
			$value = [
				'error' => $value['error'],
				'name' => $value['name'],
				'type' => $value['type'],
				'tempName' => $value['tmp_name'],
				'size' => $value['size'],
			];
		}
	}

	/**
	* Checks whether this script execution is from command line.
	* @return bool true means its run from command line, false means it isnt.
	*/
	public function isCLI()
	{
		return $this->isFromCommandLine;
	}

	/**
	* Gets the HTTP method
	*/
	public function getMethod()
	{
		return $this->method;
	}

	/**
	* Whether or not request was secure.
	*/
	public function getSecure()
	{
		return $this->secure;
	}

	/**
	* Wrapper for getSecure
	*/
	public function isSecure()
	{
		return $this->getSecure();
	}

	/**
	* Gets the HTTP protocol. I.E. HTTP/1.1
	*/
	public function getProtocol()
	{
		return $this->protocol;
	}

	/**
	* Gets the HTTP path.
	*/
	public function getPath()
	{
		return $this->path;
	}

	/**
	* Gets the HTTP query.
	*/
	public function getQuery()
	{
		return $this->query;
	}

	/**
	* Gets the requestee's IP.
	*/
	public function getIp()
	{
		return $this->clientIp;
	}

	/**
	* Gets the post data.
	*/
	public function getPost()
	{
		return $this->postData;
	}

	/**
	* Will attempt to get a GEt or POST field.
	*
	* @param string $name
	*		Not found
	* @param int $mode
	*		METHOD_ANY: GET or POST
	*		METHOD_GET: GET
	*		METHOD_POST: POST
	*/
	public function get(string $name, int $mode = 0)
	{
		if(($mode == self::METHOD_ANY || $mode == self::METHOD_GET) && isset($_GET[$name])) {
			return $_GET[$name];
		}
		if(($mode == self::METHOD_ANY || $mode == self::METHOD_POST) && isset($_POST[$name])) {
			return $_POST[$name];
		}
		return false;
	}

	public function getAllFields(int $mode = 0)
	{
		if($mode == self::METHOD_ANY) {
			return array_merge($_GET, $_POST);
		}

		if($mode == self::METHOD_GET && count($_GET)) {
			return $_GET;
		}

		if($mode == self::METHOD_POST && count($_POST)) {
			return $_POST;
		}

		return false;
	}

	/**
	* Gets the accept languages header, but in it's parsed form.
	*/
	public function getAcceptLanguage()
	{
		$h = $this->getHeader("Accept-Language");
		if(!$h) {
			return false;
		}

		return ff_parseAcceptLanguage($h);
	}

	/**
	* Gets a HTTP request header.
	*
	* @param string $name
	*		The name of the header we're fetching.
	*/
	public function getHeader(string $name)
	{
		$name = strtolower($name);
		$name = str_replace('_', '-', $name);
		if(isset($this->headers[$name])) {
			return $this->headers[$name];
		}

		return false;
	}

	/**
	* Gets a cookie from the request.
	*
	* @param string $name
	*		The cookie whose value you're getting.
	*/
	public function getCookie(string $name)
	{
		$name = strtolower($name);

		if(!isset($this->cookies[$name])) {
			return false;
		}

		return $this->cookies[$name];
	}

	/**
	* Gets an uploaded file by name
	* @param string $name
	*		name of file
	* @return object (properties: name, size, error, tempName, type)
	*/
	public function getFile(string $name)
	{
		if(isset($this->files[$name])) {
			return (object)$this->files[$name];
		}
		return null;
	}

	/**
	* Checks that this request is valid.
	*/
	public function check(&$error = [])
	{
		global $ff_response, $ff_config, $ff_sql;

		if($this->isFromCommandLine) {
			// From command line, we can assume yes.
			return true;
		}

		$ret = true;

		if(!function_exists('curl_init')) {
			$error[] = 'curl';
			$ret = false;
		}

		if(!ff_isDevelopment()) {
			$hostHeader = strtolower(ff_stripEndingBlanks($this->getHeader('host')));
			$trustedHosts = $ff_config->get('trusted-hostnames');
			if(!in_array($hostHeader, $trustedHosts)) {
				$ret = false;
				$error[] = 'hostname';
			}
		}

		return $ret;
	}
}

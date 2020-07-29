<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\http\response.php
//
// ======================================


class http_response
{
  const CRLF = "\r\n";
  private $headers = [];
  private $status = 200;
  private $statusDescriptor = 'OK';
  private $body = '';

  public function __construct(http_request $request)
  {
    global $ff_config;

    // Building main header, and host header.
    $rawRequest = $request->getMethod() .' '. $request->getPath() .' HTTP/1.0'. self::CRLF;
    $rawRequest .= 'Host: '. $request->getHost() . self::CRLF;

    // Adding user auth headers.
    if(!empty($request->getUser()) && !empty($request->getPass())) {
      $rawRequest .= 'Authorization: Basic '. base64_encode($request->getUser() .':'. $request->getPass()) . self::CRLF;
    }

    // Adding custom user headers.
    $headers = $request->getHeaders();
    foreach ($headers as $key => $value) {
      $rawRequest .= $key .': '. $value . self::CRLF;
    }

    // Setting body-related headers.
    $contentRaw = $request->getBody();
    $contentLength = strlen($contentRaw);
    if($contentLength > 0) {
      $rawRequest .= 'Content-Length: '. strval($contentLength) . self::CRLF;
    }

    $rawRequest .= 'Accept: */*'. self::CRLF;
    $rawRequest .= 'User-Agent: +(UA"'. $ff_config->get('project-name') .'")'. self::CRLF;

    // Adding content.
    $rawRequest .= self::CRLF;
    $rawRequest .= $contentRaw;

    // Getting hostname
    $hostname = $request->getHost();
    if($request->getScheme() === http_request::SCHEME_HTTPS) {
      $hostname = 'ssl://'. $hostname;
    }
    else {
      $hostname = 'tcp://'. $hostname;
    }

    $sock = fsockopen($hostname, $request->getPort(), $errno, $errstr);
    if($sock) {
      $fgets = function() use (&$sock, &$chunkSize){
        return fgets($sock, $chunkSize);
      };

      if(!fwrite($sock, $rawRequest)) {
        throw new Exception('Failed to write to socket');
      }

      $chunkSize = 1024;
      $response = $fgets();
      while($dat = $fgets()) {
        $response .= $dat;
        if(strpos($response, self::CRLF . self::CRLF) !== false) {
          break;
        }
      }

      $this->handleHeaders($response);

      $bodyStart = strpos($response, self::CRLF . self::CRLF) + 4;
      $contentAppended = 0;

      // Finding content length.
      $contentLength = $this->findContentLength();
      $t = time();// Used for timeout
      while(!feof($sock)) {
        // Getting data.
        $dat = $fgets();

        // Adding to contentAppend variable the amount read.
        $contentAppended += mb_strlen($dat);

        // Appending body
        $this->body .= $dat;

        if($t < time() - 30) {
          // timeout
          break;
        }
      }

      fclose($sock);
    }
    else {
      throw new Exception($errstr);
    }
  }

  private function findContentLength()
  {
    if(isset($this->headers['content-length'])) {
      return intval($this->headers['content-length']);
    }
    return -1;
  }

  private function handleHeaders(string $response)
  {
    $responseLen = strlen($response);
    $headerEnd = strpos($response, self::CRLF . self::CRLF);
    if(!$headerEnd) {
      throw new Exception('Cannot find header end');
    }

    $rawHeaders = substr($response, 0, $headerEnd);
    $explodedHeaders = explode(self::CRLF, $rawHeaders);
    if(!isset($explodedHeaders[0])) {
      throw new Exception('Invalid response. Header line one cannot be found.');
    }

    foreach($explodedHeaders as $k => $h) {
      if($k === 0 && substr($h, 0, 4) === 'HTTP') {
        list($httpVersion, $status, $statusDescriptor) = explode(' ', $h, 3);
        $this->status = $status;
        $this->statusDescriptor = $statusDescriptor;
      }
      else {
        list($name, $value) = explode(':', $h, 2);
        $value = ff_stripEndingBlanks($value);
        $this->headers[strtolower($name)] = $value;
      }
    }
  }

  public function getHeader(string $name)
  {
    $name = strtolower($name);
    if(isset($this->headers[$name])) {
      return $this->headers[$name];
    }
    return false;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function getCode()
  {
    return $this->getStatus();
  }

  public function getStatusDescriptor()
  {
    return $this->statusDescriptor;
  }

  public function getCodeDescriptor()
  {
    return $this->getStatusDescriptor();
  }

  public function getBody()
  {
    return $this->body;
  }
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\http\request.php
//
// ======================================


class http_request
{
  const SCHEME_HTTP = 0;
  const SCHEME_HTTPS = 1;

  private $body = '';
  private $method = 'GET';
  private $scheme = self::SCHEME_HTTPS;
  private $host = '';
  private $port = 80;
  private $user = '';
  private $pass = '';
  private $path = '/';
  private $headers = [];

  public function setMethod(string $method)
  {
    $this->method = $method;
  }

  public function setBody(string $body)
  {
    $this->body = $body;
  }

  public function setUrl(string $url)
  {
    $parsed = parse_url($url);
    if(!$parsed) {
      return false;
    }

    if(isset($parsed['scheme'])){
      if($parsed['scheme'] === 'http') {
        $this->setScheme(self::SCHEME_HTTP);
      }
      else if($parsed['scheme'] === 'https') {
        $this->setScheme(self::SCHEME_HTTPS);
      }
      else {
        throw new Exception('Unsupported Scheme');
      }
    }

    if(isset($parsed['host'])){
      $this->host = $parsed['host'];
    }

    if(isset($parsed['port'])){
      $this->port = $parsed['port'];
    }

    if(isset($parsed['user'])){
      $this->user = $parsed['user'];
    }

    if(isset($parsed['pass'])){
      $this->pass = $parsed['pass'];
    }

    if(isset($parsed['path'])){
      $this->path = $parsed['path'];
    }

    if(isset($parsed['query'])){
      $this->path = "{$this->path}?{$parsed['query']}";
    }

    return true;
  }

  public function setScheme(int $scheme)
  {
    if($scheme === self::SCHEME_HTTP) {
      $this->port = 80;
      $this->scheme = $scheme;
    }
    else if($scheme === self::SCHEME_HTTPS) {
      $this->port = 443;
      $this->scheme = $scheme;
    }
    else {
      throw new Exception('unsupported scheme');
    }
  }

  public function setHost(string $host)
  {
    $this->host = $host;
  }

  public function setPort(int $port)
  {
    $this->port = $port;
  }

  public function setAuth(string $user, string $pass)
  {
    $this->user = $user;
    $this->pass = $pass;
  }

  public function setPath(string $path)
  {
    if(strpos(str_replace([' ', "\r", "\n", "\t", "\0"], ' ', $path), ' ') !== false) {
      throw new Exception('Path cannot contain spaces.');
    }
    $this->path = $path;
  }

  public function setHeader(string $name, string $value)
  {
    $nameLower = strtolower($name);

    if($nameLower === 'host') {
      $this->host = $value;
      return;
    }

    foreach($this->headers as $key => $value) {
      if(strtolower($key) === $nameLower) {
        unset($this->headers[$key]);
        break;
      }
    }

    $this->headers[$name] = $value;
  }

  public function getMethod()
  {
    return $this->method;
  }

  public function getBody()
  {
    return $this->body;
  }

  public function getScheme()
  {
    return $this->scheme;
  }

  public function getHost()
  {
    return $this->host;
  }

  public function getPort()
  {
    return $this->port;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function getPass()
  {
    return $this->pass;
  }

  public function getPath()
  {
    return $this->path;
  }

  public function getHeaders()
  {
    return $this->headers;
  }
}

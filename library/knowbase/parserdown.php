<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\knowbase\parserdown.php
//
// ======================================


autoloader::load('dependencies/parserdown/parserdown');

/**
* Parses a knowbase post and extracts all nessacary information from it.
*/
class knowbase_parserdown
{
  private $parsed = '';
  private $headers = [];

  /**
  * Intreprets data. We advise against using this function, use the public
  * static "parse" function.
  */
  private function interpret(string $data)
  {
    $parsedown = new Parsedown();

    // Setting up hooks. Weird PHP problem I cannot pass a method as a parameter,
    // so im using these ugly inline functions.
    $parsedown->hookElement(['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], function($element) {
      $this->hookHeaders($element);
    });
    $parsedown->hookElement('a', function($element) {
      $this->hookLink($element);
    });

    $this->parsed = $parsedown->text($data);

    return !!$this->parsed;
  }

  private function hookHeaders($element)
  {
    $element = array_merge([
      'attributes' => [
        'id' => self::generateHeader(
          $element['name'],
          hash('crc32', $element['handler']['argument'])
        )
      ]
    ], $element);

    $this->headers[] = [
      'id' => $element['attributes']['id'],
      'text' => $element['handler']['argument'],
      'type' => $element['name']
    ];

    return $element;
  }

  private function hookLink($element)
  {
    if(isset($element['attributes']['href'])) {
      $url = parse_url($element['attributes']['href']);
      if(
        isset($url['scheme']) &&
        isset($url['path']) &&
        isset($this->{"hookLink_{$url['path']}"})
      ) {
        $element = $this->{"hookLink_{$url['path']}"}($element, $url);
      }
    }

    return $element;
  }

  private function hookLink_article($element, $url)
  {
    global $ff_router;

    // TODO: this post implemntation of routes.
    return $element;
  }

  /**
  * Parses a markdown data and extracts other essential information from it.
  * @param string $data The markdown data
  * @param string $key A unique identifer for caching. Ideal Value:
  *   "{id}::{revision}" Defaults to a hash of $data.
  */
  public static function parse(string $data, string $key = null)
  {
    // TODO: check for cached entry
    if(!$key) {
      $key = hash('sha2', $data);
    }

    $instance = new self();
    return ($instance->interpret($data)
      ? $instance
      : null
    );
  }

  /**
  * Gets parsed markdown data
  */
  public function getParsed()
  {
    return $this->parsed;
  }

  /**
  * Returns array of all headers.
  */
  public function getHeaders()
  {
    return $this->headers;
  }

  private static function generateHeader()
  {
    return 'markdown-'. implode('-', func_get_args());
  }
}

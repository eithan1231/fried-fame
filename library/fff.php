<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\fff.php
//
// ======================================


/**
* fff.php
*
* Fried-Fame Format. This is a special format for storing human-readable
* configurations.
*/
class fff
{
	public $keyValues = [];

	/**
	* The way this class handles errors.
	*
	* 0: will be returning false on the function
	* 1: will be triggering an error
	* 2: will be throwing an exception
	*/
	private $raiseErrorLevel = 0;

	/**
	* Class constructor
	*
	* @param string $unserialized
	*		The data that needs to be serialized.
	* @param array $settings
	*		Settings for FFF.
	*/
	function __construct(string $unserialized, array $settings = [])
	{
		if(!($state = $this->deserialize($unserialized))) {
			return $state;
		}

		if(isset($settings['error_level']) && is_int($settings['error_level'])) {
			$this->raiseErrorLevel = $settings['error_level'];
		}
	}

	/**
	* Checks if a key is considered valid
	*
	* @param string $key
	*		Key we want to see is valid.
	*/
	private function isValidKey(string $key)
	{
		$key = ff_stripEndingBlanks($key);
		if(
			strpos($key, "\r") !== false ||
			strpos($key, "\n") !== false ||
			strpos($key, ' ') !== false ||
			strpos($key, ':') !== false
		) {
			return false;
		}

		return true;
	}

	/**
	* Queries for a key.
	*
	* @param string $key
	*		The key whats value we want.
	*/
	public function read(string $key)
	{
		$key = strtolower($key);
		$key = ff_stripEndingBlanks($key);
		if(!$this->isValidKey($key)) {
			return $this->error('Invalid Key');
		}

		if(!isset($this->keyValues[$key])) {
			return false;
		}

		return $this->keyValues[$key]['value'];
	}

	/**
	* Writes a FFF key
	*
	* @param string $key
	*		The key whose value we are writing to.
	* @param string $value
	*		The value we are assigning to the key
	* @param string|null $keycomment
	*		The comment for the key. The goal of FFF, is human readability, comments
	*		will help that readability.
	*/
	public function write(string $key, string $value, $keyComment = null)
	{
		$key = strtolower($key);
		$key = ff_stripEndingBlanks($key);
		if(!$this->isValidKey($key)) {
			return $this->error('Invalid Key');
		}

		$this->keyValues[$key] = [
			'value' => $value,
			'comment' => $keyComment
		];
		return true;
	}

	/**
	* Builds the serialized version
	*/
	public function serialize()
	{
		$ret = '';
		$ret .= "#============================================================\r\n";
		$ret .= "# Welcome to Fried-Fame Formatter, a simple Key/Value store!\r\n";
		$ret .= "#============================================================\r\n";
		$ret .= "\r\n";

		foreach ($this->keyValues as $key => $value) {
			if($value['comment'] !== null) {
				$ret .= '#';
				$ret .= str_replace(
					["\r\n#", "\n#", "\n", "\r\n"],
					"\r\n#",
					$value['comment']
				);
				$ret .= "\r\n";
			}

			if(strlen($value['value']) > 124) {
				$values = str_split($value['value'], 64);
				$ret .= "{$key}:";
				foreach ($values as $val) {
					if(strlen($val) <= 0) {
						continue;
					}
					$ret .= " {$val}\r\n";
				}
			}
			else {
				$ret .= "{$key}: {$value['value']}";
			}
			$ret .= "\r\n";
		}

		return $ret;
	}

	private function deserialize(string $dat)
	{
		// Getting the lines
		$lines = explode("\n", str_replace("\r\n", "\n", $dat));
		if($lines === false) {
			return false;
		}

		$previousKey = null;
		foreach ($lines as $i => $line) {
			$lineLen = strlen($line);
			if($lineLen <= 0) {
				continue;
			}

			// Muti-line values
			if(
				$previousKey !== null &&
				ff_isWhiteSpace($line[0])
			) {
				// Skipping white spaces
				$whitespaceEnd = 0;
				while(
					$lineLen > $whitespaceEnd &&
					ff_isWhiteSpace($line[++$whitespaceEnd]) &&
					$whitespaceEnd < 10
				);

				// Appending multi-line value
				$this->keyValues[$previousKey]['value'] .= substr($line, $whitespaceEnd - 1);
				continue;
			}

			// Checking comment, or invalid line.
			if(
				$line[0] == '#' ||
				strlen($line) < 3
			) {
				continue;
			}

			// Extracting key, and key's value (multi-line will be appended next loop
			// itteration)
			$keyEnd = strpos($line, ':');
			if($keyEnd) {
				$key = substr($line, 0, $keyEnd);
				$key = strtolower($key);
				$value = substr($line, $keyEnd + 1);
				$key = ff_stripEndingBlanks($key);
				$value = ff_stripEndingBlanks($value);

				if($this->isValidKey($key)) {
					$previousKey = $key;
					$this->keyValues[$key] = [
						'value' => $value
					];
				}
				else {
					$this->error("Invalid Key");
				}
			}
			else {
				$this->error("Invalid Key");
			}
		}

		return true;
	}

	private function error($str)
	{
		if($this->raiseErrorLevel === 0) {
			return false;
		}

		if($this->raiseErrorLevel === 1) {
			trigger_error($str);
			return false;
		}

		if($this->raiseErrorLevel === 2) {
			throw new Exception($str);
		}
	}
}

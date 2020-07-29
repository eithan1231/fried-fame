<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\config.php
//
// ======================================


class config
{
	private $configVariables = null;

	public function __construct($path)
	{
		$this->configVariables = (require $path);
	}

	/**
	* Gets a confug variable
	*
	* @param string $key
	*		The keys whose value you want to retreive.
	*/
	public function get(string $key)
	{
		$key = strtolower($key);
		if(isset($this->configVariables[$key])) {
			return $this->configVariables[$key];
		}
		throw new Exception('Configuration key not found, '. ff_esc($key) .'.');
	}
}

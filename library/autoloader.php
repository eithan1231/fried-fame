<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\autoloader.php
//
// ======================================


// Initing, if it already has been initialized, nothing will happen.
autoloader::init();

/**
* PHP class to handle all auto-loading, and some manual loading.
*/
class autoloader
{
	private static $hasInit = false;

	public static function init()
	{
		if(self::$hasInit) {
			return;
		}
		self::$hasInit = true;
		spl_autoload_register('autoloader::load');
	}

	/**
	* Loads a library file
	*
	* @param string $className
	*		The name of the class we want to load.
	*/
	public static function load(string $className)
	{
		$className = str_replace('.', '', $className);
		$className = str_replace('_', '/', $className);
		return require_once FF_LIB_DIR ."/{$className}.php";
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\logger.php
//
// ======================================


class logger
{
	private static $cache = null;

	public static function getLoggers()
	{
		if(self::$cache !== null) {
			return self::$cache;
		}

		return self::$cache = [
			new logger_file(),
			new logger_none()
		];
	}

	public static function getLogger(string $name)
	{
		$name = strtolower($name);
		$loggers = self::getLoggers();
		foreach ($loggers as $logger) {
			if(strtolower($logger->getName()) == $name) {
				return $logger;
			}
		}
		return false;
	}
}

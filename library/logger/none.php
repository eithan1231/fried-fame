<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\logger\none.php
//
// ======================================


class logger_none implements logger_interface
{
	/**
	* Gets the name of the logging interface.
	*/
	public function getName()
	{
		return substr(__CLASS__, 7);
	}

	public function error(string $error, $parameter = null)
	{ }

	public function warning(string $warning, $parameter = null)
	{ }

	public function exception(Exception $ex, $parameter = null)
	{ }

	public function log(string $string, $parameter = null)
	{ }

	public function commit()
	{ }
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\logger\interface.php
//
// ======================================


interface logger_interface
{
	/**
	* Gets the name of the logging interface.
	*/
	public function getName();

	public function error(string $error, $parameter = null);

	public function warning(string $warning, $parameter = null);

	public function exception(Exception $ex, $parameter = null);

	public function log(string $string, $parameter = null);

	public function commit();
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\logger\file.php
//
// ======================================


class logger_file implements logger_interface
{
	private $eventChain = [];
	private $warningCount = 0;
	private $errorCount = 0;
	private $exceptionCount = 0;
	private $logCount = 0;

	public function __construct()
	{

	}

	/**
	* Gets the name of the logging interface.
	*/
	public function getName()
	{
		return substr(__CLASS__, 7);
	}

	public function error(string $error, $parameter = null)
	{
		$this->errorCount++;
		$this->eventChain[] = [
			'type' => __FUNCTION__,
			'time' => time(),
			'message' => $error,
			'parameters' => $parameter
		];
	}

	public function warning(string $warning, $parameter = null)
	{
		$this->warningCount++;
		$this->eventChain[] = [
			'type' => __FUNCTION__,
			'time' => time(),
			'message' => $warning,
			'parameters' => $parameter
		];
	}

	public function exception(Exception $ex, $parameter = null)
	{
		$this->exceptionCount++;
		$this->eventChain[] = [
			'type' => __FUNCTION__,
			'time' => time(),
			'message' => $ex,
			'parameters' => $parameter
		];
	}

	public function log(string $string, $parameter = null)
	{
		$this->logCount++;
		$this->eventChain[] = [
			'type' => __FUNCTION__,
			'time' => time(),
			'message' => $string,
			'parameters' => $parameter
		];
	}

	public function commit()
	{
		global $ff_request;

		if(count($this->eventChain) == 0) {
			return false;
		}

		// Generating filename
		$fileName = FF_LOG_DIR .'/'. FF_REQUEST_ID .'.log';

		// Generating log file
		$str = "====< Fried-Fame Logger >====\r\n";
		$str .= "$fileName\r\n";
		$str .= "{$ff_request->getPath()}\r\n";
		$str .= "{$ff_request->getQuery()}\r\n";
		$str .= "{$ff_request->getProtocol()} {$ff_request->getMethod()}\r\n";
		$str .= "{$ff_request->getIp()}\r\n";
		$str .= "Log Count {$this->logCount}; Warning Count {$this->warningCount}; Error Count {$this->errorCount}; Exception Count {$this->exceptionCount}\r\n\r\n";
		foreach ($this->eventChain as $link) {
			$str .= "===========================================================\r\n";
			// [date] type: message
			$str .= '['. date("F j, Y, g:i a", $link['time']) .'] '. $link['type'] .': '. $link['message']. "\r\n";
			if($link['parameters'] !== null && !empty($link['parameters'])) {
				$str .= var_export($link['parameters'], true);
			}
			$str .= "\r\n\r\n\r\n";
		}

		// Saving log
		if($f = fopen($fileName, 'w+')) {
			if(!fwrite($f, $str)) {
				return false;
			}
			fclose($f);
		}

		// Lets remove that string from memory.
		unset($str);
	}
}

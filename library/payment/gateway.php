<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\payment\gateway.php
//
// ======================================


class payment_gateway
{
	private static $getewayCache = null;

	public static function getGateways()
	{
		if(self::$getewayCache !== null) {
			return self::$getewayCache;
		}

		return self::$getewayCache = [
			new payment_gateway_paypal()
		];
	}

	public static function getGateway(string $name)
	{
		$name = strtolower($name);
		$gateways = self::getGateways();
		foreach($gateways as $gateway) {
			if(strtolower($gateway->getName()) == $name) {
				return $gateway;
			}
		}
		return null;
	}
}

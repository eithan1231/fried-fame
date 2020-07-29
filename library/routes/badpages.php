<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\badpages.php
//
// ======================================


class routes_badpages extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView()
	{
		ff_renderView(substr(__CLASS__, 7));
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/user/soapCaller.bs',
			'/phpmyadmin/scripts/setup.php',
			'/myadmin/scripts/setup.php',
			'/phpMyAdmin/scripts/setup.php',
			'/pma/scripts/setup.php',
			'/MyAdmin/scripts/setup.php',
			'/HNAP1/',
		];
	}

	/**
	* The name of the route.
	*/
	public function getName()
	{
		return substr(__CLASS__, 7);
	}

	/**
	* Whether or not this is a special class.
	*/
	public function isSpecial()
	{
		return false;
	}

	/**
	* Gets the supported http methods.
	*/
	public function getMethods()
	{
		return ['GET', 'HEAD'];
	}

	/**
	* The code to execute the route.
	*
	* @param array $parameters
	*		The parameters of the url.
	* @param request $request
	*		The request object.
	* @param response $response
	*		The response object.
	*/
	public function run(array $parameters, request &$request, response &$response)
	{
		// All these pages scrape websites for pages that are potentally vulnerable.
		// This route run's a dummy page, and will basically just send a false http
		// 200 header so as to confuse their bot.
		$response->setHttpStatus(200);
		$response->setHttpHeader('Content-Type', 'text/plain');
		$response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');
		$response->appendBody('You have been reported to the appropriate authorities for scaping with ill intent. Appropriate authorities may include ICANN, IANA, or your Internet Service Provider, depending on the severity and frequency of this ill intent.');

		$response->appendBody("\r\n");
		return true;
	}
}

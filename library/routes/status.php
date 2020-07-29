<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\status.php
//
// ======================================


/**
* Status page
*/
class routes_status extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView($parameters = [])
	{
		ff_renderView(substr(__CLASS__, 7), $parameters);
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/status',
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
		global $ff_config;

		$statusPage = ff_getStatusUrl();
		if(!$statusPage) {
			return false;// 404
		}

		$this->renderView([
			'status-page' => $statusPage
		]);
		return true;
	}
}

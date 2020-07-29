<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\windows\api\list.php
//
// ======================================


class routes_containers_windows_api_list extends route
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
      '/containers/windows/api/list',
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
		return ['POST'];
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
    global $ff_router, $ff_context;

		$response->json([
			'message' => 'okay',
			'servers' => [
				[
					'id' => 0,
					'hostname' => '1.nodes.lechr.com',
					'ip' => '127.0.0.1',
					'country' => 'au',
					'city' => 'Melbourne',
					'pptp' => false,
					'ovpn' => true
				], [
					'id' => 1,
					'hostname' => '2.nodes.lechr.com',
					'ip' => '127.0.0.2',
					'country' => 'au',
					'city' => 'Melbourne',
					'pptp' => false,
					'ovpn' => true
				]
			]
		]);

		return true;
	}
}

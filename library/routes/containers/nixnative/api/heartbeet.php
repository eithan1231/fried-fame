<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\nixnative\api\heartbeet.php
//
// ======================================


class routes_containers_nixnative_api_heartbeet extends route
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
      '/containers/nixnative/api/heartbeet',
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

		if(!($apiToken = $request->getHeader('x-api-token'))) {
			return $response->json([
				'message' => 'missing-token'
			]);
		}

		$autoapi = autoapi::getAutoAPIByToken($apiToken);
		if(!$autoapi || !$autoapi->isValid()) {
			return $response->json([
				'message' => 'bad-token'
			]);
		}

		$autoapi->heartbeat();

		return $response->json([
			'message' => 'done'
		]);

		return true;
	}
}

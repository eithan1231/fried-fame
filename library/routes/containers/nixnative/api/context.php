<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\nixnative\api\context.php
//
// ======================================


class routes_containers_nixnative_api_context extends route
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
      '/containers/nixnative/api/context',
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
    global $ff_router, $ff_context, $ff_config;

		$response->json([
			'autoapi-auth' => $ff_router->getPath('containers_nixnative_api_authenticate', [], [
				'mode' => 'host'
			]),

			'autoapi-connect' => $ff_router->getPath('containers_nixnative_api_connect', [], [
				'mode' => 'host'
			]),

			'autoapi-heartbeat' => $ff_router->getPath('containers_nixnative_api_heartbeet', [], [
				'mode' => 'host'
			]),

			'autoapi-list' => $ff_router->getPath('containers_nixnative_api_list', [], [
				'mode' => 'host'
			]),

			'autoapi-openvpnconfig' => $ff_router->getPath('containers_nixnative_api_openvpnconfig', [], [
				'mode' => 'host'
			]),

			'registration-url' => $ff_router->getPath('register', [], [
				'mode' => 'host'
			]),

			'project-name' => $ff_config->get('project-name'),
		]);

		return true;
	}
}

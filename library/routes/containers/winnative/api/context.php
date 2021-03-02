<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\winnative\api\context.php
//
// ======================================


class routes_containers_winnative_api_context extends route
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
      '/containers/winnative/api/context',
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
		// NOTE: Auto api isn't really needed here. They are all generic and not user
		// specific.

		$mostRecentPackage = packages::getPlatformMostRecent(packages::PLATFORM_WINDOWS);

		$response->json([
			'autoapi-auth' => $ff_router->getPath('containers_winnative_api_authenticate', [], [
				'mode' => 'host'
			]),

			'autoapi-connect' => $ff_router->getPath('containers_winnative_api_connect', [], [
				'mode' => 'host'
			]),

			'autoapi-list' => $ff_router->getPath('containers_winnative_api_list', [], [
				'mode' => 'host'
			]),

			'autoapi-openvpnconfig' => $ff_router->getPath('containers_winnative_api_openvpnconfig', [], [
				'mode' => 'host'
			]),

			'registration-url' => $ff_router->getPath('register', [], [
				'mode' => 'host'
			]),

			'download-page-url' => $ff_router->getPath('cp_install_windows', [], [
				'mode' => 'host'
			]),

			'project-name' => $ff_config->get('project-name'),

			'newest-version' => ($mostRecentPackage ? $mostRecentPackage['version'] : '0'),
		]);

		return true;
	}
}

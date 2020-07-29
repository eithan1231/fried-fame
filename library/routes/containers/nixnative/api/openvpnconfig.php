<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\nixnative\api\openvpnconfig.php
//
// ======================================


class routes_containers_nixnative_api_openvpnconfig extends route
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
      '/containers/nixnative/api/openvpn-config',
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

		$node_id = $request->get('node');
		if(!$node_id) {
			return $response->json([
				'message' => 'bad-param',
			]);
		}

		if(!($apiToken = $request->getHeader('x-api-token'))) {
			return $response->json([
				'message' => 'missing-token',
			]);
		}

		$autoapi = autoapi::getAutoAPIByToken($apiToken);
		if(!$autoapi || !$autoapi->isValid()) {
			return $response->json([
				'message' => 'bad-token',
			]);
		}

		$node = node::getNodeById($node_id);
		if(!$node) {
			return $response->json([
				'message' => 'bad-node',
			]);
		}

		if(!$node->isEnabled()) {
			return $response->json([
				'message' => 'node-disabled',
			]);
		}

		$response->setHttpHeader('content-type', 'application/ovpn-config');

		$response->appendBody($node->buildOpenVPNConfig());

		return true;
	}
}

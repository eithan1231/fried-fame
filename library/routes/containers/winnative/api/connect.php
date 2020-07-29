<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\winnative\api\connect.php
//
// ======================================


class routes_containers_winnative_api_connect extends route
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
      '/containers/winnative/api/connect',
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
		$node_auth_key = $request->get('node_auth');// Not required.
		if(!$node_id) {
			return $response->json([
				'message' => 'bad-param',
				'permit' => '0'
			]);
		}

		if(!($apiToken = $request->getHeader('x-api-token'))) {
			return $response->json([
				'message' => 'missing-token',
				'permit' => '0'
			]);
		}

		$autoapi = autoapi::getAutoAPIByToken($apiToken);
		if(!$autoapi || !$autoapi->isValid()) {
			return $response->json([
				'message' => 'bad-token',
				'permit' => '0'
			]);
		}

		$node = node::getNodeById($node_id);
		if(!$node) {
			return $response->json([
				'message' => 'bad-node',
				'permit' => '0'
			]);
		}

		if(!$node->isEnabled()) {
			return $response->json([
				'message' => 'node-disabled',
				'permit' => '0'
			]);
		}

		if($node->getCurrentLoad() > $node->getMaximumLoad()) {
			return $response->json([
				'message' => 'node-overload',
				'permit' => '0'
			]);
		}

		$user = $autoapi->getUser();

		$subscription = $user->getSubscription();
		if(!$subscription || !$subscription->valid) {
			return $response->json([
				'message' => 'bad-subscription',
				'permit' => '0'
			]);
		}

		if($node_auth_key !== false && $user->getNodeAuth() != $node_auth_key) {
			// NOTE: User can ignore auth key and it will be treated as though it's
			// valid. Which is fine, as this whole route is only for user verification
			// (clientside) and backend server tasks will have no interaction with it.
			return $response->json([
				'message' => 'bad-auth',
				'permit' => '0'
			]);
		}

		$plan = plan::getPlanById($subscription->subscrption_plan_id);
		if(!$plan) {
			return $response->json([
				'message' => 'bad-plan',
				'permit' => '0'
			]);
		}

		if($user->getConnectionCount() > $plan->getMaximumConcurrentConnections()) {
			return $response->json([
				'message' => 'exceeds-maximum-concurrent',
				'permit' => '0'
			]);
		}

		return $response->json([
			'message' => 'permit',
			'permit' => '1'
		]);

		return true;
	}
}

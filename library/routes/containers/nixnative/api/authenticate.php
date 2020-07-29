<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\nixnative\api\authenticate.php
//
// ======================================


class routes_containers_nixnative_api_authenticate extends route
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
      '/containers/nixnative/api/authenticate',
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

		// Limit to 10 trys for within 2 hours.
		$limit = 10;
		$limitTimespan = FF_HOUR * 2;
		if(postlimiter::exceedsRequestLimit('nixnative-auth', $request, $limit, $limitTimespan, $activeCount)) {
			return $response->json([
				'message' => 'try-again-later'
			]);
		}

		$username = $request->get('username');
		$password = $request->get('password');
		if(!$username || !$password) {
			return $response->json([
				'message' => 'missing-param'
			]);
		}

		$user = user::getUserByUsername($username);
		if(!$user) {
			postlimiter::insertRequest('nixnative-auth', $request);
			return $response->json([
				'message' => 'bad-username'
			]);
		}

		if(!$user->comparePassword($password)) {
			postlimiter::insertRequest('nixnative-auth', $request);
			return $response->json([
				'message' => 'bad-password'
			]);
		}

		$subscription = $user->getSubscription();
		if(!$subscription || !$subscription->valid) {
			return $response->json([
				'message' => 'no-subscription'
			]);
		}

		$autoapi = autoapi::createAutoAPI($user);
		if(!$autoapi) {
			return $response->json([
				'message' => 'audoapi-error'
			]);
		}

		return $response->json([
			'message' => 'okay',
			'token' => $autoapi->getToken(),
			'node-auth' => $user->getNodeAuth(),
			'user-id' => $user->getId()
		]);

		return true;
	}
}

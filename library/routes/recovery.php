<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\recovery.php
//
// ======================================


class routes_recovery extends route
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
			'/recovery',
			'/recovery/',
			'/profile/recovery',
			'/profile/recovery/',
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
		global $ff_router;
		if(
			($token = $request->get('token', request::METHOD_GET)) &&
			($userId = $request->get('user_id', request::METHOD_GET))
		) {
			if(is_numeric($userId) && is_string($token)) {
				$userId = intval($userId);
				$userObject = user::getUserById($userId);
				if($userObject) {
					$res = $userObject->resetPasswordViaRecovery($token);
					var_dump($res);
					if($res->success) {
						$response->redirect($ff_router->getPath('login'));
						return true;
					}
					else {
						$response->redirect($ff_router->getPath('recovery', [], [
							'query' => [
								'phrase' => $res->messageKey
							]
						]));
						return true;
					}
				}
			}
		}

		$this->renderView();
		return true;
	}
}

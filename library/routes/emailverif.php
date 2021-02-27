<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\emailverif.php
//
// ======================================


class routes_emailverif extends route
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
      '/email-verification/{token}/{user_id}'
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
    global $ff_router, $ff_context;

    $user_id = $parameters['user_id'];
    $token = $parameters['token'];

    if(!is_numeric($user_id)) {
			$response->redirect($ff_router->getPath('landing'));
      return true;
    }

    $user = user::getUserById($user_id);
    if(!$user) {
      // Failed to get user id.
			$response->redirect($ff_router->getPath('landing', [], [
				'query' => [
					'phrase' => 'misc-user-not-found'
				]
			]));
      return true;
    }

    $verif = $user->verifyEmail($token);
    if($verif->success) {
			$response->redirect($ff_router->getPath('cp_landing'));
    }
    else {
      $GLOBALS['ff_emailverifviewparam'] = $verif->messageKey;
      $this->renderView();
      unset($GLOBALS['ff_emailverifviewparam']);
    }

		return true;
	}
}

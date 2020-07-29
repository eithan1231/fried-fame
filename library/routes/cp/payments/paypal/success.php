<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\cp\payments\paypal\success.php
//
// ======================================


class routes_cp_payments_paypal_success extends route
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
      '/payments/gateways/paypal/success',
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
    global $ff_router, $ff_context, $ff_config;

		if(!$ff_config->get('paypal-enabled')) {
			// Not enabled. Let's treat the route as non-existant.
			return false;
		}

		if(!cp::standardProcedure($this->getName(), $request, $response)) {
      // Something went wrong with the standard procedure. This might mean it
      // needs additiona authentication, or something along those lines. Whatever
      // it is, means it has modified the $response object, and will redirect, or
      // print the appropriate page.
      return true;
    }

		$user = $ff_context->getSession()->getActiveLinkUser();
		if(!$user) {
			// Not logged in.
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$stateToken = $request->get('token');
		if(!$stateToken) {
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$state = payment_state::getStateByToken($stateToken);
		if(!$state) {
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		if($state->getUserId() != $user->getId()) {
			// Belongs to another user.
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$this->renderView([
			'state' => $state,
			'user' => $user
		]);

		return true;
	}
}

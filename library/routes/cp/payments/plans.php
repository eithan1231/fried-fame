<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\cp\payments\plans.php
//
// ======================================


class routes_cp_payments_plans extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView($p = [])
	{
		ff_renderView(substr(__CLASS__, 7), $p);
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/payments/plans'
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
		global $ff_context;

		if(!cp::standardProcedure($this->getName(), $request, $response)) {
      // Something went wrong with the standard procedure. This might mean it
      // needs additiona authentication, or something along those lines. Whatever
      // it is, means it has modified the $response object, and will redirect, or
      // print the appropriate page.
      return true;
    }

		$plans = plan::getPlans(3);
		if(!$plans) {
			return false;// chances of this happening are slim. (assuming admins arent stupid.)
		}

    $this->renderView([
			'plans' => $plans
		]);
		return true;
	}
}

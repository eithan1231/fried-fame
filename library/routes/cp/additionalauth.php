<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\cp\additionalauth.php
//
// ======================================


class routes_cp_additionalauth extends route
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
			'/control-panel/additionalauth'
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
    if(!cp::standardProcedure($this->getName(), $request, $response)) {
      // Something went wrong with the standard procedure. This might mean it
      // needs additiona authentication, or something along those lines. Whatever
      // it is, means it has modified the $response object, and will redirect, or
      // print the appropriate page.
      return true;
    }

    $this->renderView();
		return true;
	}
}

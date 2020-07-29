<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\cp\install\landing.php
//
// ======================================


class routes_cp_install_landing extends route
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
			'/control-panel/install/',
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
		global $ff_context, $ff_router;

		if(!cp::standardProcedure($this->getName(), $request, $response)) {
      // Something went wrong with the standard procedure. This might mean it
      // needs additiona authentication, or something along those lines. Whatever
      // it is, means it has modified the $response object, and will redirect, or
      // print the appropriate page.
      return true;
    }

		$ua = $ff_context->getUserAgent();
		switch (strtolower($ua->getPlatform())) {
			case 'android':
				$response->redirect($ff_router->getPath('cp_install_android'));
				break;

			case 'iphone':
			case 'ipad':
			case 'ipod':
				$response->redirect($ff_router->getPath('cp_install_ios'));
				break;

			case 'macintosh':
				$response->redirect($ff_router->getPath('cp_install_osx'));
				break;

			case 'linux':
				$response->redirect($ff_router->getPath('cp_install_linux'));
				break;

			default:
			case 'windows':
				$response->redirect($ff_router->getPath('cp_install_windows'));
				break;
		}

		return true;
	}
}

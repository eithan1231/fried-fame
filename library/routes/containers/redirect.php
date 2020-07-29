<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\containers\redirect.php
//
// ======================================


class routes_containers_redirect extends route
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
      '/containers/redirect',
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

    if(!containers::canAccess($this->getName(), $request, $response)) {
      $response->redirect($ff_router->getPath('landing'));
      return true;
    }

    $platform = '';
    if(!$platform = $request->getHeader('x-platform')) {
      $platform = $request->get('platform');
    }

    if(!$platform) {
      $response->setHttpStatus(401);
      $response->setHttpHeader('Content-type', 'text/plain');
      $response->appendBody('Unsupported platform');
      return true;
    }

    switch(strtolower($platform)) {
      case 'win32': {
        $response->redirect($ff_router->getPath('containers_windows_login'));
        break;
      }

      default: {
        $response->setHttpStatus(401);
        $response->setHttpHeader('Content-type', 'text/plain');
        $response->appendBody('Unsupported platform');
        break;
      }
    }

		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\internalapi\ffrpc\new.php
//
// ======================================


class routes_internalapi_ffrpc_new extends routes_internalapi_abstract
{
	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/internal-api/ffrpc/new',
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
	* Gets the supported http methods.
	*/
	public function getMethods()
	{
		return ['POST'];
	}

  protected function getPermit()
  {
    return internalapi::PERMIT_FFRPC;
  }

	/**
	* API Code Execution - Called by routes_internalapi_abstract
	* @param request $request
	*		The request object.
	* @param response $response
	*		The response object.
	*/
	protected function runAPI(request &$request, response &$response)
	{
    throw new Exception('Not Implemented');


		return true;
	}
}

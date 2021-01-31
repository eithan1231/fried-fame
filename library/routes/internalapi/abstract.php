<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\internalapi\abstract.php
//
// ======================================


abstract class routes_internalapi_abstract extends route
{
  abstract protected function runAPI(request &$request, response &$response);
  abstract protected function getPermit();

	/**
	* Whether or not this is a special class.
	*/
	public function isSpecial()
	{
		return false;
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
    $token = $request->getHeader('token');
    if(!$token) {
      $response->setHttpStatus(401);
      $response->appendBody('Authorization Token Missing');
      return true;
    }

    $internalAPI = internalapi::getInternalAPIByToken($token);
    if(!$internalAPI) {
      $response->setHttpStatus(401);
      $response->appendBody('Bad Authorization - Not Found');
      return true;
    }

    if($internalAPI->getToken() != $token) {
      $response->setHttpStatus(401);
      $response->appendBody('Bad Authorization - Bad Token');
      return true;
    }

    if($internalAPI->isExpired()) {
      $response->setHttpStatus(401);
      $response->appendBody('Bad Authorization - Expired token');
      return true;
    }

    if(!$internalAPI->getEnabled()) {
      $response->setHttpStatus(401);
      $response->appendBody('Bad Authorization - Expired token');
      return true;
    }

    if($internalAPI->getPermit() != $this->getPermit()) {
      $response->setHttpStatus(401);
      $response->appendBody('Bad Authorization - Invalid permit access');
      return true;
    }

    return $this->runAPI($request, $response);
	}
}

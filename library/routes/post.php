<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\post.php
//
// ======================================


class routes_post extends route
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
			'/post/{security_token}/{action}',
			'/post/{security_token}/{action}/'
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
		global $ff_context;
		$security_token = new security_token($ff_context->getSession());
		if($security_token->verify($parameters['security_token'])) {
			$post = new post($parameters['action']);
			if(!$post->run($request, $response)) {
				$response->setHttpStatus(404);
			}
		}
		else {
			$response->setHttpStatus(401);
		}
		return true;
	}
}

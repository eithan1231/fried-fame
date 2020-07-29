<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\manifest.php
//
// ======================================


class routes_manifest extends route
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
      '/manifest.webmanifest',
      '/manifest.json',
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
    global $ff_config, $ff_router;

    // See: https://developer.mozilla.org/en-US/docs/Web/Manifest

    $manifest = [
      'name' => $ff_config->get('project-name'),
      'short_name' => $ff_config->get('project-name-short'),
      'start_url' => $ff_router->getPath('landing', [], [
        'query' => [
          'ref' => 'manifest'
        ]
      ]),
      'orientation' => 'any',
      'theme_color' => '#f8f9fa',
      'display' => 'browser'
    ];

    $response->setHttpHeader('Content-type', 'application/manifest+json');
    $response->appendBody(json_encode($manifest, JSON_PRETTY_PRINT));
		return true;
	}
}

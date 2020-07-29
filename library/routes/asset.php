<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\asset.php
//
// ======================================


class routes_asset extends route
{
	public const PATH_REPLACEMENTS = [

		// Flags folder
		'flags_' => 'flags/',

		// Stars folder
		'stars_' => 'stars/',

		// logos directory
		'logos_' => 'logos/',
	];

	/**
	* Builds the route path (url), strictly the "path" part.
	*
	* NOTE: This will get the FIRST path. so if you're returning multiple paths on
	* the getPaths function, it will assume the first index.
	*
	* @param array $parameters
	*		Parameters for the route.
	* @return string The route path. IE: /this/is/a/sample
	*/
	public function buildRoute(array $parameters = [])
	{
		global $ff_config;

		$parameters['version'] = FF_VERSION;

		// Adding extension if it's not already added
		$assetTypeLength = strlen($parameters['extension']);
		if(substr($parameters['asset'], -$assetTypeLength) !== $parameters['extension']) {
			$parameters['asset'] .= '.'. $parameters['extension'];
		}

		if($ff_config->get('proxy-asset-bypass')) {

			// Generating asset path. usually this is done during the request, but
			// we have to handle it differently here.
			$assetPath = urlencode($parameters['asset']);
			foreach (self::PATH_REPLACEMENTS as $key => $value) {
				$assetPath = str_replace($key, $value, $assetPath);
			}

			// "/bypass-assets/{ext}/{asset}"
			return ff_concat(
				'/bypass-assets/',
				urlencode($parameters['extension']),
				'/',
				$assetPath
			);
		}
		else {
			return parent::buildRoute($parameters);
		}
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/asset/{float:version}/{string:extension}/{string:asset}',
			'/asset/{float:version}/{string:extension}',
			'/asset/{float:version}',
			'/asset',
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

	private function getAssetSubdirectory(string $extension)
	{
		$extension = str_replace(['.', '/'], '_', $extension);
		$directory = FF_LIB_DIR . "/assets/{$extension}";
		if(file_exists($directory)) {
			return $directory;
		}
		return false;
	}

	private function getETag($filename)
	{
		$p = [FF_VERSION, $filename];
		if(file_exists($filename)) {
			$p[] = filemtime($filename);
		}

		return hash(
			'crc32b',
			implode('-', $p)
		);
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
		global $ff_router;

		if(!isset($parameters['asset'])) {
			$response->setHttpStatus(404);
			$response->appendBody('Asset not found');
			return true;
		}

		// Checking version
		if($parameters['version'] !== FF_VERSION) {
			// Trying to load wrong version, reidrect to newest version. This should
			// ideally never be called.
			$response->redirect($ff_router->getPath(
				$this->getName(),
				array_merge($parameters, [
					'version' => FF_VERSION
				])
			));
			return true;
		}

		$assetsType = ff_stripNonAlphaNumeric($parameters['extension']);
		$assetDir = $this->getAssetSubdirectory($assetsType);
		if(!$assetDir) {
			$response->setHttpStatus(404);
			return true;
		}

		$contentType = ff_getExtensionMime($assetsType);
		if(!$contentType) {
			$response->setHttpStatus(404);
			return true;
		}
		$response->setHttpHeader('Content-Type', $contentType .'; charset='. FF_CHARSET);

		// Getting & cleaning asset & asset path, and getting current etag.
		$asset = ff_stripBad(strtolower($parameters['asset']));

		// Removing extension, if exists.
		$assetTypeLength = strlen($assetsType);
		if(substr($asset, -$assetTypeLength) === $assetsType) {
			$asset = substr($asset, 0, -$assetTypeLength);
		}

		// Basically a replacement thing, helps declutter folders
		foreach (self::PATH_REPLACEMENTS as $key => $value) {
			$asset = str_replace($key, $value, $asset);
			//$asset = $value . substr($asset, strlen($key));
		}

		$assetPath = "{$assetDir}/{$asset}.{$assetsType}";
		$etag = $this->getETag($assetPath);

		// Setting HTTP response headers.
		$response->setHttpHeader('Cache-Control', ($request->get('nocache') || ff_isDevelopment()
			? 'no-cache'
			: 'max-age='. strval(FF_YEAR * 5)
		));
		$response->setHttpHeader('ETag', "\"{$etag}\"");
		if(ff_isDevelopment()) {
			$response->setHttpHeader('X-Asset-Path', $assetPath);
		}

		// Check the request etag.
		if($ifNoneMatch = $request->getHeader('if-none-match')) {
			if($ifNoneMatch[0] === '"') {
				$ifNoneMatch = substr($ifNoneMatch, 1, -1);
			}

			if($ifNoneMatch === $etag) {
				$response->setHttpStatus(304);
				return true;
			}
			else if(ff_isDevelopment()) {
				// This is for debugging purposes.
				$response->setHttpHeader('X-If-None-Match', 'miss');
			}
		}

		if(file_exists($assetPath)) {
			if(!$response->sendFile($assetPath)) {
				$response->clearBody();
				$response->setHttpStatus(404);
				return true;
			}
		}
		else {
			$response->clearBody();
			$response->setHttpStatus(404);
			return true;
		}

		return true;
	}
}

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
	* list of extensions that can be minified. Used for the automated minified
	* check in production
	*/
	public const MINABLE_EXTENSIONS = [
		'js',
		'css'
	];

	/**
	* Overwriting route::buildRoute which will automaitcally generate a route
	*/
	public function buildRoute(array $parameters = [])
	{
		global $ff_config;

		// set version.
		$parameters['version'] = FF_VERSION;
		$parameters['asset'] = ff_stripExtension($parameters['asset']);

		// check for minified.
		if(
			!ff_isDevelopment() &&
			self::isMinifiableExtension($parameters['extension'])
		) {
			$minifiedAssetName = self::getMinifiedAssetName($parameters['asset']);
			$minifiedPath = self::getAssetPath($minifiedAssetName, $parameters['extension']);

			if($minifiedPath && file_exists($minifiedPath)) {
				$parameters['asset'] = $minifiedAssetName;
			}
		}

		// adding extension to asset
		$parameters['asset'] .= ff_concat('.', $parameters['extension']);

		// checking if we are bypassing proxy
		if($ff_config->get('proxy-asset-bypass')) {
			$assetPath = ff_stripBad($parameters['asset']);
			$assetPath = self::applyPathReplacements($assetPath);

			return ff_concat(
				'/bypass-assets/',
				ff_stripBad($parameters['extension']),
				'/',
				$assetPath
			);
		}

		// returning parameters for parent to build
		return parent::buildRoute($parameters);
	}

	public function getPaths()
	{
		return [
			'/asset/{float:version}/{string:extension}/{string:asset}',
			'/asset/{float:version}/{string:extension}',
			'/asset/{float:version}',
			'/asset',
		];
	}

	public function getName()
	{
		return substr(__CLASS__, 7);
	}

	public function isSpecial()
	{
		return false;
	}

	public function getMethods()
	{
		return ['GET', 'HEAD'];
	}

	public function run(array $parameters, request &$request, response &$response)
	{
		global $ff_router;

		// makign sure asset is set - if asset is set, everything else is set.
		if(
			!isset($parameters['asset']) ||
			$parameters['version'] !== FF_VERSION
		) {
			$response->setHttpStatus(404);
			return true;
		}

		$assetExtension = ff_stripNonAlphaNumeric($parameters['extension']);
		$assetPath = self::getAssetPath($parameters['asset'], $assetExtension);
		$etag = self::getETag($assetPath);// dislike this here, but we check if file exists - its fine.
		if(!$assetPath) {
			$response->setHttpStatus(404);
			return true;
		}

		$contentType = ff_getExtensionMime($assetExtension);
		if(!$contentType) {
			$response->setHttpStatus(404);
			return true;
		}

		$response->setHttpHeader('Content-Type', ff_concat($contentType, '; charset=', FF_CHARSET));
		$response->setHttpHeader('ETag', "\"{$etag}\"");

		// Cache-Control header. No cache is allowed for development, or if the
		// if the request parameter "nocache" is found
		if($request->get('nocache') || ff_isDevelopment()) {
			$response->setHttpHeader('Cache-Control', 'no-cache');
		}
		else {
			$response->setHttpHeader('Cache-Control', 'max-age='. strval(FF_YEAR * 5));
		}

		if(ff_isDevelopment()) {
			$response->setHttpHeader('X-Asset-Path', $assetPath);
		}

		// Checking request etag
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

		// sending response
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

	/**
	* Gets asset e-tag for cache validation system.
	*/
	private static function getETag($filename)
	{
		$options = [FF_VERSION, $filename];

		if(file_exists($filename)) {
			$options[] = filemtime($filename);
		}

		return hash(
			'crc32b',
			implode('-', $options)
		);
	}

	/**
	* Applies PATH_REPLACEMENTS to a string.
	*/
	private static function applyPathReplacements(string $str)
	{
		foreach (self::PATH_REPLACEMENTS as $key => $value) {
			$str = str_replace($key, $value, $str);
		}

		return $str;
	}

	/**
	* Is an extension able to be minified?
	*/
	private static function isMinifiableExtension(string $ext)
	{
		return in_array($ext, self::MINABLE_EXTENSIONS);
	}

	/**
	* Gets the minified version of an asset name
	*/
	private static function getMinifiedAssetName(string $assetName)
	{
		return "$assetName-min";
	}

	/**
	* Gets absolute path to a minified asset from its original name and extension
	*
	*/
	private static function getMinifiedPath(string $assetName, string $assetExtension = null)
	{
		// Assuming extension from the asset name
		if(!$assetExtension) {
			// getting extension from asset name
			$assetExtension = ff_getExtension($assetName);

			// now creating asset name.
			$assetName = ff_stripExtension($assetName);
			if(!$assetExtension) {
				throw new Exception('Asset extension is invalid');
			}
		}

		return self::getAssetPath(
			self::getMinifiedAssetName($assetName),
			$assetExtension
		);
	}

	/**
	* Gets the path of a non-minified asset.
	*/
	private static function getAssetPath(string $assetName, string $assetExtension = null)
	{
		// Assuming extension from the asset name
		if(!$assetExtension) {
			// getting extension from asset name
			$assetExtension = ff_getExtension($assetName);
			if(!$assetExtension) {
				throw new Exception('Asset extension is invalid');
			}
		}

		// stripping extension - required, as it re-adds extension at bottom.
		$assetName = ff_stripExtension($assetName);

		// getting name and directory in which asset is stored.
		$assetName = ff_stripBad(strtolower($assetName));
		$assetDirectory = self::getAssetDirectory($assetExtension);
		if(!$assetName || !$assetDirectory) {
			return false;
		}

		$assetName = self::applyPathReplacements($assetName);

		return "$assetDirectory/$assetName.$assetExtension";
	}

	/**
	* Gets the directory for specific asset type
	*/
	private static function getAssetDirectory(string $extension)
	{
		$extension = str_replace(['.', '/'], '_', $extension);
		$directory = FF_LIB_DIR . "/assets/{$extension}";
		if(file_exists($directory)) {
			return $directory;
		}
		return false;
	}
}

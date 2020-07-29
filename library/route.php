<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\route.php
//
// ======================================


/**
* The interface for routes.
*/
abstract class route
{
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
		$paths = $this->getPaths();
		if(!is_array($paths)) {
			throw new Exception("Unexpected type for variable \$paths");
		}

		// Getting the keys for the $paths variable.
		$keys = array_keys($paths);

		// Storing the first key's value in a variable.
		$path = $paths[$keys[0]];

		$pathExploded = explode('/', $path);
		$pathExplodedCount = count($pathExploded);
		$ret = '';
		foreach($pathExploded as $key => $val) {
			$valLen = strlen($val);
			$appandageParameter = $val;
			if(
				$valLen > 3 &&
				$val[0] === '{' &&
				$val[$valLen - 1] === '}'
			) {
				// Variable
				$k = substr($val, 1, $valLen - 2);
				if($i = strrpos($k, ':')) {// ignore type validation here.
					$k = substr($k, $i + 1);
				}
				$val = $parameters[strtolower($k)];
			}

			if(!is_string($val) && !is_numeric($val)) {
				throw new Exception("Unexpacted type");
			}

			$ret .= urlencode($val);
			if($key != $pathExplodedCount - 1) {
				$ret .= '/';
			}
		}
		return $ret;
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public abstract function getPaths();

	/**
	* The name of the route.
	*/
	public abstract function getName();

	/**
	* Whether or not this is a special class.
	*/
	public abstract function isSpecial();

	/**
	* Gets the supported http methods.
	*/
	public abstract function getMethods();

	/**
	* The code to execute the route.
	*
	* @param array $parameters
	*		The parameters of the url.
	* @param request $request
	*		The request object.
	* @param response $response
	*		The response object.
	* @return bool returning false will continue route search.
	*/
	public abstract function run(array $parameters, request &$request, response &$response);
}

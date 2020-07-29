<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\router.php
//
// ======================================


class router
{
	/**
	* The collection of routes.
	*/
	private $routeCollection = [];

	/**
	* Collection of special routes.
	*/
	private $specialRouteCollection = [];

	/**
	* The path that we will be working off of.
	*/
	private $path = '';

	private $forcedQueryParameters = [];

	/**
	* Work path variable. This is for, say, this project is located in a sub-directory
	* of root, we would need that so this new what directory level we are in. We
	* also need it for building absolute paths.
	* @var string
	*/
	private $workPath = '/';

	function __construct($workPath = '/')
	{
		global $ff_request, $ff_response;
		$this->path = $ff_request->getPath();

		$workPathLen = strlen($workPath);
		if($workPathLen > 0 && $workPath[$workPathLen - 1] == '/') {
			// Removing end slash.
			$workPath = substr($workPath, 0, --$workPathLen);
		}

		// Setting work path, AFTER we have configured it (removed last forward slash)
		$this->workPath = $workPath;

		if(
			strlen($this->path) > $workPathLen &&
			substr($this->path, 0, $workPathLen) == $workPath
		) {
			$this->path = substr($this->path, $workPathLen);
		}
	}

	/**
	* Registers the route.
	*
	* @param route $route
	*		The route we want to register with this instance of the router class.
	*/
	public function register(route $route)
	{
		$this->routeCollection[] = $route;
	}

	/**
	* Registers a special route.
	*
	* @param string $specialCommand
	*		The name of the command what runs this. Example: 404, would run un unknown
	*		pages.
	* @param route $route
	*		The route we are registering
	*/
	public function registerSpecial(string $specialCommand, route $route)
	{
		$specialCommand = strtolower($specialCommand);
		$this->specialRouteCollection[$specialCommand][] = $route;
	}

	/**
	* Sets a forced query parameter.
	*
	* @param string $name
	*		Name of the parameter
	* @param string $value
	*		Value of the parameter
	*
	*/
	public function setForcedQueryParameter(string $name, string $value)
	{
		$this->forcedQueryParameters["$name"] = $value;
	}

	/**
	* Gets the path to a route.
	*
	* @param string $name
	*		Name of the route you want the path of.
	* @param array $parameters
	*		The parameters for the path.
	* @param array $options
	*		Options for the path.
	*		Possbile options: (key:value)
	*			mode: absolute, host, relative
	*			hostname: %
	*			query: array|string
	*			hash: string
	*			allowForceParam: bool (whether or not we allow the forced parameters)
	*			scheme: http, https
	*/
	public function getPath(string $name, array $parameters = [], array $options = [])
	{
		global $ff_config, $ff_request;

		$getOption = function($key, $default = false) use(&$options) {
			if(isset($options[$key])) {
				return $options[$key];
			}
			return $default;
		};

		// Default options...
		$mode = strtolower($getOption('mode', 'absolute'));
		$query = $getOption('query', null);
		$hash = $getOption('hash', null);
		$allowForceParam = $getOption('allowForceParam', true);
		$scheme = $getOption('scheme', $ff_config->get('secure-server') ? 'https' : 'http');
		$hostname = $ff_request->getHeader('host');
		if(!$hostname) {
			$hostname = $ff_config->get('default-hostname');
		}
		$hostname = $getOption('hostname', $hostname);

		// Setting forced query parameters
		if($allowForceParam && count($this->forcedQueryParameters) > 0) {
			$queryParsed = '';
			if($query === null) {
				$query = null;
			}
			else if(is_string($query)) {
				$queryParsed = [];
				parse_str($query, $queryParsed);
			}
			else {
				$queryParsed = $query;
			}

			if(!$queryParsed) {
				$queryParsed = [];
			}

			foreach($this->forcedQueryParameters as $key => $value) {
				$queryParsed[$key] = $value;
			}

			$query = http_build_query($queryParsed);
		}
		if(is_array($query)) {
			$query = http_build_query($query);
		}

		// setting $name to lowercase.
		$name = strtolower($name);

		foreach($this->routeCollection as $route) {
			if(strtolower($route->getName()) == $name) {
				switch ($mode) {
					case 'relative': {
						return substr($route->buildRoute($parameters), 1) . ($query ? $query : '') . ($hash ? $hash : '');
					}

					default:
					case 'host':
					case 'absolute': {
						$path = $route->buildRoute($parameters);
						if($path[0] == '/') {
							$path = substr($path, 1);
						}
						$path = "{$this->workPath}/{$path}";

						if($mode === 'host') {
							if(strlen($scheme) > 0) {
								$scheme .= ':';
							}
							$path = "$scheme//{$hostname}{$path}";
						}

						if($query) {
							$path .= "?{$query}";
						}

						if($hash) {
							$path .= "#{$hash}";
						}

						return $path;
					}
				}
			}
		}

		throw new Exception('Route not found, please enter a valid $name');
	}

	/**
	* Returns a collection of routes.
	*/
	public function getRouteCollection()
	{
		return $this->routeCollection;
	}

	/**
	* Returns a route object by the name of it.
	*
	* @param string $name
	*		The name of the route we want to return
	*/
	public function getRoute(string $name)
	{
		$name = strtolower($name);
		foreach ($this->routeCollection as $route) {
			if($route->getName() == $name) {
				return $route;
			}
		}
		return false;
	}

	/**
	* Goes through and runs whatever route possible.
	*/
	public function run()
	{
		global $ff_request, $ff_response;

		$requestPathComponents = explode('/', $this->path);
		$requestPathComponentsCount = count($requestPathComponents);

		// Boolean indicating whether or not the route is complete.
		$complete = false;
		foreach($this->routeCollection as $route) {
			if($route->isSpecial()) {
				// We dont run special routes here.
				continue;
			}

			if(!in_array($ff_request->getMethod(), $route->getMethods())) {
				// Unintended HTTP method
				continue;
			}

			$routePaths = $route->getPaths();
			if(!$routePaths) {
				// Invalid paths
				continue;
			}

			foreach($routePaths as $path) {
				$pathComponents = explode('/', $path);
				$pathComponentsCount = count($pathComponents);
				$parameters = [];// If this path matches, parameters we use to call.

				if($requestPathComponentsCount !== $pathComponentsCount) {
					// different amount of slashes, so therefore cannot match.
					continue;
				}

				// Going through and checking/comparing path components. If everything
				// checks-out, we will run the route.
				for($i = 0; $i < $pathComponentsCount && !$complete; $i++) {
					$requestPathComponent = $requestPathComponents[$i];
					$requestPathComponentLength = strlen($requestPathComponent);
					$pathComponent = $pathComponents[$i];
					$pathComponentLength = strlen($pathComponent);

					if(
						$pathComponentLength > 2 &&
						$pathComponent[0] === '{' &&
						$pathComponent[$pathComponentLength - 1] == '}'
					) {// Dynamic variable path.
						$varName = substr($pathComponent, 1, $pathComponentLength - 2);// what is within curly brackets
						$varValue = $requestPathComponent;// Variable value. (what user requests)

						// Checking for explicit type requirements
						if($typePosition = strpos($varName, ':')) {
							$varType = substr($varName, 0, $typePosition);// Getting string type
							$varName = substr($varName, $typePosition + 1);// updating the actual name.

							switch (strtolower($varType)) {
								case 'int':
								case 'integer': {
									if(is_numeric($varValue)) {
										$varValue = intval($varValue);
									}
									else {
										$varValue = null;
									}
									break;
								}

								case 'float':
								case 'double': {
									if(is_numeric($varValue)) {
										$varValue = floatval($varValue);
									}
									else {
										$varValue = null;
									}
									break;
								}

								case 'bool':
								case 'boolean': {
									$varValue = ff_stringToBool($varValue);
									break;
								}

								default: {
									$varValue = urldecode(strval($varValue));
									break;
								}
							}
						}

						if($varValue === null) {
							// if the value is null, it means the type is invalid.
							break;
						}

						$parameters[strtolower($varName)] = $varValue;
					}
					else if($pathComponent !== $requestPathComponent) {
						// Isnt a variable, or a match, so continue with other paths.
						break;
					}

					// Setting complete variable
					$complete = ($i == $pathComponentsCount - 1);
				}

				// If complete, run...
				if($complete) {
					if($route->run(
						$parameters,
						$ff_request,
						$ff_response
					)) {
						// Returned true, therefore no need to continue search. Break.
						break;
					}
					else {
						// Returned false, or null, so continue search for another route.
						$complete = false;
					}
				}
			}// path enumeration end

			if($complete) {
				break;
			}
		}// route collection enumeration end

		if(!$complete) {
			// Didnt find route... Run 404.
			$this->runSpecial('404');
		}
	}

	public function runSpecial(string $command, $parameters = [])
	{
		global $ff_request, $ff_response;
		$command = strtolower($command);

		if(isset($this->specialRouteCollection[$command])) {
			foreach($this->specialRouteCollection[$command] as $route) {
				if(!$route->isSpecial()) {
					continue;
				}
				$route->run($parameters, $ff_request, $ff_response);
			}
		}
		else {
			$ff_response->setHttpStatus(404);
			$ff_response->appendBody("This page cannot be found.\n(Default 404 handler)");
		}
	}

	public function getWorkPath()
	{
		return $this->workPath;
	}
}

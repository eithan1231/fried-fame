<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\functions.php
//
// ======================================


/**
* Checks if a character is a blank space (<SP>, <CR>, <LF>, TAB)
*
* @param string $char
*		The character we want to check is a white-space.
*/
function ff_isWhiteSpace(string $char)
{
	return (
		$char[0] === ' ' ||
		$char[0] === "\r" ||
		$char[0] === "\n" ||
		$char[0] === "\t"
	);
}

/**
* Strips white spaces from the beginning and endinf og a string.
*
* @param string $string
*		The string wen want to strip.
*/
function ff_stripEndingBlanks(string $string)
{
	$stringLen = strlen($string);

	while($stringLen > 0 && ff_isWhiteSpace($string[0])) {
		$string = substr($string, 1);
		$stringLen--;
	}

	while($stringLen > 0 && ff_isWhiteSpace($string[$stringLen - 1])) {
		$string = substr($string, 0, --$stringLen);
	}

	return $string;
}

/**
* Gets the status descriptor of a http status.
*
* @param int $status
*		The status we want the descriptor of.
*/
function ff_getHttpStatusDescriptor(int $status)
{
	switch ($status) {
    case 100: return 'Continue';
    case 101: return 'Switching Protocols';
    case 200: return 'OK';
    case 201: return 'Created';
    case 202: return 'Accepted';
    case 203: return 'Non-Authoritative Information';
    case 204: return 'No Content';
    case 205: return 'Reset Content';
    case 206: return 'Partial Content';
    case 300: return 'Multiple Choices';
    case 301: return 'Moved Permanently';
    case 302: return 'Moved Temporarily';
    case 303: return 'See Other';
    case 304: return 'Not Modified';
    case 305: return 'Use Proxy';
    case 400: return 'Bad Request';
    case 401: return 'Unauthorized';
    case 402: return 'Payment Required';
    case 403: return 'Forbidden';
    case 404: return 'Not Found';
    case 405: return 'Method Not Allowed';
    case 406: return 'Not Acceptable';
    case 407: return 'Proxy Authentication Required';
    case 408: return 'Request Time-out';
    case 409: return 'Conflict';
    case 410: return 'Gone';
    case 411: return 'Length Required';
    case 412: return 'Precondition Failed';
    case 413: return 'Request Entity Too Large';
    case 414: return 'Request-URI Too Large';
    case 415: return 'Unsupported Media Type';
    case 500: return 'Internal Server Error';
    case 501: return 'Not Implemented';
    case 502: return 'Bad Gateway';
    case 503: return 'Service Unavailable';
    case 504: return 'Gateway Time-out';
    case 505: return 'HTTP Version not supported';
    default: return 'Default';
	}
	return 'Unknown';
}

/**
* Exact same as htmlspecialchars, just shorter.
*/
function ff_esc(string $string, int $flags = ENT_HTML5, string $encoding = FF_CHARSET, bool $double_encode = TRUE)
{
	return htmlspecialchars($string, $flags, $encoding, $double_encode);
}

/**
* Strips the extension of a filename
*
* @param string $str
*		The filename whoese extension we want to remove
*/
function ff_stripExtension($str)
{
	if($pos = strrpos($str, '.')) {
		return substr($str, 0, $pos);
	}
	return $pos;
}

/**
* Gets the filename (strips directories, and extension)
*
* @param string $str
*		The string whose directories/extension you want to strip.
*/
function ff_filename(string $str)
{
	return ff_stripExtension(basename($str));
}

/**
* Converts a string to a boolean
*
* @param string $s
*		The string we want to convert.
* @return bool whether or not $s is a bool.
*/
function ff_stringToBool($s)
{
	if($s === true || $s === false) {
		return $s;
	}

	$s = strtolower($s);
	return (
		$s === '1' ||
		$s === 'true' ||
		$s === 'ok' ||
		$s === 'yes'
	);
}

/**
* Builds a cookie header for the HTTP response.
*
* @param string $name
*		Name of the cookie
* @param string $value
*		Value of the cookie
* @param string $options
*		Options for the cookie.
*/
function ff_buildCookieHeader(string $name, string $value, array $options = [])
{
	if(!isset($options['path'])) {
		$options['path'] = '/';
	}

	$cookieHeader = "Set-Cookie: " . urlencode($name) .'='. urlencode($value);
	foreach ($options as $optionName => $optionValue) {
		$optionNameAltered = $optionName;
		$optionValueAltered = $optionValue;

		switch (strtolower($optionName)) {
			case 'expires': {
				if(is_numeric($optionValueAltered)) {
					$optionValueAltered = gmdate('D, d M Y H:i:s T', intval($optionValueAltered));
				}
			}

			default: break;
		}

		$cookieHeader .= '; ';
		$cookieHeader .= urlencode($optionNameAltered);
		if(strlen($optionValueAltered) > 0) {
			$cookieHeader .= '=';
			$cookieHeader .= $optionValueAltered;
		}
	}

	return $cookieHeader;
}

/**
* Parses the HTTP request header, Accept-Language.
*
* @param string $acceptLanguage
*		The header to-be parsed.
* @return array The array of accept languages, in order of weight (high to low).
*/
function ff_parseAcceptLanguage(string $acceptLanguage)
{
	global $ff_config;
	$parsed = [];
	$exploded = explode(',', $acceptLanguage);
	if(count($exploded) === 0) {
		$exploded = [$acceptLanguage];
	}
	foreach($exploded as $lang) {
		$langWeight = 1.0;
		$langCode = '';
		$langCountryCode = '';
		if($weightPos = strpos($lang, ';q=')) {
			$langWeight = floatval(substr($lang, $weightPos + 4));
			$lang = substr($lang, $weightPos);
		}

		$localExplode = explode('-', $lang);
		if(count($localExplode) === 0) {
			$localExplode = [$lang];
		}

		if(isset($localExplode[0])) {
			$langCode = $localExplode[0];
		}
		if(isset($localExplode[1])) {
			$langCountryCode = $localExplode[1];
		}

		$parsed[] = [
			'weight' => $langWeight,
			'language_code' => $langCode,
			'languace_country_code' => $langCountryCode
		];
	}

	// TODO: order by weight.
	return $parsed;
}

/**
* Gets an extensions mime content type.
*
* @param string $extension
*		The extension whose content-type you want.
*/
function ff_getExtensionMime(string $extension)
{
	switch (strtolower($extension)) {
		case 'js': {
			return 'text/javascript';
		}

		case 'css': {
			return 'text/css';
		}

		case 'svg': {
			return 'image/svg+xml';
		}

		case 'htm':
		case 'html': {
			return 'text/html';
		}

		case 'aac': {
			return 'audio/aac';
		}

		case 'bin': {
			return 'application/octet-stream';
		}

		case 'bmp': {
			return 'image/bmp';
		}

		case 'bz': {
			return 'application/x-bzip';
		}

		case 'csv': {
			return 'text/csv';
		}

		case 'doc': {
			return 'application/msword';
		}

		case 'docx': {
			return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
		}

		case 'ico': {
			return 'image/x-icon';
		}

		case 'jpeg':
		case 'jpg': {
			return 'image/jpeg';
		}

		case 'json': {
			return 'application/json';
		}

		case 'mpeg': {
			return 'video/mpeg';
		}

		case 'png': {
			return 'image/png';
		}

		case 'pdf': {
			return 'application/pdf';
		}

		case 'rar': {
			return 'application/x-rar-compressed';
		}

		case 'xml': {
			return 'application/xml';
		}

		case '7z': {
			return 'application/x-7z-compressed';
		}

		case 'zip': {
			return 'application/zip';
		}

		case 'webp': {
			return 'image/webp';
		}

		default: return false;
	}
}

/**
* This function lowercases the domain-part. Since the 'local-part' of the
* email is case sensitive, we leave that as is, but the domain part, that is
* case insensntive.
*
* @param string $email
*		Email to clean.
*/
function ff_cleanEmail(string &$email)
{
	if($x = strpos($email, '@')) {
		$email = substr($email, 0, $x) . strtolower(substr($email, $x));
	}
}

/**
* Function response object (Used for functions that need data un return.)
*
* @param bool $success
*		Whether or not we want to return the object success state as true or false.
* @param array|object $data
*		The return data 'key'
* @param array $messageKey
*		The phrase key for a message.
*/
function ff_return(bool $success, $data = [], string $messageKey = '')
{
	if(!is_object($data) && !is_array($data) && !is_string($data)) {
		throw new Exception('Unexpected type');
	}

	if(is_string($data)) {
		if(!empty($messageKey)) {
			throw new Exception('$messageKey was set by $data');
		}
		$messageKey = $data;
		$data = [];
	}

	if(empty($messageKey)) {
		$messageKey = 'default';
	}
	return (object) [
		'success' => $success,
		'data' => $data,
		'messageKey' => $messageKey,

		// I changed it from message to messageKey, so lets keep this here incase i
		// missed something
		'message' => $messageKey,

		// Get the phrase body linked with $messageKey
		'getMessageBody' => function(array $parameters = []) use($messageKey) {
			global $ff_context;
			return $ff_context->getSession()->getLanguage()->getPhrase($parameters);
		}
	];
}

/**
* Renders a few by it's name (name is basically filename)
*
* @param string $viewName
*		Name of the view.
* @param array $parameters
*		Parameters for the view. NOTE: These will be set in a global variable,
*		but removed as soon as done with.
*/
function ff_renderView(string $viewName, array $parameters = [])
{
	$GLOBALS['ff_viewParameters'] = $parameters;
	$tmp = 'views/'. str_replace('_', '/', $viewName);
	include FF_LIB_DIR ."/{$tmp}.php";
	unset($GLOBALS['ff_viewParameters']);
}

/**
* Gets parameters for a view, automatically set with ff_renderView.
*/
function ff_getViewParameters()
{
	return (isset($GLOBALS['ff_viewParameters'])
		? $GLOBALS['ff_viewParameters']
		: []
	);
}

/**
* Gets url to status page
*/
function ff_getStatusUrl()
{
	global $ff_config, $ff_request;
	$statusPage = $ff_config->get('status-page');
	return str_replace('{domain}', $ff_request->getHeader('host'), $statusPage);
}

/**
* Renders a post redirect view
*
* @param string $redirectTo
*		The location we want to redirect to
* @param string $message
*		Message to prompt user on redirect screen
*/
function ff_postRedirectView(string $redirectTo, string $message = '')
{
	$GLOBALS['ff_redirecto'] = $redirectTo;
	$GLOBALS['ff_redirectmsg'] = $message;
	ff_renderView('special_postredirect');
	unset($GLOBALS['ff_redirecto'], $GLOBALS['ff_redirectmsg']);
}

/**
* Builds a cache key
*
* @param string $name
*		Name of the cache key
* @param array $parameters
*		Parameters for the key. Keys in this parameter ($parameters) will be ignored.
*/
function ff_cacheKey(string $name, $parameters = [])
{
	if(!is_array($parameters)) {
		if(!is_numeric($parameters) && !is_string($parameters)) {
			throw new Exception('unexpected type');
		}
		$parameters = [$parameters];
	}

	return strtolower($name) .'::'. implode(';', $parameters);
}

/**
* Converts parameter one into a readable size. (For example, 1024, would be 1kb.)
*
* Credits:
*		http://php.net/manual/en/function.memory-get-usage.php#96280
*
* @param int $bytes
*		The integer you want to convert.
*/
function ff_getSizeAsVisual(int $bytes)
{
	$unit = array('b','kb','mb','gb','tb','pb');
	return round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . $unit[$i];
}

/**
* Checks if we are in development mode.
* @return bool
*/
function ff_isDevelopment()
{
	global $ff_config;
	if(!$ff_config->get('development')) {
		return false;
	}
	return ff_stringToBool($ff_config->get('development'));
}

/**
* Gets and creates a new smtpClient instances, and auto configures credentials. .
*/
function ff_newSmtp()
{
	global $ff_config;

	// Getting smtp object
	$smtpClient = new smtpclient(
		$ff_config->get('smtp-hostname'),
		intval($ff_config->get('smtp-port')),
		$ff_config->get('smtp-security')
	);

	// Setting credentials.
	$smtpClient->setUser(
		$ff_config->get('smtp-username'),
		$ff_config->get('smtp-username'),
		$ff_config->get('smtp-password')
	);

	return $smtpClient;
}

/**
* Strips all non-alpha numeric characters from parameter one.
*
* @param string $string
*		The string we want to strip characters from.
*/
function ff_stripNonAlphaNumeric(string $string)
{
	return preg_replace("/[^A-Za-z0-9 ]/", '', $string);
}

/**
* Strips everything but alpha-numeric, underscore, and hyphen.
*
* @param string $string
*		The string we want to strip characters from.
*/
function ff_stripBad(string $string)
{
	return preg_replace('/[^\w-]/', '', $string);
}

/**
* This is a SEO helper, it will return "$id-$name", but will explode names at
* spaces, then will lowercase it, and url encode it, then implode it with a
* hyphen at each space.
*
* @param int $id
*		The id
* @param string $name
*		The subect, or any form of keywords.
*/
function ff_idAndSubject(int $id, string $subject)
{
	if(strlen($subject) > 32) {
		$subject = substr($subject, 0, 32);
	}
	$subject = strtolower($subject);
	$subject = explode(
		' ',
		str_replace([' ', ',', '.', '/', '_', '+', '='], ' ', $subject)
	);
	$subject = array_map('strtolower', $subject);
	$subject = array_map('ff_stripNonAlphaNumeric', $subject);
	$subject = array_map('urlencode', $subject);
	if(count($subject) == 0) {
		return $id;
	}
	return $id .'-'. implode('-', $subject);
}

/**
* This extracts the $id parameter from the 'ff_idAndSubject' function.
*
* @param string $merged
*		The results of 'ff_idAndSubject'
*/
function ff_getIdFromMergedIdAndSubject(string $merged)
{
	$p = strpos($merged, '-');
	if(!$p) {
		return intval($merged);
	}
	return intval(substr($merged, 0, $p));
}

/**
* Returns a random index in an array.
*
* @param array $in
*		Array whose random value we want to return.
*/
function ff_randomArrayIndex(array $in)
{
	return $in[mt_rand(0, count($in) - 1)];
}

/**
* Gets tacktrace as a formatted string.
* @return string
*/
function ff_stackTrace()
{
	$st = array_reverse(debug_backtrace());
	$ret = '';
	foreach ($st as $k => $v) {
		if($k === count($st) - 1) {
			continue;
		}
		if(
			!isset($v['file']) ||
			!isset($v['line']) ||
			!isset($v['class']) ||
			!isset($v['type']) ||
			!isset($v['function']) ||
			!isset($v['args'])
		) {
			$ret .= "[{$k}]: Unknown\r\n";
			continue;
		}
		$ret .= "[{$k}]: {$v['file']} ({$v['line']}) {$v['class']}{$v['type']}{$v['function']}(". implode(', ', array_map(function(&$x) {
			if(is_numeric($x)) {
				return $x;
			}

			if(is_bool($x)) {
				return $x ? 'true' : 'false';
			}

			if(is_null($x)) {
				return 'NULL';
			}

			if(is_object($x)) {
				return '[Object]';
			}

			if(is_resource($x)) {
				return '[Resource]';
			}

			if(is_array($x)) {
				return '[Array]';
			}

			if(is_string($x)) {
				if(strlen($x) > 15) {
					$x = substr($x, 0, 15) . '...';
				}
				return '"'. addslashes($x) .'"';
			}
		}, $v['args'])) . ")\r\n";
	}
	return $ret;
}

/**
* Censors a name.
* @param string $name
*		Name to be censored.
* @return string Censored name.
*/
function ff_censorName(string $name)
{
	$nameLen = strlen($name);
	$fill = '';
	for($i = 0; $i < $nameLen - 2; $i++) {
		$fill .= '*';
	}
	return $name[0] . $fill . $name[$nameLen - 1];
}

/**
* Builds a page title.
* @param string $phrase
*		Name of the title phrase.
* @param null|language $language
*		A language object, or a null object. Null will fetch language linked with
*		session
* @return string
*/
function ff_buildTitle(string $phrase, $language = null, $parameters = [])
{
	global $ff_context, $ff_config;

	if(!$language) {
		$language = $ff_context->getLanguage();
	}

	return $language->getPhrase("{$phrase}", array_merge([
		'project' => $ff_config->get('project-name')
	], $parameters));
}

/**
* Generates a User-Agent for HTTP requests.
* @return string
*/
function ff_buildUserAgent()
{
	global $ff_config, $ff_router;

	// {shortname}/{version} (+{landing-page})
	return $ff_config->get('project-name-short') .'/'. FF_VERSION .' (+'. $ff_router->getPath('landing', [], [
		'mode' => 'host'
	]) .')';
}

/**
* Claculates percission for user statistics
*/
function ff_calculatePrecision($duration)
{
	if($duration > FF_YEAR) {
		return FF_MONTH;
	}
	else if($duration > FF_MONTH * 6) {
		return FF_WEEK * 2;
	}
	else if($duration > FF_MONTH * 3) {
		return FF_WEEK;
	}
	else if($duration > FF_MONTH) {
		return FF_DAY;
	}
	else if($duration > FF_WEEK * 2) {
		return FF_DAY * 2;
	}
	else if($duration > FF_WEEK) {
		return FF_DAY;
	}
	else if($duration < FF_DAY) {
		return FF_HOUR;
	}
	return FF_DAY;
}

function ff_getPhraseCacheKey($phraseName, $language)
{
	return ff_cacheKey('phrase', [$phraseName, $language]);
}

/**
* Sometimes hard to concatinate fairly large strings... this is to make the code
* slightly more readable.
* all parameters are concatted.
*/
function ff_concat()
{
	$ret = '';
	foreach (func_get_args() as $value) {
		$ret .= $value;
	}
	return $ret;
}

/**
* Checks if a string is completely Alpha-Numeric, also including underscores
* and hyphens
* @param string $s
*/
function ff_isAlphanumeric($s)
{
	return preg_match('/[^a-z_\-0-9]/i', $s);
}

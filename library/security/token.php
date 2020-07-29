<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\security\token.php
//
// ======================================


class security_token
{
	/**
	* The duration a security token is active, before expiring.
	* this is in seconds
	*/
	const TOKEN_DURATION = FF_HOUR * 12;// 12hr

	/**
	* Amount of time token can have left in the reuse period
	*/
	const TOKEN_REUSE_DURATION = FF_HOUR * 3;

	private $session = null;

	/**
	* Just storing a generated token, to prevent generating multiple.
	*/
	private $generatedToken = null;

	public function __construct(session $session)
	{
		$this->session = $session;
	}

	/**
	* Checks if a token exists.
	*
	* @param string $token
	*		The token which we wanna see exists.
	* @return boolean whether token exists.
	*/
	private function tokenExists(string $token)
	{
		global $ff_sql;

		if(strlen($token) > 32) {
			// Limited to 256 characters, so it cannot exist.
			return false;
		}

		$res = $ff_sql->query_fetch("
			SELECT
				count(1) as cnt
			FROM `security_tokens`
			WHERE
				`token` = ". $ff_sql->quote($token) ."
		", ['cnt' => 'int']);

		if(!$res) {
			throw new Exception('Unexpected return value');
		}

		return $res['cnt'] > 0;
	}

	/**
	* Generates a security token, and links it with the session.
	* @return string|bool On success, returns token. On failure, return false
	*/
	public function getToken()
	{
		global $ff_sql, $ff_request, $ff_context;

		if($this->generatedToken) {
			// Already cached a token, lets return it.
			return $this->generatedToken;
		}

		// Generating parameters.
		$session_id = $this->session->getId();
		$ip = $ff_request->getIp();
		$expiry = FF_TIME + self::TOKEN_DURATION;

		// Attempt to get a security token that is still valid, this will save resources.
		if($existingToken = $ff_sql->query_fetch("
			SELECT
				`id`,
				`token`,
				`date`,
				`expiry`
			FROM `security_tokens`
			WHERE
				`session_id` = ". $ff_sql->quote($session_id) ." AND
				`ip` = ". $ff_sql->quote($ip) ." AND
				`expiry` > ". $ff_sql->quote(FF_TIME - self::TOKEN_REUSE_DURATION) ."
		", [
			'id' => 'int',
			'date' => 'int',
			'expiry' => 'int',
		])) {
			if($existingToken['expiry'] > FF_TIME) {
				// getting issues where it would return wrong results, and idfk what
				// i've done to provoke it.
				return $this->generatedToken = $existingToken['token'];
			}
		}

		// Generating token.
		$token = '';
		while(self::tokenExists($token = cryptography::randomString(32)));

		// Inserting query
		$res = $ff_sql->query("
			INSERT INTO `security_tokens`
			(`id`, `session_id`, `ip`, `date`, `expiry`, `token`)
			VALUES (
				NULL,
				". $ff_sql->quote($session_id) .",
				". $ff_sql->quote($ip) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($expiry) .",
				". $ff_sql->quote($token) ."
			)
		");

		// Checking result
		if(!$res) {
			// Failed to insert, dunno why.
			return false;
		}

		// returning result
		return $this->generatedToken = $token;
	}

	/**
	* Checks whether or not a token is valid.
	*
	* @param string $token
	*		The token we wish to check is valid.
	*/
	public function verify(string $token)
	{
		global $ff_sql, $ff_request;

		if($this->session === null) {
			throw new Exception('Invalid Session');
		}

		$result = $ff_sql->query_fetch("
			SELECT
				`id`,
				`session_id`,
				`ip`,
				`date`,
				`expiry`,
				`token`
			FROM `security_tokens`
			WHERE
				`token` = ". $ff_sql->quote($token) ." AND
				`session_id` = ". $ff_sql->quote($this->session->getId()) ."
		", [
			'id' => 'int',
			'session_id' => 'int',
			'date' => 'int',
			'expiry' => 'int'
		]);

		if(!$result) {
			return false;
		}

		if($result['expiry'] < FF_TIME) {
			// expired
			return false;
		}

		if($result['ip'] !== $ff_request->getIp()) {
			// IP missmatch
			return false;
		}

		return true;
	}
}

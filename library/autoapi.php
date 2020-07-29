<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\autoapi.php
//
// ======================================


/**
* This is the api used between the clients (android, windows, etc).
*/
class autoapi
{
	private static $cache = [];

	private $id = 0;
	private $user_id = 0;
	private $token = '';
	private $date = 0;
	private $last_heartbeet = 0;
	private $enabled = false;

	/**
	* How long an api token is considered value from last heartbeet.
	*/
	const TOKEN_VALID_DURATION = FF_HOUR;

	/**
	* Links the instance of the AutoAPI with a id,
	*
	* @param int $id
	*		ID which we want to link with this class instance.
	*/
	public function linkById(int $id)
	{
		global $ff_sql;

		$res = $ff_sql->fetch("
			SELECT
				`id`,
				`user_id`,
				`token`,
				`date`,
				`last_heartbeet`,
				`enabled`
			FROM
				`autoapi`
			WHERE
				`id` = ". $ff_sql->quote($id) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'last_heartbeet' => 'int',
			'enabled' => 'bool'
		]);

		if(!$res) {
			return false;
		}

		foreach ($res as $key => $value) {
			$this->$key = $value;
		}

		return true;
	}

	/**
	* Links the instance of the AutoAPI with a token,
	*
	* @param string $token
	*		Token which we want to link with this class instance.
	*/
	public function linkByToken(string $token)
	{
		global $ff_sql;

		$res = $ff_sql->fetch("
			SELECT
				`id`,
				`user_id`,
				`token`,
				`date`,
				`last_heartbeet`,
				`enabled`
			FROM
				`autoapi`
			WHERE
				`token` = ". $ff_sql->quote($token) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'last_heartbeet' => 'int',
			'enabled' => 'bool'
		]);

		if(!$res) {
			return false;
		}

		foreach ($res as $key => $value) {
			$this->$key = $value;
		}

		return true;
	}

	/**
	* Gets autoapi instance by id
	*
	* @param int $id
	*		ID which we want to link with this class instance.
	*/
	public static function getAutoAPIById(int $id)
	{
		if(isset(self::$cache[__FUNCTION__]) && isset(self::$cache[__FUNCTION__][$id])) {
			return self::$cache[__FUNCTION__][$id];
		}

		$autoapi = new autoapi();
		if(!$autoapi->linkById($id)) {
			return false;
		}

		return self::$cache[__FUNCTION__][$id] = $autoapi;
	}

	/**
	* Gets autoapi instance by a token
	*
	* @param string $token
	*		token which we want to link with this class instance.
	*/
	public static function getAutoAPIByToken(string $token)
	{
		if(isset(self::$cache[__FUNCTION__]) && isset(self::$cache[__FUNCTION__][$token])) {
			return self::$cache[__FUNCTION__][$token];
		}

		$autoapi = new autoapi();
		if(!$autoapi->linkByToken($token)) {
			return false;
		}

		return self::$cache[__FUNCTION__][$token] = $autoapi;
	}

	/**
	* Creates a new auto API
	*
	* @param user $user
	*		The user who's linked with the autoapi
	*/
	public static function createAutoAPI(user $user)
	{
		global $ff_sql;

		$token = cryptography::randomString(32);
		// TODO: Check if token exists.

		$ff_sql->query("
			INSERT INTO `autoapi`
			(`id`, `user_id`, `token`, `date`, `last_heartbeet`, `enabled`)
			VALUES (
				NULL,
				". $ff_sql->quote($user->getId()) .",
				". $ff_sql->quote($token) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote(FF_TIME) .",
				1
			)
		");

		return autoapi::getAutoAPIById($ff_sql->getLastInsertId());
	}

	/**
	* Runs the heartbeet
	*/
	public function heartbeat()
	{
		global $ff_sql;

		if(!$this->isEnabled()) {
			// Instance is disabled, so updating heartbeet is useless.
			return false;
		}

		$last_heartbeet = FF_TIME;
		$ff_sql->query("
			UPDATE `autoapi`
			SET last_heartbeet = ". $ff_sql->quote(FF_TIME) ."
			WHERE id = ". $ff_sql->quote($this->getId()) ."
		");

		return true;
	}

	/**
	* Returns of the instance of the autoapi is enabled
	*/
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	* Refer to getEnabled
	*/
	public function isEnabled()
	{
		return $this->getEnabled();
	}

	/**
	* Gets the token linked with this class
	*/
	public function getToken()
	{
		return $this->token;
	}

	/**
	* Gets the unique identifier for this class
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Gets the user object of the linked user
	* @return user
	*/
	public function getUser()
	{
		return user::getUserById($this->getUserId());
	}

	/**
	* Gets the users' id linked with the autoapi instance
	*/
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	* Gets the date which this was created.
	*/
	public function getDate()
	{
		return $this->date;
	}

	/**
	* Refer to getDate.
	*/
	public function getCreationDate()
	{
		return $this->getDate();
	}

	/**
	* Whether this api token is valid.
	*/
	public function isValid()
	{
		return (
			$this->getEnabled() &&
			($this->last_heartbeet + self::TOKEN_VALID_DURATION > FF_TIME)
		);
	}
}

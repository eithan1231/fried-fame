<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\notification.php
//
// ======================================


class notification
{
	private $id;
	private $user_id;
	private $date;
	private $expiry;
	private $hidden;
	private $phrase_name;
	private $phrase_parameters;
	private $route_name;
	private $route_parameters;

	public function linkById(int $id)
	{
		global $ff_sql;

		$result = $ff_sql->query_fetch("
			SELECT
				*
			FROM
				`notifications`
			WHERE
				`id` = ". $ff_sql->quote($id) ."
		");

		if(!$result) {
			return false;
		}

		return $this->linkByTableRowData($result);
	}

	public function linkByTableRowData(array $row)
	{
		foreach ($row as $key => $value) {
			$this->$key = $value;
		}

		$this->route_parameters = json_decode($this->route_parameters, true);
		$this->phrase_parameters = json_decode($this->phrase_parameters, true);
	}

	public static function push(
		user $user,
		string $phraseName,
		array $phraseParameters = [],
		int $duration = FF_MONTH,
		string $routeName = '',
		array $routeParamters = []
	) {
		global $ff_sql;

		$ff_sql->query("
			INSERT INTO `notifications`
			(`id`, `user_id`, `date`, `expiry`, `phrase_name`, `phrase_parameters`, `hidden`, `route_name`, `route_parameters`)
			VALUES (
				NULL,
				". $ff_sql->quote($user->getId()) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote(FF_TIME + $duration) .",
				". $ff_sql->quote($phraseName) .",
				". $ff_sql->quote(json_encode($phraseParameters)) .",
				0,
				". $ff_sql->quote($routeName) .",
				". $ff_sql->quote(json_encode($routeParamters)) ."
			)
		");

		return true;
	}

	public static function getUserNotifications(user $user, bool $showHidden = false)
	{
		global $ff_sql;

		$queryData = $ff_sql->query_fetch_all("
			SELECT
				`id`,
				`user_id`,
				`date`,
				`expiry`,
				`phrase_name`,
				`phrase_parameters`,
				`hidden`,
				`route_name`,
				`route_parameters`
			FROM
				`notifications`
			WHERE
				`user_id` = ". $ff_sql->quote($user->getId()) ." AND
				`expiry` > ". $ff_sql->quote(FF_TIME) ."
				". ($showHidden ? '' : "AND `hidden` = 0") ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'expiry' => 'int',
			'hidden' => 'bool',
		]);

		if(!$queryData) {
			return false;
		}

		return array_map(function($row) use(&$queryData) {
			$notif = new notification();
			$notif->linkByTableRowData($row);
			return $notif;
		}, $queryData);
	}

	public function getId()
	{
		return $this->id;
	}

	public function getUserId()
	{
		return $this->user_id;
	}

	public function getUser()
	{
		return user::getUserById($this->getUserId());
	}

	public function getDate()
	{
		return $this->date;
	}

	public function getExpiry()
	{
		return $this->expiry;
	}

	public function getHidden()
	{
		return $this->hidden;
	}

	public function getPhraseName()
	{
		return $this->phrase_name;
	}

	public function getPhraseParameters()
	{
		return $this->phrase_parameters;
	}

	public function getRouteName()
	{
		return $this->route_name;
	}

	public function getRouteParameters()
	{
		return $this->route_parameters;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\feedback.php
//
// ======================================


class feedback
{
	/**
	* Creates a feedback entry
	*
	* @param user $user
	*		This is the user creating the entry.
	* @param string $body
	*		Body of the feedback entry
	* @return ff_return Whether or not it was successful
	*/
	public static function newFeedback(user $user, string $body)
	{
		global $ff_sql;

		if(strlen($body) > 65535) {
			return ff_return(false, [], 'misc-too-long');
		}

		if(strlen($body) <= 0) {
			return ff_return(false, [], 'misc-too-short');
		}

		if(!$user->getGroup()->can('feedback')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		$ff_sql->query("
			INSERT INTO `general_feedback`
			(`id`, `user_id`, `date`, `body`)
			VALUES (
				NULL,
				". $ff_sql->quote($user->getId()) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($body) ."
			)
		");

		return ff_return(true);
	}

	public static function getFeedback(int $index = 0, $limit = 128)
	{
		global $ff_sql;

		// TODO: remove count(1) its slow.
		$count = $ff_sql->fetch("select count(1) as `cnt` from `general_feedback`")['cnt'];

		return $ff_sql->fetch_all("
			SELECT `id`, `user_id`, `date`, `body`
			FROM `general_feedback`
			WHERE
				id <= ". $ff_sql->quote($count - $index) ."
			ORDER BY `id` DESC
			LIMIT ". $ff_sql->quote($limit) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int'
		]);
	}
}

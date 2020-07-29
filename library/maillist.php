<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\maillist.php
//
// ======================================


class maillist
{
	/**
	* Susbcribes an address to the mailing list.
	* @param string $address
	*		Address to be subscribed
	* @param int|user $user
	*		User who's being subscribed to list. This is optional, and user can be
	*		subscribed multiple times.
	*		NOTE: This can be 3 states, an integer (user id), user object, or a null.
	*		integer will link the user with a user id. Object with the id of the object,
	*		null will set to default (0). DEFAULT CAN BE OVERWRITTEN!
	*		WARNING: Be careful with this. Make sure user is authorized, and stuff. If
	*		address is already on list, but there is no assigned user id, this will
	*		overwrite it!
	* @return ff_return
	*/
	public static function subscribe(string $address, $user = null)
	{
		global $ff_sql;

		if(strlen($address) > 254) {
			return ff_return(false, [], 'misc-address-too-long');
		}

		ff_CleanEmail($address);

		if(is_int($user)) {
			$uid = $user;
		}
		else if ($user === null) {
			$uid = 0;
		}
		else {
			$uid = $user->getId();
		}

		$existingMetadata = $ff_sql->fetch("
			SELECT
				`id`,
				`user_id`,
				`email`,
				`enabled`
			FROM
				`mailing_list`
			WHERE
				`email` = ". $ff_sql->quote($address) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'enabled' => 'bool',
		]);

		if($existingMetadata) {
			if($existingMetadata['user_id'] === 0 && $uid) {
				$ff_sql->query("
					UPDATE `mailing_list`
					SET `user_id` = ". $ff_sql->quote($uid) ."
					WHERE `id` = ". $ff_sql->quote($existingMetadata['id']) ."
				");
			}

			return ff_return(true);
		}
		else {
			$ff_sql->query("
				INSERT INTO `mailing_list`
				(`id`, `user_id`, `email`, `removal_token`, `enabled`)
				VALUES (
					NULL,
					". $ff_sql->quote($uid) .",
					". $ff_sql->quote($address) .",
					". $ff_sql->quote(cryptography::randomString(16)) .",
					1
				)
			");
		}

		return ff_return(true);
	}

	/**
	* Ubsubscribes an email from the mailing list.
	* @param string $address
	*		Address which we wanna unsubscribe
	* @param string $token
	*		Token to remove subscription
	* @return bool
	*/
	public static function unsubscribe(string $address, string $token)
	{
		global $ff_sql;

		if(strlen($address) > 254) {
			return false;
		}

		ff_CleanEmail($address);

		$ff_sql->query("
			UPDATE `mailing_list`
			SET `enabled` = 0
			WHERE
				`address` = ". $ff_sql->quote($address) ." AND
				`removal_token` = ". $ff_sql->quote($token) ."
		");

		return true;
	}
}

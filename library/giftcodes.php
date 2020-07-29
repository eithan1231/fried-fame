<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\giftcodes.php
//
// ======================================


class giftcodes
{
	const EXPIRATION = FF_YEAR;// lasts a year before user is unable to activate it.

	/**
	* Redeems a giftcode
	*
	* @param user $user
	*		The user who is redeeming the code
	* @param string $code
	*		The code which is being redeemed
	*/
	public static function redeem(user $user, string $code)
	{
		global $ff_sql;

		$code = strtolower($code);
		if(strlen($code) > 32) {
			return ff_return(false, [], 'misc-giftcode-not-found');
		}

		if(!$user->getGroup()->can('purchase')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		$dbEntry = $ff_sql->fetch("
			SELECT
				`id`,
				`plan_id`,
				`enabled`,
				`date`,
				`expiry`,
				`creator_user_id`,
				`user_id`,
				`activation_message`,
				`code`
			FROM
				`giftcodes`
			WHERE
				`code` = ". $ff_sql->quote($code) ."
		", [
			'id' => 'int',
			'plan_id' => 'int',
			'enabled' => 'bool',
			'date' => 'int',
			'expiry' => 'int',
			'creator_user_id' => 'int',
			'user_id' => 'int'
		]);

		if($dbEntry) {
			if(!$dbEntry['enabled']) {
				return ff_return(false, [], 'misc-giftcode-disabled');
			}

			if($dbEntry['expiry'] < FF_TIME) {
				return ff_return(false, [], 'misc-giftcode-expired');
			}

			// Disabling it for future use.
			$ff_sql->query("
				UPDATE `giftcodes`
				SET `enabled` = 0, `user_id` = ". $ff_sql->quote($user->getId()) ."
				WHERE id = ". $ff_sql->quote($dbEntry['id']) ."
			");

			$plan = plan::getPlanById($dbEntry['plan_id']);
			if(!$plan) {
				// Should never happen. A plan must have been removed, but ideally this
				// should never happen.
				throw new Exception('Plan not found');
			}

			if($user->giveSubscription($plan)) {
				return ff_return(true, [
					'plan' => $dbEntry['plan_id'],
					'activation_message' => $dbEntry['activation_message']
				], 'misc-success');
			}
			else {
				throw new Exception('failed to give subscription');
			}
		}
		else {
			return ff_return(false, [], 'misc-giftcode-not-found');
		}
	}

	/**
	* Creates new giftcode(s)
	*
	* @param user $user
	*		The user who's creating the giftcodes.
	* @param plan $plan
	*		the plan that user will receive
	* @param string $message
	*		Welcome message (after activation, redeemee is prompted with).
	* @param int $count
	*		The amount of codes we want to create.
	*/
	public static function create(user $user, plan $plan, string $message, int $count = 1)
	{
		global $ff_sql;

		if(!$user->getGroup()->can('mod_giftcode')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		if($count > 500 || $count < 1) {
			return ff_return(false, [], 'misc-goftcode-generate-too-much');
		}

		// Generating codes
		$codes = [];
		for($i = 0; $i < $count; $i++) {
			$code = cryptography::randomString(4, true) .'-'. cryptography::randomString(4, true) .'-'. cryptography::randomString(4, true) .'-'. cryptography::randomString(5, true);
			$code = strtolower($code);

			// TODO: Make this more optimised.. this is horrific.
			if($ff_sql->fetch("
				SELECT count(1) as cnt
				FROM `giftcodes`
				WHERE `code` = ". $ff_sql->quote($code) ."
			")['cnt'] == 0) {
				$codes[] = $code;
			}
		}

		// Constructing parameters for inserting the query
		$insertValues = [];
		foreach ($codes as $code) {
			// TODO: cleanup.. this is ew.
			$insertValues[] = '(
				NULL,
				'. $ff_sql->quote($plan->getId()) .',
				1,
				'. $ff_sql->quote(FF_TIME) .',
				'. $ff_sql->quote(FF_TIME + self::EXPIRATION) .',
				'. $ff_sql->quote($user->getId()) .',
				0,
				'. $ff_sql->quote($message) .',
				'. $ff_sql->quote($code) .'
			)';
		}

		$res = $ff_sql->query("
			INSERT INTO `giftcodes`
			(id, plan_id, enabled, date, expiry, creator_user_id, user_id, activation_message, code)
			VALUES ". implode(',', $insertValues) ."
		");

		return ff_return ($res !== false, [
			'codes' => $codes
		]);
	}
}

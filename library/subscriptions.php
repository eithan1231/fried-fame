<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\subscriptions.php
//
// ======================================


class subscriptions
{
	/**
	* Guves a user a subscription, or updates existing
	*
	* @param user $user
	*		receivee
	* @param plan $plan
	*		The place user is to receive
	* @param bool $sendMail
	*		Whether or not we send new subscription email.
	* @return bool True on success, otherwise false.
	*/
  public static function giveSubscription(user $user, plan $plan, bool $sendMail = true)
	{
		global $ff_sql;
		$currentSubscription = self::getSubscription($user);

    $res = false;

		if(!$currentSubscription) {
			$res = $ff_sql->query("
				INSERT INTO `user_subscriptions`
				(`id`, `user_id`, `subscrption_plan_id`, `date`, `expiry`, `enabled`)
				VALUES (
					NULL,
					". $ff_sql->quote($user->getId()) .",
					". $ff_sql->quote($plan->getId()) .",
					". $ff_sql->quote(FF_TIME) .",
					". $ff_sql->quote(FF_TIME + $plan->getDuration()) .",
					1
				)
			") !== false;
		}
		else {
			// Generating expiry
			$expiry = ($currentSubscription->expiry < FF_TIME
				? FF_TIME + $plan->getDuration()// Expired existing subscription
			 	: $currentSubscription->expiry + $plan->getDuration()// Valid & existing subscription
			);

			$res = $ff_sql->query("
				UPDATE `user_subscriptions`
				SET
					`subscrption_plan_id` = ". $ff_sql->quote($plan->getId()) .",
					`expiry` = ". $ff_sql->quote($expiry) ."
				WHERE
					`id` = ". $ff_sql->quote($currentSubscription->id) ." AND
					`user_id` = ". $ff_sql->quote($user->getId()) ."
			") !== false;
		}

    // Push subscription notification
		if($sendMail) {
			$emailSender = new email_newsubscription($user, $plan);
			$emailSender->setRecipient($user->getEmail());
			$emailSender->send();
		}

    return $res;
  }

	/**
	* Disables a users active subscription
	*
	* @param user $user
	*		User whose subscription we are disabling
	*/
	public static function disableSubscription(user $user)
	{
		global $ff_sql;
		$currentSubscription = self::getSubscription($user);
		if(!$currentSubscription) {
			// Not active subscription. Let's just act as though it was disabled.
			return true;
		}

		if(!$currentSubscription->enabled) {
			// Disabled already.. lol.
			return true;
		}

		return $ff_sql->query("
			UPDATE `user_subscriptions`
			SET `enabled` = 0
			WHERE `user_id` = ". $ff_sql->quote($user->getId()) ."
		") !== false;
	}

	/**
	* Gets subscription information linked with a user.
	*
	* @param user $user
	*		The users' whose subscription information you want.
	* @return null|object On success, returns object, on failure, null.
	*/
	public static function getSubscription(user $user)
	{
		global $ff_sql;
		$subscriptionData = $ff_sql->query_fetch("
			SELECT
				`id`,
				`subscrption_plan_id`,
				`date`,
				`expiry`,
				`enabled`
			FROM `user_subscriptions`
			WHERE
				`user_id` = ". $ff_sql->quote($user->getId()) ."
		", [
			'id' => 'int',
			'subscrption_plan_id' => 'int',
			'date' => 'int',
			'expiry' => 'int',
			'enabled' => 'bool',
		]);

		if(!$subscriptionData) {
			return null;
		}

		$subscriptionData['valid'] = $subscriptionData['enabled'] && $subscriptionData['expiry'] > FF_TIME;

		return (object)$subscriptionData;
	}

	/**
	* Subtracts a subscription plan from a users profile.
	*
	* @param user $user
	*		User whose subscription we are removing
	* @param plan $plan
	*		The plan which we are removing
	*/
	public static function removeSubscription(user $user, plan $plan)
	{
		global $ff_sql;

		$currentSubscription = self::getSubscription($user);
		if(!$currentSubscription) {
			// Not active subscription. Let's just act as though it was removed.
			return true;
		}

		// NOTE: Doesn't matter if the expiry has passed. If user gets a new
		// subscription, it will reset the expirating date before appending the
		// duration.
		$expiry = $currentSubscription->expiry - $plan->getDuration();

		return $ff_sql->query("
			UPDATE `user_subscriptions`
			SET `expiry` = ". $ff_sql->quote($expiry) ."
			WHERE `user_id` = ". $ff_sql->quote($user->getId()) ."
		") !== false;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\payment\state.php
//
// ======================================


/**
* Class stores information about a payment state, this is so IPN's for payment
* responses can be handled easier.
*/
class payment_state
{
	private static $cache = [];

	private $id = 0;
	private $token = '';
	private $user_id = 0;
	private $paymentMethod = '';
	private $subscriptionPlanId = 0;
	private $couponId = 0;
	private $affiliateId = 0;
	private $hasCompleted = false;

	const TOKEN_SIZE = 32;

	/**
	* Links a state with by an id.
	*
	* @param int $id
	*		Id you want to link with the current object.
	*/
	public function linkById(int $id)
	{
		global $ff_sql;

		$result = $ff_sql->query_fetch("
			SELECT
				id,
				token,
				user_id,
				payment_method,
				subscription_plan_id,
				coupon_id,
				affiliate_id,
				has_completed
			FROM
				payment_state
			WHERE
				id = ". $ff_sql->quote($id) ."
			LIMIT 1
		", [
			'id' => 'int',
			'subscription_plan_id' => 'int',
			'coupon_id' => 'int',
			'has_completed' => 'bool',
		]);

		if(!$result) {
			return false;
		}

		$this->id = $result['id'];
		$this->token = $result['token'];
		$this->user_id = $result['user_id'];
		$this->paymentMethod = $result['payment_method'];
		$this->subscriptionPlanId = $result['subscription_plan_id'];
		$this->couponId = $result['coupon_id'];
		$this->affiliateId = $result['affiliate_id'];
		$this->hasCompleted = $result['has_completed'];

		return true;
	}

	/**
	* Gets a state by an id. This also allows for cache, thus poterntially
	* increasing performace
	*
	* @param int $id
	*		The ID you want to get
	*/
	public static function getStateById(int $id)
	{
		if(isset(self::$cache[$id])) {
			return self::$cache[$id];
		}

		$state = new payment_state();
		if(!$state->linkById($id)) {
			return false;
		}

		return self::$cache[$id] = $state;
	}

	/**
	* Gets a state object by an identifying token
	*
	* @param int $id
	*		Id you want to link with the current object.
	*/
	public static function getStateByToken(string $token)
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT `id`
			FROM `payment_state`
			WHERE `token` = ". $ff_sql->quote($token) ."
			LIMIT 1
		");

		if(!$res) {
			return false;
		}

		return self::getStateById($res['id']);
	}

	/**
	* Creates a new state (or updates existing)
	*/
	public static function newState(payment_gateway_abstract $gateway, user $user, plan $plan, $coupon = null, $affiliate = null)
	{
		global $ff_sql;

		if($coupon != null && get_class($coupon) != 'payment_coupon') {
			return false;
		}

		if($affiliate != null && get_class($affiliate) != 'payment_affiliate') {
			return false;
		}

		$couponId = ($coupon === null ? 0 : $coupon->getId());
		$affiliateId = ($affiliate === null ? 0 : $affiliate->getId());

		$token = cryptography::randomString(self::TOKEN_SIZE);

		$res = $ff_sql->query("
			INSERT INTO payment_state
			(id, token, user_id, payment_method, subscription_plan_id, coupon_id, affiliate_id, has_completed)
			VALUES (
				NULL,
				". $ff_sql->quote($token) .",
				". $ff_sql->quote($user->getId()) .",
				". $ff_sql->quote($gateway->getName()) .",
				". $ff_sql->quote($plan->getId()) .",
				". $ff_sql->quote($couponId) .",
				". $ff_sql->quote($affiliateId) .",
				0
			)
		");

		if(!$res) {
			return false;
		}

		$lastId = $ff_sql->getLastInsertId();
		if(!$lastId) {
			return false;
		}

		return self::getStateById($lastId);
	}

	/**
	* Gets the identifier linked with this state
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Gets the token linked with this state
	*/
	public function getToken()
	{
		return $this->token;
	}

	/**
	* Alias for hasCompleted method
	*/
	public function getHasCompleted()
	{
		return $this->hasCompleted;
	}

	/**
	* Returns boolean as to whether this has been completed
	*/
	public function hasCompleted()
	{
		return $this->getHasCompleted();
	}

	/**
	* Gets the payment gateway used for this payment state (is string)
	*/
	public function getGateway()
	{
		return $this->paymentMethod;
	}

	/**
	* Gets the payment gateway used for this payment state (is string)
	*/
	public function getPaymentGateway()
	{
		return $this->getGateway();
	}

	/**
	* Gets ID of coupon linked with this state
	*/
	public function getCouponId()
	{
		if($this->couponId === 0) {
			return false;
		}
		return $this->couponId;
	}

	/**
	* Gets coupon object
	*/
	public function getCoupon()
	{
		if($this->couponId === 0) {
			return false;
		}
		return payment_coupon::getCouponById($this->couponId);
	}

	/**
	* Gets id of the subscription plan linked with state
	*/
	public function getPlanId()
	{
		return $this->subscriptionPlanId;
	}

	/**
	* Gets the subscription plan linekd with state
	*/
	public function getPlan()
	{
		return plan::getPlanById($this->getPlanId());
	}

	/**
	* Gets the affiliate id linked with state
	*/
	public function getAffiliateId()
	{
		if(!$this->affiliateId) {
			return false;
		}
		return $this->affiliateId;
	}

	/**
	* Gets the affiliate object linked with state
	*/
	public function getAffiliate()
	{
		if(!$this->affiliateId) {
			return false;
		}
		throw new Exception('Affiliate not implemented');
	}

	/**
	* Gets the user's id associated with state
	*/
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	* Gets user object assoctiated with state
	*/
	public function getUser()
	{
		return user::getUserById($this->user_id);
	}

	/**
	* Marks state as having been completed
	*/
	public function markCompleted()
	{
		global $ff_sql;

		$hasCompleted = true;
		$ff_sql->query("
			UPDATE
				`payment_state`
			SET
				`has_completed` = 1
			WHERE
				id = ". $ff_sql->quote($this->id) ."
		");

		return true;
	}
}

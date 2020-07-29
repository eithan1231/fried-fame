<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\payment.php
//
// ======================================


/**
* Class for lodging payments.
*/
class payment
{
	private static $cache = [];
	const STATUS_SUCCESSFUL = 1;// Successful
	const STATUS_CHARGEBACK_PROCESS = 2;// Chargeback in progress
	const STATUS_CHARGEBACKED = 3;// Was chargebacked
	const STATUS_FAILED = 4;// Failed
	const STATUS_REFUNDED = 5;// Payment was refunded
	const STATUS_BAD_INPUT = 6;// Was provided with bad information.
	const STATUS_REVERSED = 7;// Chargeback attempted, but was reversed

	private $id = null;
	private $status = null;
	private $user_id = null;
	private $date = null;
	private $affiliate_id = null;
	private $coupon_id = null;
	private $payments_state_id = null;
	private $currency = null;
	private $gross = null;
	private $fee = null;
	private $gateway_name = null;
	private $gateway_info = null;

	/**
	* Links payment object with the id
	*
	* @param int $id
	*		ID of the payment we are querying
	*/
	public function linkById(int $id)
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT
				*
			FROM `payments`
			WHERE
				`id` = ". $ff_sql->quote($id) ."
			GROUP BY `id`
			ORDER BY `date` DESC
		", [
			'id' => 'int',
			'status' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'affiliate_id' => 'int',
			'coupon_id' => 'int',
			'payments_state_id' => 'int',
			'gross' => 'float',
			'fee' => 'float',
		]);

		if(!$res) {
			return false;
		}

		foreach($res as $key => $value) {
			$this->$key = $value;
		}

		$this->gateway_info = json_decode($this->gateway_info, true);

		return true;
	}

	/**
	* Links payment object with the state id
	*
	* @param int $state
	*		ID of the state we are querying
	*/
	public function linkByStateId(int $state)
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT
				*
			FROM `payments`
			WHERE
				`payments_state_id` = ". $ff_sql->quote($state) ."
			GROUP BY `id`
			ORDER BY `date` DESC
		", [
			'id' => 'int',
			'status' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'affiliate_id' => 'int',
			'coupon_id' => 'int',
			'payments_state_id' => 'int',
			'gross' => 'float',
			'fee' => 'float',
		]);

		if(!$res) {
			return false;
		}

		foreach($res as $key => $value) {
			$this->$key = $value;
		}

		$this->gateway_info = json_decode($this->gateway_info, true);

		return true;
	}

	/**
	* Gets a payment by id
	*
	* @param int $id
	*		Gets payment through it's id
	*/
	public static function getPaymentById(int $id)
	{
		if(isset(self::$cache[__FUNCTION__][$id])) {
			return self::$cache[__FUNCTION__][$id];
		}

		$payment = new payment();
		if(!$payment->linkById($id)) {
			return false;
		}

		return self::$cache[__FUNCTION__][$id] = $payment;
	}

	/**
	* Gets a payment by the used state
	*
	* @param payment_state $state
	*		The state which we are querying
	*/
	public static function getPaymentByState(payment_state $state)
	{
		$id = $state->getId();

		if(isset(self::$cache[__FUNCTION__][$id])) {
			return self::$cache[__FUNCTION__][$id];
		}

		$payment = new payment();
		if(!$payment->linkByStateId($id)) {
			return false;
		}

		return self::$cache[__FUNCTION__][$id] = $payment;
	}

	/**
	* Logs payment (or updates exsiting)
	*
	* @param int $status
	*		The status of the payment
	* @param user $user
	*		The user who's linked with payment
	* @param string $currency
	*		The currency of the payment
	* @param float $gross
	*		What the payment has grossed (including coupons, excluding fees)
	* @param float $fee
	*		The fee associated with payment
	* @param string $gatewayName
	*		The gateway name. (ie: paypal)
	* @param array $gatewayParameters
	*		Information associated with the gateway
	* @param payment_state $state
	*		The state of payment
	* @param payment_coupon|null $coupon
	*		Coupon used
	* @param payment_affiliate|null $affiliate
	*		Affiliate used
	*/
	public static function logPayment(
		int $status,
		user $user,
		string $currency,
		float $gross,
		float $fee,
		string $gatewayName,
		array $gatewayParameters,
		payment_state $state,
		payment_coupon $coupon = null,
		payment_affiliate $affiliate = null
	) {
		global $ff_sql;

		$gatewayParameters = json_encode($gatewayParameters);
		if(strlen($gatewayParameters) > 16777216) {
			// Too large to be inserted
			throw new Exception('Unable to insert Gateway Parameters');
		}

		if(strlen($currency) > 3) {
			// Currency is limited to 3 characters.
			return false;
		}

		// TODO: Check is state is being used. If it is, update the payment with
		// the assigned state. There shouldn't be duplicate states in payments.

		$existingRes = $ff_sql->query_fetch("
			SELECT `id`
			FROM `payments`
			WHERE `payments_state_id` = ". $ff_sql->quote($state->getId()) ."
		", [
			'id' => 'int'
		]);

		if(!$existingRes) {
			// Payment doesnt exist
			return $ff_sql->query("
				INSERT INTO `payments`
				(id, status, user_id, date, affiliate_id, coupon_id, payments_state_id, currency, gross, fee, gateway_name, gateway_info)
				VALUES (
					NULL,
					". $ff_sql->quote($status) .",
					". $ff_sql->quote($user->getId()) .",
					". $ff_sql->quote(FF_TIME) .",
					". $ff_sql->quote($affiliate === null ? 0 : $affiliate->getId()) .",
					". $ff_sql->quote($coupon === null ? 0 : $coupon->getId()) .",
					". $ff_sql->quote($state->getId()) .",
					". $ff_sql->quote($currency) .",
					". $ff_sql->quote($gross) .",
					". $ff_sql->quote($fee) .",
					". $ff_sql->quote($gatewayName) .",
					". $ff_sql->quote($gatewayParameters) ."
				)
			") != false;
		}
		else {
			// Payment exists
			return $ff_sql->query("
				UPDATE `payments`
				SET
					`status` = ". $ff_sql->quote($status) .",
					`currency` = ". $ff_sql->quote($currency) .",
					`gross` = ". $ff_sql->quote($gross) .",
					`fee` = ". $ff_sql->quote($fee) .",
					`gateway_name` = ". $ff_sql->quote($gatewayName) .",
					`gateway_info` = ". $ff_sql->quote($gatewayParameters) .",
					`coupon_id` = ". $ff_sql->quote($coupon === null ? 0 : $coupon->getId()) .",
					`affiliate_id` = ". $ff_sql->quote($affiliate === null ? 0 : $affiliate->getId()) ."
				WHERE
					`id` = ". $ff_sql->quote($existingRes['id']) ." AND
					`payments_state_id` = ". $ff_sql->quote($state->getId()) ."
			") != false;
		}
	}

	/**
	* Gets payments linked with a user.
	*
	* @param user $user
	*		The user whose payments we want to get
	* @param int $page
	*		The page we want to start the fetch at
	* @param int $limit
	*		The maximum amount of values we can return.
	*/
	public static function getUserPayments(user $user, int $page, int $limit = 32)
	{
		global $ff_sql;
		// TODO: Update this to be more efficient, using LIMIT for pages is idiotic & slow.
		$offset = $page * $limit;
		return $ff_sql->query_fetch_all("
			SELECT
				`id`,
				`status`,
				`user_id`,
				`date`,
				`affiliate_id`,
				`coupon_id`,
				`payments_state_id`,
				`currency`,
				`gross`,
				`fee`,
				`gateway_name`,
				`gateway_info`
			FROM `payments`
			WHERE
				`user_id` = ". $ff_sql->quote($user->getId()) ."
			ORDER BY `id` DESC
			LIMIT {$offset}, {$limit}
		", [
			'id' => 'int',
			'status' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'affiliate_id' => 'int',
			'coupon_id' => 'int',
			'payments_state_id' => 'int',
			'gross' => 'float',
			'fee' => 'float',
		]);
	}

	public function setStatus(int $status)
	{
		global $ff_sql;

		$res = $ff_sql->query("
			UPDATE
				`payments`
			SET
				`status` = ". $ff_sql->quote($status) ."
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		$this->status = $status;

		return $res !== false;
	}

	/**
	* Returns ID of payment
	* @return int
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Returns status of payment
	* @return int
	*/
	public function getStatus()
	{
		return $this->status;
	}

	/**
	* Returns userid of payment
	* @return int
	*/
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	* Returns user object of payment
	* @return user
	*/
	public function getUser()
	{
		return user::getUserById($this->getUserId());
	}

	/**
	* Returns date of payment
	* @return int
	*/
	public function getDate()
	{
		return $this->date;
	}

	/**
	* Returns affiliate of payment
	* @return payment_affiliate
	*/
	public function getAffiliate()
	{
		// TODO:
		return null;
	}

	/**
	* Returns coupon of payment
	* @return payment_coupon
	*/
	public function getCoupon()
	{
		return payment_coupon::getCouponById($this->coupon_id);
	}

	/**
	* Returns userid of payment
	* @return payment_state
	*/
	public function getPaymentState()
	{
		return payment_state::getStateById($this->payments_state_id);
	}

	/**
	* Returns currency of payment
	* @return string
	*/
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	* Returns gross of payment (excluding fees, including discounts)
	* @return float
	*/
	public function getGross()
	{
		return $this->gross;
	}

	/**
	* Returns fee of payment
	* @return float
	*/
	public function getFee()
	{
		return $this->fee;
	}

	/**
	* Returns geteway of payment
	* @return string
	*/
	public function getGatewayName()
	{
		return $this->gateway_name;
	}

	/**
	* Returns geteway information of payment
	* @return array
	*/
	public function getGatewayInfo()
	{
		return $this->gateway_info;
	}

	/**
	* I'm an idiot and misspelt gateway. Not sure if this is called elsewhere..
	* for in the event it is, im leaving this here.
	*/
	public function getGetwayInfo()
	{
		return $this->getGatewayInfo();
	}
}

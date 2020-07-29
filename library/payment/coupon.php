<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\payment\coupon.php
//
// ======================================


class payment_coupon
{
	private static $couponCache = [];
	private $id = 0;
	private $code = '<invalid>';
	private $discount = 0;
	private $creator = 0;
	private $date = 0;
	private $expiry = 0;
	private $usageCount = 0;
	private $maxUsageCount = 0;

	/**
	* Links coupon by its ID
	*/
	public function linkbyId(int $id)
	{
		global $ff_sql;

		$result = $ff_sql->query_fetch("
			SELECT
				`id`,
				`code`,
				`discount`,
				`creator`,
				`date`,
				`expiry`,
				`usage_count`,
				`max_usage_count`
			FROM `coupons`
			WHERE
				`id` = ". $ff_sql->quote($id) ."
		", [
			'id' => 'int',
			'discount' => 'int',
			'creator' => 'int',
			'date' => 'int',
			'expiry' => 'int',
			'usage_count' => 'int',
			'max_usage_count' => 'int',
			'discount' => 'float',
		]);

		if(!$result) {
			return false;
		}

		$this->id = $result['id'];
		$this->discount = $result['discount'];
		$this->creator = $result['creator'];
		$this->date = $result['date'];
		$this->expiry = $result['expiry'];
		$this->usageCount = $result['usage_count'];
		$this->maxUsageCount = $result['max_usage_count'];
		$this->code = $result['code'];

		if($this->discount > 100) {
			$this->discount = 100;
		}

		if($this->discount < 0) {
			$this->discount = 0;
		}

		return true;
	}

	/**
	* Gets coupon by its id
	*/
	public static function getCouponById(int $id)
	{
		if(isset(self::$couponCache[$id])) {
			return self::$couponCache[$id];
		}

		$coupon = new payment_coupon();
		if(!$coupon->linkById($id)) {
			return false;
		}

		return self::$couponCache[$id] = $coupon;
	}

	/**
	* Gets a coupon by its code
	*/
	public static function getCouponByCode(string $code)
	{
		global $ff_sql;

		$result = $ff_sql->query_fetch("
			SELECT
				`id`
			FROM
				`coupons`
			WHERE
				`code` = ". $ff_sql->quote($code) ."
		");

		if(!$result) {
			return false;
		}

		return self::getCouponById($result['id']);
	}

	/**
	* Gets the idenifier for the coupon
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Gets the discount percentage
	*/
	public function getDiscountPercentage()
	{
		return $this->discount;
	}

	/**
	* Hets expiry date
	*/
	public function getExpiry()
	{
		return $this->expiry;
	}

	/**
	* Whether or not it has expired
	*/
	public function getExpired()
	{
		return $this->expiry < FF_TIME;
	}

	/**
	* Gets current usage count for coupon
	*/
	public function getUsageCount()
	{
		return $this->usageCount;
	}

	public function getCode()
	{
		return $this->code;
	}

	/**
	* Increases usage count for coupon
	*/
	public function incrementUsageCount()
	{
		global $ff_sql;

		$ff_sql->query("
			UPDATE `coupons`
				SET usage_count = (usage_count + 1)
			WHERE
				id = ". $ff_sql->quote($this->id) ."
		");

		$this->usageCount++;
	}

	/**
	* Gets maximum usage count of this coupon
	*/
	public function getMaxUsageCount()
	{
		return $this->maxUsageCount;
	}

	/**
	* Gets a boolean as to whether this is a valid coupon
	*/
	public function getValid(&$reason = '')
	{
		if($this->getExpired()) {
			// expired
			$reason = 'expired';
			return false;
		}

		if(
			$this->getMaxUsageCount() > 0 &&
			$this->getUsageCount() >= $this->getMaxUsageCount()
		) {
			$reason = 'usagecount';
			return false;
		}

		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\plan.php
//
// ======================================


/**
* Plans for subscriptions
*/
class plan
{
  private static $memorizeCache = [];

  private $id = 0;
  private $currency = 'usd';
  private $price = 0.0;
  private $duration = 0;
  private $enabled = false;
  private $discountable = false;
  private $maximum_concurrent_connections = 0;
  private $name = '';

  /**
  * Creates a new subscription plan
  *
  * @param user $creator
  *   The user who's creating this plan.
  * @param string $currency
  *   The currency that will be used.
  * @param float $price
  *   Price of the plan
  * @param bool $enabled
  *   Whether or not this is enabled
  * @param bool $discountable
  *   Whether or not discounts can be applied to this plan. "p_" is for variable conflict
  * @return object (ff_return)
  */
  public static function newPlan(
    user $creator,
    string $currency,
    float $price,
    int $duration,
    bool $enabled = true,
    bool $discountable = true
  ) {
    global $ff_sql;

    $creatorGroup = $creator->getGroup();
    if(!$creatorGroup->can('mod_payments')) {
      return ff_return(false, [], 'misc-pmission-denied');
    }

    $res = $ff_sql->query("
      INSERT INTO `subscription_plans`
      (`id`, `currency`, `price`, `duration`, `enabled`, `discountable`)
      VALUES (
        NULL,
        ". $ff_sql->quote($currency) .",
        ". $ff_sql->quote($price) .",
        ". $ff_sql->quote($duration) .",
        ". $ff_sql->quote($enabled) .",
        ". $ff_sql->quote($discountable) .",
      )
    ");

    if(!$res) {
      throw new Exception('Query Error');
    }

    $id = $ff_sql->getLastInsertId();

    // Inserting audit log.
    audits_admin_newplan::insert($creator, $id);

    return ff_return(true, [
      'id' => $id
    ]);
  }

	/**
	* Links plan by it's id
	*
	* @param int $id
	*		ID we are linkign it with
	*/
  public function linkById(int $id)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT *
      FROM `subscription_plans`
      WHERE
        `id` = ". $ff_sql->quote($id) ."
    ", [
      'id' => 'int',
      'price' => 'float',
      'duration' => 'int',
      'enabled' => 'bool',
      'discountable' => 'bool',
      'maximum_concurrent_connections' => 'int'
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
	* Retuns a plan object by it's id
	*
	* @param int $id
	*		The ID which we are linking with it
	*/
  public static function getPlanById(int $id)
  {
    if(isset(self::$memorizeCache[$id])) {
      return self::$memorizeCache[$id];
    }

    // TODO: Object storage cache.

    $ret = new plan();
    if(!$ret->linkById($id)) {
      return false;
    }
    return self::$memorizeCache[$id] = $ret;
  }

  /**
  * Gets an array of plan objects.
  *
  * @param int $count
  *   Amount of plan objects you want.
  */
  public static function getPlans(int $count = 3)
  {
    global $ff_sql;
    $res = $ff_sql->query_fetch_all("
      SELECT
        `id`
      FROM
        `subscription_plans`
      WHERE
        `enabled` = 1
      ORDER BY `duration` DESC
      LIMIT ". $ff_sql->quote($count) ."
    ", [
      'id' => 'int'
    ]);

    if(!$res) {
      return false;
    }

    // Converting to an array of plan objects.
    $return = [];
    foreach ($res as $value) {
      if($plan = self::getPlanById($value['id'])) {
        $return[] = $plan;
      }
    }

    return $return;
  }

	/**
	* Gets name of plan
	* @return string
	*/
  public function getName()
  {
    return $this->name;
  }

	/**
	* Whether or not this plan is discountable
	* @return bool
	*/
  public function getDiscountable()
  {
    return $this->discountable;
  }

	/**
	* Whether or not this plan is enabled
	* @return bool
	*/
  public function getEnabled()
  {
    return $this->enabled;
  }

	/**
	* Gets duration of the plan
	* @return int
	*/
  public function getDuration()
  {
    return $this->duration;
  }

	/**
	* Gets price of the plan
	* @return float
	*/
  public function getPrice()
  {
    return $this->price;
  }

	/**
	* Gets currency of the plan (varchar 3)
	* @return string
	*/
  public function getCurrency()
  {
    return $this->currency;
  }

	/**
	* gets id of plan
	* @return int
	*/
  public function getId()
  {
    return $this->id;
  }

	/**
	* gets maimum amount of concurrent connections
	* @return int
	*/
  public function getMaximumConcurrentConnections()
  {
    return $this->maximum_concurrent_connections;
  }

  /**
  * Calculates the monthly price
  * @return float
  */
  public function monthlyPrice()
  {
    $months = $this->duration / FF_MONTH;
    return round($this->price / $months, 2);
  }

  /**
  * Get duration string in a human intrepretable string
  */
  public function getDurationString()
  {
    global $ff_context;
    $language = $ff_context->getLanguage();

    if(($x = $this->duration / FF_YEAR) >= 1) {
      $x = round($x);
      if($x == 1) {
        return $language->getPhrase('misc-year');
      }
      return $language->getPhrase('misc-num-years', [
        'num' => $x
      ]);
    }

    if(($x = $this->duration / FF_MONTH) >= 1) {
      $x = round($x);
      if($x == 1) {
        return $language->getPhrase('misc-month');
      }
      return $language->getPhrase('misc-num-months', [
        'num' => $x
      ]);
    }

    if(($x = $this->duration / FF_WEEK) >= 1) {
      $x = round($x);
      if($x == 1) {
        return $language->getPhrase('misc-week');
      }
      return $language->getPhrase('misc-num-weeks', [
        'num' => $x
      ]);
    }

    if(($x = $this->duration / FF_DAY) >= 1) {
      $x = round($x);
      if($x == 1) {
        return $language->getPhrase('misc-day');
      }
      return $language->getPhrase('misc-num-days', [
        'num' => $x
      ]);
    }

    if(($x = $this->duration / FF_HOUR) >= 1) {
      $x = round($x);
      if($x == 1) {
        return $language->getPhrase('misc-hour');
      }
      return $language->getPhrase('misc-num-hours', [
        'num' => $x
      ]);
    }

    return "{$this->duration} seconds";
  }
}

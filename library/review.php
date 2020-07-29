<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\review.php
//
// ======================================


/**
* Product review system.
*/
class review
{
  /**
  * Creates a new review.
  *
  * @param user $creator
  *   The user who is making the review.
  * @param int $stars
  *   The star rating
	* @param string $body
  *  Body of the review.
	* @param null|string $language
  *  Language in which review was written
  */
  public static function newReview(user $creator, int $stars, string $body, string $language = null)
  {
    global $ff_sql;

		if(!$creator->canReview()) {
			return ff_return(false, 'misc-permission-denied');
		}

    if($stars > 5 || $stars <= 0) {
      return ff_return(false, 'misc-invalid-stars');
    }

    if(strlen($body) <= 0 || strlen($body) > 512) {
      return ff_return(false, 'misc-body-too-long');
    }

		if($language === null) {
			$language = $ff_config->get('session-default-language');
		}

    $res = $ff_sql->query("
      INSERT INTO `reviews`
      (`id`, `user_id`, `date`, `stars`, `language_code`, `approved`, `deleted`, `body`)
      VALUES (
        NULL,
        ". $ff_sql->quote($creator->getId()) .",
        ". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($stars) .",
        ". $ff_sql->quote($language) .",
        0,
        0,
        ". $ff_sql->quote($body) ."
      )
    ");

    return ff_return($res !== false, [
      'id' => $ff_sql->getLastInsertId()
    ]);
  }

  /**
  * Get ideal reviews to be displayed to all.
	* @param int $limit
	*		Amount of values we can return
	* @param null|string $languagePriority
	*		The language which we want to get results for.
  * @return array
  */
  public static function getPreferredReviews(int $limit = 128, $language = null)
  {
    global $ff_context, $ff_sql;

    $cache = $ff_context->getCache();
    $cache_key = ff_cacheKey(__CLASS__ . __FUNCTION__, [$limit, $language]);
    if($cached_object = $cache->get($cache_key)) {
      return $cached_object;
    }

		$otherWhere = (($language === null)
			? ''
			: ' AND language_code = '. $ff_sql->quote($language)
		);

    $res = $ff_sql->query_fetch_all("
      SELECT
        `id`,
        `user_id`,
        `date`,
        `stars`,
        `body`,
				`language_code`
      FROM `reviews`
      WHERE
        `approved` = 1 AND
        `deleted` = 0 AND
        `stars` > 3
				{$otherWhere}
      ORDER BY `stars` DESC
      LIMIT ". $ff_sql->quote($limit) ."
    ", [
      'id' => 'int',
      'user_id' => 'int',
      'date' => 'int',
      'stars' => 'int'
    ]);

    if(!$res) {
      return false;
    }

    // Shuffle array
    shuffle($res);

    // Store in cache for an hour.
    $cache->store($cache_key, $res, FF_TIME + (60*60));

    return $res;
  }

  /**
  * Gets information about a review.
  *
  * NOTE: This method is only suppose to be here temporarally.
  *
  * @param int $id
  *   The ID of the review we want information on.
  */
  public static function getReviewInfoById(int $id)
  {
    global $ff_sql;
    return $ff_sql->query_fetch("
      SELECT
        `id`,
        `user_id`,
        `date`,
        `stars`,
        `approved`,
        `deleted`,
        `body`
      FROM
        `reviews`
      WHERE
        `id` = ". $ff_sql->quote($id) ."
    ", [
      'id' => 'int',
      'user_id' => 'int',
      'date' => 'int',
      'stars' => 'int',
      'approved' => 'bool',
      'deleted' => 'bool'
    ]);
  }

	public static function getReviewsByUser(user $user)
	{
		global $ff_sql;
    return $ff_sql->query_fetch_all("
      SELECT
        `id`,
        `user_id`,
        `date`,
        `stars`,
        `approved`,
        `deleted`,
        `body`
      FROM
        `reviews`
      WHERE
        `user_id` = ". $ff_sql->quote($user->getId()) ."
    ", [
      'id' => 'int',
      'user_id' => 'int',
      'date' => 'int',
      'stars' => 'int',
      'approved' => 'bool',
      'deleted' => 'bool'
    ]);
	}

	/**
	* Checks whether or not a user can write a review.
	*/
	public static function canUserReview(user $user)
	{
		global $ff_sql;

		$group = $user->getGroup();
		if(!$group->can('review')) {
			return false;
		}

		// Checking for reviews in the past week
    $previous = $ff_sql->query_fetch("
      SELECT count(1) as cnt
      FROM `reviews`
      WHERE
        `user_id` = ". $ff_sql->quote($user->getId()) ." AND
        `date` > ". $ff_sql->quote(FF_TIME - FF_WEEK) ."
    ");

    if($previous['cnt'] >= 1) {
      return false;
    }

		$subscription = $user->getSubscription();

		return $subscription->valid;
	}

	public static function getReviews(int $index, int $count, $filter = [])
	{
		global $ff_sql;

		$whereAppend = '';
		if(isset($filter['hide_deleted']) && $filter['hide_deleted']) {
			$whereAppend .= ' AND `deleted` = 0 ';
		}

		if(isset($filter['hide_approved']) && $filter['hide_approved']) {
			$whereAppend .= ' AND `approved` = 0 ';
		}


		return $ff_sql->query_fetch_all("
			SELECT
				`id`,
				`user_id`,
				`date`,
				`stars`,
				`approved`,
				`deleted`,
				`body`
			FROM
				`reviews`
			WHERE
				`id` > ". $ff_sql->quote($index) ."
        $whereAppend
			LIMIT ". $ff_sql->quote($count) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'stars' => 'int',
			'approved' => 'bool',
			'deleted' => 'bool'
		]);
	}

	/**
	* Deletes review
	*
	* @param user $user
	*		Person doing the action
	* @param int $reviewId
	*		ID of review
	*/
	public static function deleteReview(user $user, int $reviewId)
	{
		global $ff_sql;
		if(!$user->getGroup()->can('mod_reviews')) {
			return ff_return(false, 'misc-permission-denied');
		}

		$review = self::getReviewInfoById($reviewId);
		if(!$review) {
			return ff_return(false, 'misc-not-found');
		}

		$reviewUser = user::getuserById($review['user_id']);

		$ff_sql->query("
			UPDATE `reviews`
			SET `deleted` = 1
			WHERE `id` = ". $ff_sql->quote($reviewId) ."
		");

		audits_admin_deletereview::insert($user, $reviewId);

		notification::push($reviewUser, 'notif-review-deleted', []);

		return ff_return(true, 'misc-success');
	}

	/**
	* Undeletes review
	*
	* @param user $user
	*		Person doing the action
	* @param int $reviewId
	*		ID of review
	*/
	public static function undeleteReview(user $user, int $reviewId)
	{
		global $ff_sql;
		if(!$user->getGroup()->can('mod_reviews')) {
			return ff_return(false, 'misc-permission-denied');
		}

		$review = self::getReviewInfoById($reviewId);
		if(!$review) {
			return ff_return(false, 'misc-not-found');
		}

		$reviewUser = user::getUserById($review['user_id']);

		$ff_sql->query("
			UPDATE `reviews`
			SET `deleted` = 0
			WHERE `id` = ". $ff_sql->quote($reviewId) ."
		");

		audits_admin_undeletereview::insert($user, $reviewId);

		notification::push($reviewUser, 'notif-review-undeleted', []);

		return ff_return(true, 'misc-success');
	}

	/**
	* approves review
	*
	* @param user $user
	*		Person doing the action
	* @param int $reviewId
	*		ID of review
	*/
	public static function approveReview(user $user, int $reviewId)
	{
		global $ff_sql;
		if(!$user->getGroup()->can('mod_reviews')) {
			return ff_return(false, 'misc-permission-denied');
		}

		$review = self::getReviewInfoById($reviewId);
		if(!$review) {
			return ff_return(false, 'misc-not-found');
		}

		$reviewUser = user::getuserById($review['user_id']);

		$ff_sql->query("
			UPDATE `reviews`
			SET `approved` = 1, `deleted` = 0
			WHERE `id` = ". $ff_sql->quote($reviewId) ."
		");

		audits_admin_approvereview::insert($user, $reviewId);

		notification::push($reviewUser, 'notif-review-approved', []);

		return ff_return(true, 'misc-success');
	}
}

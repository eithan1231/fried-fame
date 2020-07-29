<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\announcement.php
//
// ======================================


class announcement
{
	private $id = 0;
	private $date = 0;
	private $expiry = 0;
	private $user_id = 0;
	private $subject = '';
	private $body = '';
	private $bodySanitized = null;

	/**
	* Creates a new announcement
	*
	* @param user $user
	*		The user that is creating the announcement
	* @param string $subject
	*		Subject of the announcement
	* @param string $body
	*		The primary body of the announcement (HTML ENABLED)
	* @param int $duration
	*		The duration this announcement will be shown for. In unix time.
	*/
	public static function createAnnouncement(user $user, string $subject, string $body, int $duration)
	{
		global $ff_sql;

		if(!$user->getGroup()->can('mod_announcement')) {
			return ff_return(false, 'misc-permission-denied');
		}

		if(strlen($subject) > 256) {
			return ff_return(false, 'misc-subject-too-long');
		}

		$ff_sql->query("
			INSERT INTO `announcements`
			(`id`, `date`, `expiry`, `user_id`, `subject`, `body`)
			VALUES (
				NULL,
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote(FF_TIME + $duration) .",
				". $ff_sql->quote($user->getId()) .",
				". $ff_sql->quote($subject) .",
				". $ff_sql->quote($body) ."
			)
		");

		$id = $ff_sql->getLastInsertId();

		audits_admin_announcement::insert($user, $id);

		return ff_return(true, [
			'id' => $id
		]);
	}

	/**
	* Links this object with an associated ID.
	*
	* @param int $id
	*		Id of the announcement we want to link with.
	* @return bool
	*/
	public function linkById(int $id)
	{
		global $ff_sql;

		$row = $ff_sql->fetch("
			SELECT *
			FROM `announcements`
			WHERE
			 `id` = ". $ff_sql->quote($id) ."
			 LIMIT 1
		", [
			'id' => 'int',
			'date' => 'int',
			'expiry' => 'int',
			'user_id' => 'int'
		]);

		if(!$row) {
			return false;
		}

		foreach ($row as $key => $value) {
			$this->$key = $value;
		}

		return true;
	}

	/**
	* Gets an announcement by id
	*
	* @param int $id
	*		Id of the announcement we want to get.
	* @return announcement
	*/
	public static function getAnnouncementById(int $id)
	{
		global $ff_context;
		$cache = $ff_context->getCache();

		$cacheKey = self::buildCacheKey($id);
		$announcement = $cache->get($cacheKey);
		if(!$announcement) {
			$announcement = new announcement();
			if(!$announcement->linkById($id)) {
				return false;
			}

			// Calculating expiry.
			$announcementExpiry = $announcement->getExpiry();
			if($announcementExpiry < FF_TIME) {
				$announcementExpiry = FF_TIME + FF_MONTH;
			}

			$cache->store($cacheKey, $announcement, $announcementExpiry);
		}

		return $announcement;
	}

	/**
	* Gets a list of active announcements (ones which you should show user.)
	* @return array
	*/
	public static function getActiveAnnouncements(int $limit = -1)
	{
		global $ff_sql;

		$queryAppend = "";
		if($limit > 0) {
			$queryAppend .= "LIMIT ". $ff_sql->quote($limit) ."\n";
		}

		$res = $ff_sql->query_fetch_all("
			SELECT
				`id`
			FROM
				`announcements`
			WHERE
				`expiry` > ". $ff_sql->quote(FF_TIME) ."
			ORDER BY `id` DESC
			$queryAppend
		");

		if(!$res) {
			return false;
		}

		$res = array_map(function($p) {
			return announcement::getAnnouncementById($p['id']);
		}, $res);

		return $res;
	}

	/**
	* Unique idenifier linked with this announcement
	* @return int
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Date at which this announcement was made
	* @return int
	*/
	public function getDate()
	{
		return $this->date;
	}

	/**
	* Date at which this announcement expires
	* @return int
	*/
	public function getExpiry()
	{
		return $this->expiry;
	}

	/**
	* Gets user id that created announcement
	* @return int
	*/
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	* Gets user account who created announcement
	* @return user
	*/
	public function getUser()
	{
		return user::getUserById($this->getUserId());
	}

	/**
	* Gets announcement subject
	* @return string
	*/
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	* Gets the clean html body.
	* @return string
	*/
	public function getCleanBody()
	{
		if($this->bodySanitized) {
			return $this->bodySanitized;
		}

		// Loading HTMLPurifier
		autoloader::load('dependencies/htmlpurifier/HTMLPurifier-includes');

		// Getting the default HTML Purifier config
		$config = HTMLPurifier_Config::createDefault();

		// Setting the elements we want to allow.
		$config->set('HTML.Allowed', 'b,i,u,strike,h1,h2,h3,h4,h5,p,blockquote,pre,ul,li,ol,hr,a[href],div');

		// Creating purifier object
		$purifier = new HTMLPurifier($config);

		// Storing the sanitized body
		$this->bodySanitized = $purifier->purify($this->body);

		// Updating cache.
		$this->selfCache();

		// Returning sanitized object
		return "<!-- Sanitized line:". __LINE__ ." func:". __FUNCTION__ ." class:". __CLASS__ ." UNCACHED -->\n{$this->bodySanitized}";
	}

	private function selfCache()
	{
		global $ff_context;
    $cache = $ff_context->getCache();
    $key = self::buildCacheKey($this->id);
    $cache->store($key, $this, $this->expiry);
	}

	private static function buildCacheKey(int $id)
	{
		return ff_cacheKey(__CLASS__, [$id]);
	}
}

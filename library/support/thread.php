<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\support\thread.php
//
// ======================================


class support_thread
{
  public static $cachedThreads = [];
  private $id = null;
  private $userId = null;
  private $subject = null;
  private $date = null;
  private $lastPostDate = null;
  private $isClosed = null;
  private $isDeleted = null;
  private $posts = [];

  /**
  * Links the current thread object with an id.
  *
  * @param int $id
  *   The ID we want to link this obcjt with.
  */
  public function linkById(int $id)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT
        `id`,
        `user_id`,
        `subject`,
        `date`,
        `last_post_date`,
        `is_closed`,
        `is_deleted`
      FROM
        `support_threads`
      WHERE
        `id` = ". $ff_sql->quote($id) ."
    ", [
      'id' => 'int',
      'user_id' => 'int',
      'date' => 'int',
      'last_post_date' => 'int',
      'is_closed' => 'bool',
      'is_deleted' => 'bool',
    ]);

    if($res) {
      $this->id = $res['id'];
      $this->userId = $res['user_id'];
      $this->subject = $res['subject'];
      $this->date = $res['date'];
      $this->lastPostDate = $res['last_post_date'];
      $this->isClosed = $res['is_closed'];
      $this->isDeleted = $res['is_deleted'];

      $this->selfCache();
    }

    return $res !== false;
  }

  /**
  * Gets a thread object by it's id, and user. if user is null, it will be ignored.
  *
  * @param int $id
  *   ID of the thread.
  * @param user $user
  *   The user who is linked with the thread. Leave this to null to ignore checks.
  */
  public static function getThreadById(int $id, user $user = null)
  {
    global $ff_context;
		$thread = null;
    if(!isset(self::$cachedThreads[$id])) {
			$cache = $ff_context->getCache();
	    $cacheKey = self::buildCacheKey($id);
	    if(!$thread = $cache->get($cacheKey)) {
	      $thread = new support_thread();
	      if(!$thread->linkById($id)) {
	        return ff_return(false);
	      }

	      $cache->store($cacheKey, $thread, self::getCacheExpiry());
	    }

			// memorize cache
	    self::$cachedThreads[$id] = &$thread;
		}
		else {
			// memorize cache
	    $thread = &self::$cachedThreads[$id];
		}

		if($user) {
			$group = $user->getGroup();
			if(!$group->can('support')) {
	      // The users assigned user group doesnt have permission to support.
	      return ff_return(false, [], 'misc-permission-denied');
	    }

	    if(!$group->can('mod_support')) {
	      if($thread->getUserId() !== $user->getId()) {
	        return ff_return(false, [], 'misc-permission-denied');
	      }
	    }
		}

		return ff_return(true, $thread);
  }

  /**
  * Creates a new support thread.
  *
  * @param user $creator
  *   User who is creating the thread.
  * @param string $subject
  *   Subject of thread
  * @param string $body
  *   The content of thread.
  * @return ff_return on success, the data key 'id' is set to the thread id.
  */
  public static function newThread(user $creator, string $subject, string $body)
  {
    global $ff_sql;
    $group = $creator->getGroup();

    if(!$group->can('support')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		if(mb_strlen($subject) <= 0) {
      return ff_return(false, [], 'misc-subject-too-short');
    }

    if(mb_strlen($subject) > 128) {
      return ff_return(false, [], 'misc-subject-too-long');
    }

		if(mb_strlen($body) <= 0) {
      return ff_return(false, [], 'misc-body-too-short');
    }

    if(mb_strlen($body) > 16777215) {
      return ff_return(false, [], 'misc-body-too-long');
    }

    $ff_sql->query("
      INSERT INTO `support_threads`
      (`id`, `user_id`, `subject`, `date`, `last_post_date`, `is_closed`, `is_deleted`)
      VALUES (
        NULL,
        ". $ff_sql->quote($creator->getId()) .",
        ". $ff_sql->quote($subject) .",
        ". $ff_sql->quote(FF_TIME) .",
        ". $ff_sql->quote(FF_TIME) .",
        0,
        0
      )
    ");

		$id = $ff_sql->getLastInsertId();
    $thread = self::getThreadById($id, $creator);
    if($thread->success) {
      $thread->data->newPost($creator, $body);
    }

    return ff_return(true, ['id' => $id]);
  }

	/**
	* Gets support threads for administrators
	* @param user $searcher
	*		The user who's executing the query
	* @param user|null $subject
	*		Excluding all results except for this user.
	*/
	public static function getThreadsForAdmins(user $searcher, user $subject = null, $filter = null)
	{
		global $ff_sql;

		$where = [];
		if($filter === null) {
			$filter = [
				'closed' => false,
				'deleted' => false,
			];
		}

		if(isset($filter['closed'])) {
			if($filter['closed']) {
				$where[] = '`support_threads`.`is_closed` = 1';
			}
			else {
				$where[] = '`support_threads`.`is_closed` = 0';
			}
		}

		if(isset($filter['deleted'])) {
			if($filter['deleted']) {
				$where[] = '`support_threads`.`is_deleted` = 1';
			}
			else {
				$where[] = '`support_threads`.`is_deleted` = 0';
			}
		}

		if($subject != null) {
			$where[] = '`support_threads`.`user_id` = '. $ff_sql->quote($subject->getId());
		}

		return $ff_sql->query_fetch_all("
			SELECT
				`support_threads`.`id`,
        `support_threads`.`user_id`,
        `support_threads`.`subject`,
        `support_threads`.`date`,
        `support_threads`.`last_post_date`,
        `support_threads`.`is_closed`,
        `support_threads`.`is_deleted`
			FROM
				`support_threads`
        INNER JOIN `users` ON `users`.`id` = `support_threads`.`user_id`
        INNER JOIN `groups` ON `groups`.`id` = `users`.`group_id`
			WHERE
				". implode(" AND ", $where) ."
			ORDER BY
        `groups`.`can_mod_support` ASC,
        `support_threads`.`last_post_date` DESC
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
			'last_post_date' => 'int',
			'is_closed' => 'bool',
			'is_deleted' => 'bool',
		]);
	}

  /**
  * Inserts a new post.
  *
  * @param user $user
  *   User creating post.
  * @param string $body
  *   Body of the post.
  */
  public function newPost(user $user, string $body)
  {
    $ret = support_post::newPost($this, $user, $body);
    $this->getPosts(false);// Purge posts cache, and update it.
    return $ret;
  }

	/**
	* Marks thread as closed
	*
	* @param user $user
	*		The user who's closing thread. Permission checks are executed.
	*/
	public function close(user $user)
	{
		global $ff_sql;

		// Permission check. Only mod_support can close.
		$userGroup = $user->getGroup();
		if(!$userGroup->can('mod_support')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		// Closing
		$ff_sql->query("
			UPDATE `support_threads`
			SET `is_closed` = 1
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		$this->isClosed = true;
		$this->selfCache();

		audits_admin_closesupportticket::insert($user, $this);

		// Pushing email
		$emailSender = new email_supportthreadclosed($this);
		$emailSender->setRecipient($this->getUser()->getEmail());
		$emailSender->send();

		return ff_return(true);
	}

	/**
	* Marks thread as opened
	*
	* @param user $user
	*		The user who's opening thread. Permission checks are executed.
	*/
	public function open(user $user)
	{
		global $ff_sql;

		// Permission check. Only mod_support can close.
		$userGroup = $user->getGroup();
		if(!$userGroup->can('mod_support')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		// Opening
		$ff_sql->query("
			UPDATE `support_threads`
			SET `is_closed` = 0
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		$this->isClosed = false;
		$this->selfCache();

		audits_admin_opensupportticket::insert($user, $this);

		// Pushing email
		$emailSender = new email_supportthreadopened($this);
		$emailSender->setRecipient($this->getUser()->getEmail());
		$emailSender->send();

		return ff_return(true);
	}

	/**
	* Deletes a thread
	*
	* @param user $user
	*		The user who's deleting thread. Permission checks are executed.
	*/
	public function delete(user $user)
	{
		global $ff_sql;

		// Permission check. Only mod_support can close.
		$userGroup = $user->getGroup();
		if(!$userGroup->can('mod_support')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		// Deleting
		$ff_sql->query("
			UPDATE `support_threads`
			SET `is_deleted` = 1
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		$this->isDeleted = true;
		$this->selfCache();

		audits_admin_deletesupportticket::insert($user, $this);

		// Pushing email
		$emailSender = new email_supportthreaddeleted($this);
		$emailSender->setRecipient($this->getUser()->getEmail());
		$emailSender->send();

		return ff_return(true);
	}

	/**
	* Undeletes a thread
	*
	* @param user $user
	*		The user who's undeleting thread. Permission checks are executed.
	*/
	public function undelete(user $user)
	{
		global $ff_sql;

		// Permission check. Only mod_support can close.
		$userGroup = $user->getGroup();
		if(!$userGroup->can('mod_support')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		// Undeleting
		$ff_sql->query("
			UPDATE `support_threads`
			SET `is_deleted` = 0
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		$this->isDeleted = false;
		$this->selfCache();

		audits_admin_undeletesupportticket::insert($user, $this);

		// Pushing email
		$emailSender = new email_supportthreadundeleted($this);
		$emailSender->setRecipient($this->getUser()->getEmail());
		$emailSender->send();

		return ff_return(true);
	}

  /**
  * Gets all posts linked with thread.
  *
  * @param bool $cache
  *   If this is false, it will purge all cached linked with posts.
  */
  public function getPosts(bool $cache = true)
  {
    // NOTE: We may be fetching from database TWICE! BUT, this is for ease of
    // cache. The second time should only be selected once from db every x hours.
    global $ff_sql;

    if($cache && count($this->posts) > 0) {
      return $this->posts;
    }

    $this->posts = $ff_sql->query_fetch_all("
      SELECT `id`, `user_id`, `date`, `is_deleted`
      FROM `support_posts`
      WHERE `thread_id` = ". $ff_sql->quote($this->id) ."
      ORDER BY `date` DESC
    ", [
      'is_deleted' => 'bool',
      'user_id' => 'int',
      'date' => 'int',
      'id' => 'int',
    ]);

    // Updating cache.
    $this->selfCache();

    return $this->posts;
  }

  /**
  * Gets the most recent post.
  */
  public function getRecentMostPost()
  {
    if(!$this->posts) {
      $this->getPosts();
    }

    if(!$this->posts || count($this->posts) === 0) {
      // should never be reached.
      throw new Exception('missing post');
    }

    $recent = $this->posts[0];
    foreach($this->posts as $post) {
      if($post['date'] > $recent['date']) {
        $recent = $post;
      }
    }

    return $recent;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getUserId()
  {
    return $this->userId;
  }

  public function getUser()
  {
    return user::getUserById($this->userId);
  }

  public function getSubject()
  {
    return $this->subject;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function getLastPostDate()
  {
    return $this->lastPostDate;
  }

  public function getIsClosed()
  {
    return $this->isClosed;
  }

  public function isClosed()
  {
    return $this->getIsClosed();
  }

  public function getIsDeleted()
  {
    return $this->isDeleted;
  }

  public function isDeleted()
  {
    return $this->getIsDeleted();
  }

  public function selfCache()
  {
    global $ff_context;
    $ff_context->getCache()->store(
      self::buildCacheKey($this->getId()),
      $this,
      self::getCacheExpiry()
    );
  }

  public static function buildCacheKey(int $id)
  {
    return ff_cacheKey(__CLASS__, [$id]);
  }

  public static function getCacheExpiry()
  {
    return FF_TIME + FF_MONTH;
  }
}

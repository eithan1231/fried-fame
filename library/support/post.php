<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\support\post.php
//
// ======================================


/**
* The post of a support post.
*
*/
class support_post
{
  private static $cachedPosts = [];

  private $id = 0;
  private $threadId = 0;
  private $deleted = false;
  private $userId = false;
  private $date = 0;
  private $body = null;
	private $bodyHtmlSupportEnabled = false;
  private $bodySanitized = null;

  /**
  * Links this post to an id
  *
  * @param int $id
  *   The id you wish to link with this object.
  */
  public function linkById(int $id)
  {
    global $ff_sql, $ff_context;

    $res = $ff_sql->query_fetch("
      SELECT `date`, `is_deleted`, `body`, `user_id`, `thread_id`
      FROM `support_posts`
      WHERE `id` = ". $ff_sql->quote($id) ."
    ", [
      'date' => 'int',
      'user_id' => 'int',
      'thread_id' => 'int',
      'is_deleted' => 'bool',
    ]);

    if($res) {
      $this->id = $id;
      $this->userId = $res['user_id'];
      $this->threadId = $res['thread_id'];
      $this->deleted = $res['is_deleted'];
      $this->body = $res['body'];
      $this->date = $res['date'];
    }

    return $res !== false;
  }

  /**
  * Gets a post by the id of a psot.
  *
  * @param int $id
  *   ID of post you want to retreive
  */
  public static function getPostById(int $id)
  {
    global $ff_context;

    if(isset(self::$cachedPosts[$id])) {
      return self::$cachedPosts[$id];
    }

    $cache = $ff_context->getCache();
    $cacheKey = self::buildCacheKey($id);
    $post = $cache->get($cacheKey);
    if(!$post) {
      $post = new support_post();
      if(!$post->linkById($id)) {
        return false;
      }

      // Wasnt cached, so lets try cache it.
      $cache->store($cacheKey, $post, self::getCacheExpiry());
    }

    return self::$cachedPosts[$id] = $post;
  }

  /**
  * Creates a new post, linked with $thread, and created by $user
  *
  * @param support_thread $thread
  *   The thread this post is linked to.
  * @param user $user
  *   User who is creating this post.
  * @param string $body
  *   Body of this post
  */
  public static function newPost(support_thread $thread, user $user, string $body)
  {
    global $ff_sql, $ff_router;

		if(mb_strlen($body) > 16777215) {
      return ff_return(false, [], 'misc-body-too-long');
    }

		if(mb_strlen($body) <= 0) {
			return ff_return(false, [], 'misc-body-too-short');
    }

    if($thread->isClosed() || $thread->isDeleted()) {
      // Deleted or closed. Ideally we shouldnt be sending permission denied
      // when it's deleted, beucase that's an acknowledgement that it exists.
      // but whatever.
      return ff_return(false, [], 'misc-permission-denied');
    }

    $user_group = $user->getGroup();

    // Checking permissions
    $permitted = false;
    if($thread->getUserId() === $user->getId()) {
      // User created thread.
      $permitted = true;
    }
    if(!$permitted && $user_group->can('mod_support')) {
      // Support moderator
      $permitted = true;
    }
    if(!$permitted) {
      // Not permitted to post.
      return ff_return(false, [], 'misc-permission-denied');
    }

    $ff_sql->query("
      INSERT INTO `support_posts`
      (`id`, `thread_id`, `user_id`, `date`, `is_deleted`, `body`)
      VALUES (
        NULL,
        ". $ff_sql->quote($thread->getId()) .",
        ". $ff_sql->quote($user->getId()) .",
        ". $ff_sql->quote(FF_TIME) .",
        0,
        ". $ff_sql->quote($body) ."
      )
    ");

    $ff_sql->query("
      UPDATE `support_threads`
      SET
        `last_post_date` = ". $ff_sql->quote(FF_TIME) ."
      WHERE
        `id` = ". $ff_sql->quote($thread->getId()) ."
    ");

    // Push email to support thread starter
    if($thread->getUserId() !== $user->getId()) {
      $threadCreator = $thread->getUser();

      $emailSender = new email_supportpostreply(
        $threadCreator->getUsername(),
        $thread->getId(),
        $ff_router->getPath('cp_support_view', [
          'id-subject' => ff_idAndSubject($thread->getId(), $thread->getSubject())
        ]),

        $thread->getSubject(),
        $user->getUsername(),
        $user_group->getName(),
        $user_group->getColor(),
        ff_esc(strip_tags($body))
      );

      $emailSender->setRecipient($threadCreator->getEmail());
      $emailSender->send();
    }

    return ff_return(true);
  }

  /**
  * Gets the thread this post is linked to.
  * @return int
  */
  public function getThreadId()
  {
    return $this->threadId;
  }

  /**
  * Gets the thread object linked with this post.
  * @return support_thread
  */
  public function getThread()
  {
    return support_thread::getThreadId($this->threadId);
  }

  /**
  * Gets the poster of this post.
  * @return int
  */
  public function getUserId()
  {
    return $this->userId;
  }

  /**
  * Gets user who posted.
  * @return user
  */
  public function getUser()
  {
    return user::getUserById($this->userId);
  }

  /**
  * gets the identifier for the current object
  * @return int
  */
  public function getId()
  {
    return $this->id;
  }

  /**
  * Checks whether this support post is deleted
  * @return bool
  */
  public function isDeleted()
  {
    return $this->deleted;
  }

  /**
  * The date at which this was created
  * @return int
  */
  public function getDate()
  {
    return $this->date;
  }

  /**
  * Gets the body, formatted.
  * @return string
  */
  public function getCleanBody()
  {
    global $ff_config;
    if(!$this->body) {
      return '';
    }

		if($this->bodyHtmlSupportEnabled == $ff_config->get('allow-html-support-posts')) {
			if($this->bodySanitized) {
	      return $this->bodySanitized;
	    }
		}
		$this->bodyHtmlSupportEnabled = $ff_config->get('allow-html-support-posts');

    $this->bodySanitized = str_replace("\n", '<br />', $this->body);

    // Cleaning
    if($ff_config->get('allow-html-support-posts')) {
      autoloader::load('dependencies/htmlpurifier/HTMLPurifier-includes');
      $config = HTMLPurifier_Config::createDefault();
      $config->set('HTML.Allowed', 'b,i,u,strike,h1,h2,h3,h4,h5,p,blockquote,pre,ul,li,ol,hr,a[href],div');
      $purifier = new HTMLPurifier($config);
      $this->bodySanitized = $purifier->purify($this->bodySanitized);
    }
    else {
			if(strpos($this->bodySanitized, '<')) {
				// If administrator disables html after haveing it enabled, it will
				// leave html code. This is just a crappy attempt to try remove it.
				$this->bodySanitized = ff_esc(strip_tags($this->bodySanitized));
			}
			else {
				$this->bodySanitized = ff_esc($this->bodySanitized);
			}
    }

    $this->selfCache();

    return $this->bodySanitized;
  }

  private function selfCache()
  {
    global $ff_context;
    $cache = $ff_context->getCache();
    $key = self::buildCacheKey($this->id);
    $cache->store($key, $this, self::getCacheExpiry());
  }

  public static function buildCacheKey($id)
  {
    return ff_cacheKey(__CLASS__, [$id]);
  }

  public static function getCacheExpiry()
  {
    return FF_TIME + FF_MONTH;
  }
}

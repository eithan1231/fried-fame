<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\knowbase\post.php
//
// ======================================


/**
* Knowledgebase post.
*/
class knowbase_post
{
  private $id = 0;
  private $date = 0;
  private $user_id = 0;
  private $view_count = 0;
  private $index_title = '';
  private $title = '';
  private $description = '';
  private $body = '';
  private $htmlBody = null;
  private $headers = null;

  /**
  * Creates a new knowledge base post.
  * @param user $user Who is creating the post.
  * @param knowbase_category $category The category which this post is a member of.
  * @param string $title Title of the post.
  * @param string $body Body of the element. Limited to 64KB.
  * @param string $description Description of this post. Used in previews, and opengraph.
  */
  public static function createPost(user $user, knowbase_category $category, string $title, string $body, string $description)
  {
    global $ff_sql;

    if(!$user->getGroup()->can('mod_knowbase')) {
      return ff_return(false, 'misc-permission-denied');
    }

    if(strlen($title) > 256) {
      return ff_return(false, 'misc-subject-too-long');
    }

    if(strlen($description) > FF_KB) {
      return ff_return(false, 'misc-description-too-long');
    }

    if(strlen($body) > FF_KB * 64) {
      return ff_return(false, 'misc-description-too-long');
    }

    // Lower case, remove all non-alpha-numerics (except dashes and underscores),
    // and replace spaces with dashes.
    $indexTitle = strtolower(ff_stripBad(str_replace(' ', '-', $title)));

    $ff_sql->query("
      INSERT INTO `knowbase_post`
      (id, date, category_id, user_id, view_count, index_title, title, description, body)
      VALUES (
        NULL,
        ". $ff_sql->quote(FF_TIME) .",
        ". $ff_sql->quote($category->getId()) .",
        ". $ff_sql->quote($user->getId()) .",
        0,
        ". $ff_sql->quote($indexTitle) .",
        ". $ff_sql->quote($title) .",
        ". $ff_sql->quote($description) .",
        ". $ff_sql->quote($body) ."
      )
    ");

    $id = $ff_sql->getLastInsertId();

    audits_admin_knowbasepost::insert($user, $id);

    return ff_return(true, [
      'id' => $id
    ]);
  }

  /**
  * Gets a knowbase_post object by its identifer
  * @param int $id
  */
  public static function getPostById(int $id)
  {
    $post = new self();
    if($post->linkById($id)) {
      return $post;
    }

    return null;
  }

  /**
  * Gets knowbase_post by its indexed title.
  * @param string $indexTitle
  */
  public static function getPostByIndexTitle(string $indexTitle)
  {
    // NOTE: This function may appear counter-productive, and frankly it might
    // be. Converting a name to id, then looking up that exact row by id... but
    // for the way we've implemented caching this is essential. Perhaps in future
    // we can add some sort of alias for title to id so no db query is needed for
    // such lookup.

    global $ff_sql;

    $res = $ff_sql->fetch("
      SELECT `id`
      FROM `knowbase_post`
      WHERE `index_title` = ". $ff_sql->quote($indexTitle) ."
      LIMIT 1
    ");

    return (!!$res
      ? self::getPostById($res['id'])
      : null
    );
  }

  public function linkById(int $id)
  {
    global $ff_sql;

    $res = $ff_sql->fetch("
      SELECT *
      FROM knowbase_post
      WHERE id = ". $ff_sql->quote($id) ."
      LIMIT 1
    ", [
      'id' => 'int',
      'date' => 'int',
      'category_id' => 'int',
      'user_id' => 'int'
    ]);

    if(!$res) {
      return false;
    }

    foreach($res as $key => $value) {
      $this->$key = $value;
    }

    return true;
  }

  /**
  * @return integer
  */
  public function getId()
  {
    return $this->id;
  }

  /**
  * @return string
  */
  public function getTitle()
  {
    return $this->title;
  }

  /**
  * @return string unparsed body.
  */
  public function getBody()
  {
    return $this->body;
  }

  /**
  * @return string parsed bidy.
  */
  public function getBodyParsed()
  {
    $this->processBody();
    return $this->htmlBody;
  }

  /**
  * Gets the HTML headers of the post.
  */
  public function getBodyHeaders()
  {
    $this->processBody();
  }

  /**
  * Gets other recommended posts.
  * @return array
  */
  public function getRecommendations()
  {
    return [];
  }

  /**
  * Gets post description.
  * @return string
  */
  public function getDescription()
  {
    return $this->description;
  }

  /**
  * Alias of getBodyHeaders
  */
  public function getHeaders()
  {
    return $this->getBodyHeaders();
  }

  /**
  * Increments the view count for this post.
  */
  public function incrementViewcount()
  {
    global $ff_sql;

    $this->view_count++;

    return !!$ff_sql->query("
      UPDATE knowbase_post
      SET view_count = (view_count + 1)
      WHERE id =". $ff_sql->quote($this->id) ."
    ");
  }

  private function processBody()
  {
    if($this->htmlBody && $this->headers != null) {
      // Already processed
      return true;
    }

    if($parserDown = knowbase_parserdown::parse($this->body)) {
      $this->htmlBody = $parserDown->parsed;
      $this->headers = $parserDown->headers;
      return true;
    }

    return false;
  }
}

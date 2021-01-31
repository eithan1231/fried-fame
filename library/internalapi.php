<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\internalapi.php
//
// ======================================


/**
* This API is an administrative API that services can use for a variety of
* intended uses. Some of the uses included, are fetching FF-RPC nodes,
* inserting new FF-RPC nodes for automated deployment, and much more.
*/
class internalapi
{
  public const PERMIT_FFRPC = 'ffrpc';

  public const PERMIT_TYPES = [
    self::PERMIT_FFRPC
  ];

  private $id = 0;
  private $date = 0;
  private $expiry = false;
  private $enabled = 0;
  private $userId = 0;
  private $permit = "";
  private $token = "";

  private function linkByData($data)
  {
    $this->id = $data['id'];
    $this->date = $data['date'];
    $this->expiry = $data['expiry'];
    $this->enabled = $data['enabled'];
    $this->userId = $data['user_id'];
    $this->permit = $data['permit'];
    $this->token = $data['token'];
  }

  /**
  * Creates a new Internal-API Token
  * @param user $user the user creating the api token
  * @param string $permit the permismsions the token has access to.
  */
  public static function createInternalAPI(user $user, string $permit)
  {
    global $ff_sql;

    if(!$user->getGroup()->can('mod_internalapi')) {
      return ff_return(false, 'misc-permission-denied');
    }

    if(!in_array($permit, self::PERMIT_TYPES)) {
      return ff_return(false, 'misc-invalid-permit-type');
    }

    $expiry = FF_TIME + (FF_YEAR * 4);// 4 years
    $token = self::getNewToken();

    $ff_sql->query("
      INSERT INTO `internalapi`
      (`id`, `enabled`, `date`, `expiry`, `user_id`, `permit`, `token`)
      VALUES (
        NULL,
        1,
        ". $ff_sql->quote(FF_TIME) .",
        ". $ff_sql->quote($expiry) .",
        ". $ff_sql->quote($user->getId()) .",
        ". $ff_sql->quote($permit) .",
        ". $ff_sql->quote($token) ."
      )
    ");

    // getting new instance
    $internalAPIInstance = self::getInternalAPIByToken($token);

    // audit log
    audits_admin_internalapinew::insert($user, $internalAPIInstance);

    return ff_return(true, [
      'instance' => $internalAPIInstance
    ], 'misc-success');
  }

  /**
  * Gets InternalAPI object by an ID
  * @param int $id
  */
  public static function getInternalAPIById(int $id)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT *
      FROM `internalapi`
      WHERE `id` = ". $ff_sql->quote($id) ."
    ", [
      'id' => 'int',
      'date' => 'int',
      'expiry' => 'int',
      'enabled' => 'bool',
      'user_id' => 'int'
    ]);

    if(!$res) {
      return null;
    }

    $internalAPIInstance = new self();
    $internalAPIInstance->linkByData($res);
    return $internalAPIInstance;
  }

  /**
  * Gets InternalAPI object by a token
  * @param string $token
  */
  public static function getInternalAPIByToken(string $token)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT *
      FROM `internalapi`
      WHERE `token` = ". $ff_sql->quote($token) ."
    ", [
      'id' => 'int',
      'date' => 'int',
      'expiry' => 'int',
      'enabled' => 'bool',
      'user_id' => 'int'
    ]);

    if(!$res) {
      return null;
    }

    $internalAPIInstance = new self();
    $internalAPIInstance->linkByData($res);
    return $internalAPIInstance;
  }

  /**
  * Gets a new token that doestn exist on the database
  */
  private static function getNewToken()
  {
    $token = cryptography::randomString(256);
    while(self::tokenExists($token)) {
      $token = cryptography::randomString(256);
    }
    return $token;
  }

  /**
  * Gets array of InternalAPI items
  */
  public static function getInternalAPIList()
  {
    global $ff_sql;

    $rows = $ff_sql->query_fetch_all("
      SELECT *
      FROM `internalapi`
    ", [
      'id' => 'int',
      'date' => 'int',
      'expiry' => 'int',
      'enabled' => 'bool',
      'user_id' => 'int'
    ]);

    $result = [];
    foreach ($rows as $row) {
      $tmp = new self();
      $tmp->linkByData($row);
      $result[] = $tmp;
    }

    return $result;
  }

  /**
  * Is there any internal api tokens in the table?
  */
  public static function isInternalAPIListEmpty()
  {
    global $ff_sql;
    $res = $ff_sql->fetch("SELECT EXISTS(SELECT 1 FROM `internalapi`) AS output", ['output' => 'int']);
    return $res['output'] != 1;
  }

  /**
  * Checks if a token exists
  */
  private static function tokenExists(string $token)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT count(1) as `cnt`
      FROM `internalapi`
      WHERE `token` = ". $ff_sql->quote($token) ."
    ", ['cnt' => 'int']);

    return $res['cnt'] > 0;
  }

  /**
  * Enables this InternalAPI
  * @param user $user
  */
  public function enable(user $user)
  {
    global $ff_sql;

    if(!$user->getGroup()->can('mod_internalapi')) {
      return false;
    }

    $this->enabled = true;

    $ff_sql->query("
      UPDATE `internalapi`
      SET
        `enabled` = ". $ff_sql->quote($this->enabled) ."
      WHERE
        `id` = ". $ff_sql->quote($this->id) ."
    ");

    audits_admin_internalapiedit::insert($user, $this);

    return true;
  }

  /**
  * Disables this InternalAPI
  * @param user $user
  */
  public function disable(user $user)
  {
    global $ff_sql;

    if(!$user->getGroup()->can('mod_internalapi')) {
      return false;
    }

    $this->enabled = false;

    $ff_sql->query("
      UPDATE `internalapi`
      SET
        `enabled` = ". $ff_sql->quote($this->enabled) ."
      WHERE
        `id` = ". $ff_sql->quote($this->id) ."
    ");

    audits_admin_internalapiedit::insert($user, $this);

    return true;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function getExpiry()
  {
    return $this->expiry;
  }

  public function getEnabled()
  {
    return $this->enabled;
  }

  public function getUserId()
  {
    return $this->userId;
  }

  public function getUser()
  {
    return user::getUserById($this->getUserId());
  }

  public function getPermit()
  {
    return $this->permit;
  }

  public function getToken()
  {
    return $this->token;
  }

  public function isExpired()
  {
    return $this->expiry < FF_TIME;
  }
}

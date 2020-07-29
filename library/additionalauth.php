<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\additionalauth.php
//
// ======================================


class additionalauth
{

  private static $methods = null;

  public static function getMethods()
  {
    if(self::$methods) {
      return self::$methods;
    }

    return self::$methods = [
      new additionalauth_email(),
      new additionalauth_hotp()
    ];
  }

  /**
  * Gets additional authentication method by it's name.
  *
  * @param string $name
  *   The autentication mathod's name you want.
  * @param bool $allowDisabled
  *   Whether or not we include disabled methods in the return pool.
  */
  public static function getMethodbyName(string $name, bool $allowDisabled = false)
  {
    self::getMethods();
    $name = strtolower($name);

    foreach(self::$methods as $method) {
      if(!$allowDisabled && !$method->enabled()) {
        // Treat it as though it doesnt exist.
        continue;
      }
      if(strtolower($method->getName()) === $name) {
        return $method;
      }
    }

    return false;
  }

  /**
  * Gets the additionalauth method linked with a user, if non, returns empty str.
  *
  * @param user $user
  *   The user whose additionalauth you want.
  */
  public static function getUserAuth(user $user)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT `method`
      FROM `user_auth`
      WHERE `user_id` = ". $ff_sql->quote($user->getId()) ."
    ");

    if(!$res) {
      return false;
    }

    return self::getMethodbyName($res['method']);
  }

  /**
  * Gets a users additional authentication context.
  *
  * @param user $user
  *   The users whose context you want.
  */
  public static function getUserAuthContext(user $user)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT `context`
      FROM `user_auth`
      WHERE `user_id` = ". $ff_sql->quote($user->getId()) ."
    ");

    if(!$res) {
      return false;
    }

    return json_decode($res['context'], true);
  }

  /**
  * Sets a users additional authentication context.
  *
  * @param user $user
  *   The users whose additional auth context you want to set.
  * @param array $context
  *   The context you want to se it to.
  */
  public static function setUserAuthContext(user $user, array $context)
  {
    global $ff_sql;

    if(!$ff_sql->query_fetch("
      SELECT `context`
      FROM `user_auth`
      WHERE `user_id` = ". $ff_sql->quote($user->getId()) ."
    ")) {
      // no context.
      return false;
    }

    $contextJson = json_encode($context);

    return $ff_sql->query("
      UPDATE `user_auth`
      SET `context` = ". $ff_sql->quote($contextJson) ."
      WHERE `user_id` = ". $ff_sql->quote($user->getId()) ."
    ") !== false;
  }
}

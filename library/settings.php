<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\settings.php
//
// ======================================


/**
* Class to handle user settings.
*/
class settings
{
  private static $_memorizeCache = [];

  private $user_id = 0;
  private $columns = [];

  const MANIPULATION_TYPE_HIDDEN = 'HIDDEN';
  const MANIPULATION_TYPE_AUTO = 'AUTO';
  const MANIPULATION_TYPE_MANUAL = 'MANUAL';

  const TYPE_INTEGER = 'INT';
  const TYPE_FLOAT = 'FLOAT';
  const TYPE_DOUBLE = 'FLOAT';
  const TYPE_STRING = 'STRING';
  const TYPE_ARRAY = 'ARRAY';

  const CONFIG_COLUMNS = [
    'user_id' => [
      'manipulation_type' => self::MANIPULATION_TYPE_HIDDEN,
      'type' => self::TYPE_INTEGER,
    ],

    'time_difference' => [
      'manipulation_type' => self::MANIPULATION_TYPE_AUTO,
      'type' => self::TYPE_INTEGER,
    ]
  ];

  public function linkById(int $user_id)
  {
    global $ff_sql;

    $res = $ff_sql->query_fetch("
      SELECT *
      FROM `user_settings`
      WHERE `user_id` = ". $ff_sql->quote($user_id) ."
    ", [
      'user_id' => 'int',
      'time_difference' => 'int'
    ]);

    if(!$res) {
      return false;
    }

    $this->user_id = $res['user_id'];
    foreach($res as $key => $value) {
      $this->columns[$key] = $value;
    }

    return true;
  }

  public static function getByUserId(int $user_id, bool $allowCache = true)
  {
    global $ff_context;
    if($allowCache && isset(self::$_memorizeCache[$user_id])) {
      return self::$_memorizeCache[$user_id];
    }
    $cache = $ff_context->getCache();
    $cacheKey = self::buildCacheKey($user_id);
    if($cached_object = $cache->get($cacheKey)) {
      return self::$_memorizeCache[$user_id] = $cached_object;
    }

    $settings = new settings();
    if(!$settings->linkById($user_id)) {
      return false;
    }

    $cache->store($cacheKey, $settings, FF_TIME + (FF_MINUTE * 30));
    return self::$_memorizeCache[$user_id] = $settings;
  }

  /**
  * Same as "self::getByUserId" but first parameter is a user object.
  */
  public static function getByUser($user, bool $allowCache = true)
  {
    return self::getByUserId($user->getId(), $allowCache);
  }

  /**
  * When a user registers, we need to insert his default user settings.
  */
  public static function initializeUserSettings(int $user_id, array $default_override = [])
  {
    global $ff_sql;
    $setDefault = function($param, $default) use (&$default_override) {
      if(!isset($default_override[$param])) {
        $default_override[$param] = $default;
      }
    };

    $default_override['user_id'] = $user_id;
    $setDefault('time_difference', 0);

    return $ff_sql->query("
      INSERT INTO `user_settings`
      (`user_id`, `time_difference`)
      VALUES (
        ". $ff_sql->quote($default_override['user_id']) .",
        ". $ff_sql->quote($default_override['time_difference']) ."
      )
    ") !== false;
  }

  /**
  * Sets a user options
  * @param string $key
  *   Option Key
  * @param mixed $value
  *   Value of option
  */
  public function setOption(string $key, $value, bool $ignoreTypeCheck = false)
  {
    global $ff_sql;

    if(!in_array($key, array_keys(self::CONFIG_COLUMNS))) {
      throw new Exception("Unexpected Key ${$key}");
    }

    if(!$ignoreTypeCheck && self::validateOption($key, $value)) {
      throw new Exception('Bad Type');
    }

    $insertValue = self::serializeInsert($key, $value);

    // NOTE: Don't store processed value in this class, sore what the database
    // has.
    $this->columns[$key] = $insertValue;

    // It may appear as though this is vulnerable, it's not. We are not
    // sanitizing $key variable when referencing it in the query, but we
    // validate that it's safe against an array of trusted keys at the beginning
    // of this function.
    $ff_sql->query("
      UPDATE user_settings
      SET
        {$key} = ". $ff_sql->quote($insertValue) ."
      WHERE
        user_id = ". $ff_sql->quote($this->user_id) ."
    ");

    // Updating the cached object.
    $this->selfCache();

    return true;
  }

  /**
  * Gets setting option
  * @param string $key
  */
  public function getOption(string $key)
  {
    if(!in_array($key, array_keys(self::CONFIG_COLUMNS))) {
      throw new Exception("Unexpected Key {$key}");
    }

    return self::castOption($key, $this->columns[$key]);
  }

  /**
  * Returns an array of settings and their values
  * @param mixed $filter The filter for the settings we want to yeild. Should
  * be a MANIPULATION_TYPE.
  * @param bool $includeMetadata Whether or not to include metadata about a column
  * @return array
  */
  public function getOptions($filter = null, bool $includeMetadata = true)
  {
    $ret = [];

    foreach ($this->columns as $column => $columnValue) {
      if(
        $filter === null ||
        $filter === self::CONFIG_COLUMNS[$column]['manipulation_type']
      ) {
        $ret[$column] = $columnValue;

        if($includeMetadata) {
          // Meta-data on the column
          $ret["__{$column}"] = self::CONFIG_COLUMNS[$column];
        }
      }
    }

    return $ret;
  }

  public static function buildCacheKey(int $user_id)
  {
    return ff_cacheKey(__CLASS__ . __FUNCTION__, [$user_id]);
  }

  private function selfCache()
  {
    global $ff_context;

    $cache = $ff_context->getCache();
    $cache->store(
      self::buildCacheKey($this->user_id),
      $this,
      FF_TIME + (FF_MINUTE * 30)
    );
  }

  private static function serializeInsert(string $key, $value)
  {
    $keyInfo = self::CONFIG_COLUMNS[$key];

    if($keyInfo['type'] === self::TYPE_ARRAY) {
      $value = json_encode($value, true);
    }

    return $value;
  }

  private static function castOption(string $key, $value)
  {
    $keyInfo = self::CONFIG_COLUMNS[$key];

    if($keyInfo['type'] === self::TYPE_INTEGER) {
      return intval($value);
    }

    if($keyInfo['type'] === self::TYPE_STRING) {
      return strval($value);
    }

    if($keyInfo['type'] === self::TYPE_ARRAY) {
      return json_decode($value, true);
    }

    if($keyInfo['type'] === self::TYPE_FLOAT) {
      return floatval($value);
    }

    throw new Exception('Unexpected Type');
  }

  private static function validateOption(string $key, $value)
  {
    // assume exists.
    $keyInfo = self::CONFIG_COLUMNS[$key];

    if($keyInfo['type'] === self::TYPE_INTEGER && is_int($value)) {
      return true;
    }

    if($keyInfo['type'] === self::TYPE_STRING && is_string($value)) {
      return true;
    }

    if($keyInfo['type'] === self::TYPE_ARRAY && is_array($value)) {
      return true;
    }

    if($keyInfo['type'] === self::TYPE_FLOAT && (is_float($value) || is_double($value))) {
      return true;
    }

    return false;
  }
}

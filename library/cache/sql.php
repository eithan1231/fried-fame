<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\cache\sql.php
//
// ======================================


class cache_sql implements cache_interface
{
  /**
  * Caches a variable.
  *
  * @param string $key
  *   The key of the variable you want to cache
  * @param mixed $value
  *   The type of this can be anything. This is what you want to cache.
  * @param int $expiry
  *   This is when the cached variable will expire.
  */
  public function store(string $key, $value, int $expiry)
  {
    global $ff_sql;

    $hashedKey = $this->hashKey($key);
    $valueSerialized = serialize($value);

    if(strlen($key) > 256) {
      return false;
    }

    if(strlen($valueSerialized) > 262144) {
      return false;
    }

    if($this->exists($key)) {
      if($stmt = $ff_sql->prepare("
        UPDATE `cache`
        SET
          `value` = ?,
          `expiry` = ?
        WHERE
          `key` = ". $ff_sql->quote($hashedKey) ."
      ")) {
        $stmt->bind_param(
          'ss',
          $valueSerialized,
          $expiry
        );
        $stmt->execute();
        $stmt->close();
      }
    }
    else {
      if($stmt = $ff_sql->prepare("
        INSERT INTO `cache`
        (`key`, `original_key`, `expiry`, `value`)
        VALUES (
          ?,
          ?,
          ?,
          ?
        )
      ")) {
        $stmt->bind_param(
          'ssis',
          $hashedKey,
          $key,
          $expiry,
          $valueSerialized
        );
        $stmt->execute();
        $stmt->close();
      }
    }

    return true;
  }

  /**
  * This will get a cached object.
  *
  * @param string $key
  *   The key of the cached object
  * @return mixed On failure, null, otherwise the cached value.
  */
  public function get(string $key)
  {
    global $ff_sql;

    $hashedKey = $this->hashKey($key);

    $result = $ff_sql->query_fetch_all("
      SELECT `original_key`, `value`
      FROM `cache`
      WHERE
        `key` = ". $ff_sql->quote($hashedKey) ." AND
        `expiry` > ". $ff_sql->quote(FF_TIME) ."
    ");

    if(!$result) {
      return null;
    }

    foreach($result as $value) {
      if($value['original_key'] == $key) {
        return unserialize($value['value']);
      }
    }

    return null;
  }

  /**
  * this will  check if a cached variable exists.
  *
  * @param string $key
  *   The key of the cached variable, we want to see exists.
  */
  public function exists(string $key)
  {
    global $ff_sql;
    return $ff_sql->query_fetch("
      SELECT count(1) as cnt
      FROM `cache`
      WHERE `key` = ". $ff_sql->quote($this->hashKey($key)) ."
    ")['cnt'] > 0;
  }

  /**
  * Deletes cache record.
  */
  public function delete($key)
  {
    global $ff_sql;
    return $ff_sql->query_fetch("
      DELETE FROM `cache`
      WHERE `key` = ". $ff_sql->quote($this->hashKey($key)) ."
    ")['cnt'] > 0;
  }


  /**
  * Gets the name of the caching engine.
  * @return string
  */
  public function getName()
  {
    return 'sql';
  }

  /**
  * Whether or not it's an in-memory database. Some things shouldn't be in memory,
  * or should only be in memory.
  * @return bool
  */
  public function isInMemory()
  {
    return false;
  }


	/**
	* Purges all cache
	* @return bool
	*/
	public function purge()
	{
		global $ff_sql;
		return $ff_sql->query("TRUNCATE `cache`") !== false;
	}

  private function hashKey(string $key)
  {
    return hash('sha256', $key);
  }
}

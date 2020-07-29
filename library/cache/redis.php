<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\cache\redis.php
//
// ======================================


class cache_redis implements cache_interface
{
  static public function initializeRedisConnection()
  {
    global $ff_redis, $ff_config;

    if(!isset($ff_redis)) {
      $redisConfig = $ff_config->get('cache-redis-config');

      $hostname = (isset($redisConfig['hostname'])
        ? $redisConfig['hostname']
        : 'localhost'
      );
      $port = (isset($redisConfig['port'])
        ? intval($redisConfig['port'])
        : 6379
      );
      $timeout = (isset($redisConfig['timeout'])
        ? $redisConfig['timeout']
        : 3
      );
      $password = (isset($redisConfig['password'])
        ? $redisConfig['password']
        : null
      );

      $ff_redis = new Redis();
      $ff_redis->connect(
        $hostname,
        $port,
        $timeout
      );

      if($password && !$ff_redis->auth($password)) {
        throw new Exception("Redis failed to authenticate {$hostname}");
      }

      return true;
    }
    else if($ff_redis->isConnected()) {
      return true;
    }
    return false;
  }

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
    global $ff_redis;
    if(!self::initializeRedisConnection()) { return false; }

    $key = self::cacheKey($key);
    $valueSerialized = serialize($value);

    return $ff_redis->setEx(
      $key,
      $expiry - FF_TIME,// It requires TTL in seconds.
      $valueSerialized
    );
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
    global $ff_redis;
    if(!self::initializeRedisConnection()) { return false; }

    $value = $ff_redis->get(self::cacheKey($key));

    if(!$value) {
      return false;
    }

    return unserialize($value);
  }

  /**
  * this will  check if a cached variable exists.
  *
  * @param string $key
  *   The key of the cached variable, we want to see exists.
  */
  public function exists(string $key)
  {
    global $ff_redis;
    if(!self::initializeRedisConnection()) { return false; }
    return $ff_redis->exists(self::cacheKey($key)) > 0;
  }

  /**
  * Deletes cache record.
  */
  public function delete($key)
  {
    global $ff_redis;
    if(!self::initializeRedisConnection()) { return false; }
    return $ff_redis->del(self::cacheKey($key)) > 0;
  }

  /**
  * Gets the name of the caching engine.
  * @return string
  */
  public function getName()
  {
    return 'redis';
  }

  /**
  * Whether or not it's an in-memory database. Some things shouldn't be in memory,
  * or should only be in memory.
  * @return bool
  */
  public function isInMemory()
  {
    return true;
  }

	/**
	* Purges all cache
	* @return bool
	*/
	public function purge()
	{
    // Due to key value nature, it's hard to delete like this.
    throw new Exception('Not Implemented');
	}

  private static function cacheKey($key)
  {
    return "fried-fame:{$key}";
  }
}

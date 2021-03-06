<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\cache\none.php
//
// ======================================


class cache_none implements cache_interface
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
    return false;
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
    return false;
  }

  /**
  * Deletes cache record.
  */
  public function delete($key)
  {
    return true;
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
  * Gets the name of the caching engine.
  * @return string
  */
  public function getName()
  {
    return 'none';
  }

	/**
	* Purges all cache
	* @return bool
	*/
	public function purge()
	{
		return false;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\cache.php
//
// ======================================


class cache
{
  private static $caches = null;

  /**
  * Returns an array of all the registered cache classes
  */
  public static function getCaches()
  {
    if(self::$caches) {
      return self::$caches;
    }

    return self::$caches = [
      new cache_none(),
      new cache_sql(),
      new cache_redis(),
    ];
  }

  public static function getCacheByName(string $name)
  {
    $name = strtolower($name);
    $caches = self::getCaches();
    foreach($caches as $cache) {
      if(strtolower($cache->getName()) === $name) {
        return $cache;
      }
    }
    return null;
  }
}

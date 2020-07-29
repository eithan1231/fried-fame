<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\postlimiter.php
//
// ======================================


/**
* This is a basic class that will restrict post requests. This will need to be
* manually configured per post handler.
*/
class postlimiter
{
  /**
  * The maximum timespan you can use.
  *
  * NOTE: You should NOT change this.
  *
  * @var int
  */
  private static $maximumTimespan = 60 * 60 * 24;

  /**
  * Inserts a request.
  *
  * @param string $name
  *   Name of the request.
  * @param request $request
  *   Information about the request.
  */
  public static function insertRequest(string $name, request &$request)
  {
    global $ff_sql;

    if($request->isCLI()) {
      // Let's not limit CLI requests... they are more than likely doing tests,
      // or something of importance. RIP if it's testing this class. jaja.
      return;
    }

    if(strlen($name) > 32) {
      throw new Exception('$name exceeds length limit');
    }

    $ff_sql->query("
      INSERT INTO `postlimiter`
      (`name`, `date`, `ip`)
      VALUES (
        ". $ff_sql->quote($name) .",
        ". $ff_sql->quote(FF_TIME) .",
        ". $ff_sql->quote($request->getIp()) ."
      )
    ");
  }

  /**
  * Checks if a user is exceeding the request limit.
  *
  * @param string $name
  *   Name of the request.
  * @param request $request
  *   Information about the request.
  * @param int $limit
  *   The limit of requests within $timespan.
  * @param int $timespan
  */
  public static function exceedsRequestLimit(string $name, request &$request, int $limit, int $timespan = 60 * 15, &$requestCount = 0)
  {
    global $ff_sql;

    if($request->isCLI()) {
      // Let's not limit CLI requests... they are more than likely doing tests,
      // or something of importance. RIP if it's testing this class. jaja.
      return false;
    }

    if(ff_isDevelopment()) {
      // Ideally we dont want to get rate limited while developing.
      return false;
    }

    if(strlen($name) > 32) {
      throw new Exception('$name exceeds length limit');
    }

    if($timespan < 0 || $timespan > self::$maximumTimespan) {
      trigger_error('Timespan parameter excueeded maximum timespan, changed to maximum');
      $timespan = self::$maximumTimespan;
    }

    $res = $ff_sql->query_fetch("
      SELECT
        COUNT(1) as cnt
      FROM `postlimiter`
      WHERE
        `name` = ". $ff_sql->quote($name) ." AND
        `ip` = ". $ff_sql->quote($request->getIp()) ." AND
        `date` > ". $ff_sql->quote(FF_TIME - $timespan) ."
    ", [
      'cnt' => 'int'
    ]);

    if(!$res) {
      return false;
    }

    return ($requestCount = $res['cnt']) > $limit;
  }
}

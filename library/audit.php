<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audit.php
//
// ======================================


class audit
{
  private static $cache_audits = null;

  public static function getAudits()
  {
    if (self::$cache_audits) {
      return self::$cache_audits;
    }

    return self::$cache_audits = [
      new audits_admin_approvereview(),
			new audits_admin_deletereview(),
			new audits_admin_undeletereview(),
      new audits_admin_newplan(),
			new audits_admin_setphrase(),
			new audits_admin_changeusergroup(),
			new audits_admin_closesupportticket(),
			new audits_admin_opensupportticket(),
			new audits_admin_deletesupportticket(),
			new audits_admin_undeletesupportticket(),
			new audits_admin_newffrpcnode(),
			new audits_admin_deleteffrpcnode(),
			new audits_admin_announcement(),
			new audits_admin_sendemail(),
			new audits_admin_uploadpackage(),
      new audits_admin_publicsupportstatus(),
      new audits_admin_knowbasepost();
    ];
  }

  public static function getAuditByName(string $name)
  {
    $name = strtolower($name);
    $audits = self::getAudits();
    foreach ($audits as $audit) {
      if(strtolower($audit->getName()) === $name) {
        return $audit;
      }
    }
    return null;
  }

  public static function renderSnippetFromName(string $name, array $context)
  {
    return self::getAuditByName($name)->renderSnippet($context);
  }

	private static function applyAuditHistoryFilter(array $filter)
	{
		global $ff_sql;
		$where = [];
		if(isset($filter['user_id'])) {
			$where[] = '`user_id` ='. $ff_sql->quote($filter['user_id']);
		}
		return $where;
	}

  public static function getAdminAuditHistory(int $offset, int $count, array $filter = [])
  {
    global $ff_sql;

    $where = self::applyAuditHistoryFilter($filter);
		$order = '';

		if(!isset($filter['above_id']) || $filter['above_id']) {
			$where[] = '`admin_audit_logs`.`id` > '. $ff_sql->quote($offset);
		}
		else {
			$where[] = '`admin_audit_logs`.`id` <= '. $ff_sql->quote($offset);
			// NOTE: This order is undone at the end
			$order = "ORDER BY `admin_audit_logs`.`id` DESC";
		}

    $where = 'WHERE '. implode(' AND ', $where);

    $res = $ff_sql->query_fetch_all("
			SELECT
				`admin_audit_logs`.`id` AS id,
				`admin_audit_logs`.`user_id`,
				`users`.`username` AS user_name,
				`users`.`group_id` AS user_group_id,
				`admin_audit_logs`.`date` AS date,
				`admin_audit_logs`.`name` AS name,
				`admin_audit_logs`.`value` AS value
			FROM
				`admin_audit_logs`
			INNER JOIN
				`users`
			ON
				`users`.`id` = `admin_audit_logs`.`user_id`
			{$where}
			{$order}
      LIMIT ". $ff_sql->quote($count) ."
    ", [
      'id' => 'int',
      'user_id' => 'int',
      'user_group_id' => 'int',
      'date' => 'int',
    ]);

    if(!$res) {
      return false;
    }

		if(!isset($filter['above_id']) || !$filter['above_id']) {
			// putting order as it should be.
			$res = array_reverse($res);
		}

    foreach($res as &$value) {
      $value['value'] = json_decode($value['value'], true);
    }

    return $res;
  }

	public static function getAdminAuditHistoryCount(array $filter = [], bool $allowCache = true)
	{
		global $ff_sql, $ff_context;

		// Getting cache prequisits
		$cache = $ff_context->getCache();
		$cacheParameters = [];
		foreach ($filter as $key => $value) {
			$cacheParameters = $key .'='. $value;
		}
		$cacheKey = ff_cacheKey(__CLASS__ . __FUNCTION__, $cacheParameters);

		// Getting cached object
		if($allowCache && $cacheData = $cache->get($cacheKey)) {
			return $cacheData;
		}

		// No cached object.

		// Building where for query
		$where = self::applyAuditHistoryFilter($filter);
		$where = (count($where) === 0
      ? ''
      : ' WHERE '. implode(' AND ', $where)
    );

		$res = $ff_sql->query_fetch("
			SELECT
				count(1) AS cnt
			FROM `admin_audit_logs`
			$where
		");

		// Cache it for 6 hours.
		$cache->store($cacheKey, $res['cnt'], FF_TIME + (FF_HOUR * 6));

		return $res['cnt'];
	}
}

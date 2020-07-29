<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\analytics.php
//
// ======================================


/*
List of queries for generating analytics


---- Fetches all nodes, and their bandwidth usage. ----
SELECT
	vpn_nodes.id,
	vpn_nodes.country,
	vpn_nodes.city,
	vpn_nodes.hostname,
	vpn_nodes.ip,
	sum(connections.data_sent) as sent_data,
	sum(connections.data_received) as received_data,
	sum(connections.data_received + connections.data_sent) as total_data
FROM
	connections
INNER JOIN
	vpn_nodes
ON
	connections.node_id = vpn_nodes.id
GROUP BY
	connections.node_id
ORDER BY total_data desc;

*/
class analytics
{
	/**
	* Used for generating a graph. Basically returns an array, each 'key' of the
	* array is set to a date. The value represents how many connections there were
	* on that specfic date.
	*/
	public static function getNodeConnection(int $nodeId, int $timespan = FF_WEEK, $precision = FF_DAY)
	{
		global $ff_sql, $ff_context;

		$cache = $ff_context->getCache();
		$cacheKey = ff_cacheKey(__CLASS__ . __FUNCTION__, func_get_args());
		if($obj = $cache->get($cacheKey)) {
			return $obj;
		}

		// Round to the nearest day, with default settings.
		$beginning = intval(ceil((FF_TIME  - $duration) / $precision) * $precision);
		$return = [];

		for($i = 0; $i <= $timespan; $i += $percision) {
			$queryTime = intval($beginning + $i);
			$res = $ff_sql->query_fetch("
				SELECT
					count(1) as `active_connections`
				FROM `connections`
				WHERE
					node_id = $nodeId AND
					connect_date < $queryTime AND
					disconnect_date > $queryTime
			");

			$return[$queryTime] = $res['active_connections'];
		}

		$cache->store($cacheKey, $returnValue, FF_TIME + $precision);

		return $return;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \maintenance.php
//
// ======================================


define('FF_SQL_IGNORE_LONG_QUERY', 1);

/**
* After upgrading scripts, run this script to upgrade database.
*/
echo "Fried-Fame maintenance script\n\n";

// Main includes.
require_once __DIR__ .'/constants.php';
require_once FF_LIB_DIR .'/autoloader.php';
autoloader::load('functions');

// Loading development and production things.
$ff_config = new config(FF_WORK_DIR .'/config.php');

$ff_request = new request();
$ff_sql = new sql(
	$ff_config->get('mysql.username'),
	$ff_config->get('mysql.password'),
	$ff_config->get('mysql.hostname'),
	$ff_config->get('mysql.database')
);
$ff_context = new context(true);

if(!$ff_request->isCLI()) {
	die('Must run in CLI mode');
}

if(!$ff_sql->ping()) {
	die('Unable to initiate database connection');
}

function main($argv, $argc)
{
	global $ff_context, $ff_sql, $ff_config;
	if($argc <= 0) {
		$argv[0] = '';
	}
	switch ($argv[0]) {
		case 'purge-cache': {
			print('Purging cache');
			if($ff_context->getCache()->purge()) {
				print('Cache Purged.');
			}
			else {
				print('Failed to purge cache');
			}

			break;
		}

		default: die('Unexpected parameter');
	}
}

main(array_slice($argv, 1), $argc - 1);

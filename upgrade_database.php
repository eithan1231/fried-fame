<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \upgrade_database.php
//
// ======================================


define('FF_SQL_IGNORE_LONG_QUERY', 1);

/**
* After upgrading scripts, run this script to upgrade database.
*/
echo "Fried-Fame database upgrade\n\n";

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

if(!$ff_sql->connect()) {
	die('bad db');
}

$successful = true;

function doUpgrade($successMsg, $failureMsg, $query)
{
	global $ff_sql, $successful;
	$success = true;

	try {
		$success = $ff_sql->query($query) != false;
	}
	catch(Exception $ex) {
		$success = false;
	}


	if($success) {
		echo $successMsg ."\r\n";
	}
	else {
		$successful = false;
		echo '==========================================';
		echo "\r\n";
		echo $failureMsg;
		echo "\r\n\r\n";
		echo substr($query, 0, 500);
		echo "\r\n\r\n\r\n";
	}
}

/**
* Adds a column to a table.
* @param string $columnName
*		Name of new column
* @param string $type
*		Type. IE: "varchar (32)" or "BOOLEAN"
* @param string $table
*		Table in which this will be created.
* @param string $queryExtend
*		Appended to end of query. Set defaults, indexes, etc.
*/
function addDBTableColumn(string $columnName, string $type, string $table, string $queryExtend = '')
{
	doUpgrade(
		"Added {$columnName} column to the table {$table}",
		"Failed to add {$columnName} column to the table {$table}",
		"ALTER TABLE `{$table}` ADD `{$columnName}` {$type} NOT NULL $queryExtend"
	);
}

/* =============================================================================
* ==============================================================================
* Start of upgrade script (parts you program)
* ==============================================================================
*/


/* =============================================================================
* ==============================================================================
* End of upgrade script (parts you program)
* ==============================================================================
*/

if($successful) {
	die("Script execution completed successfully.\n\n");
}
else {
	die("Script execution completed with errors. Please see the errors above and manually correct them!\n\n");
}

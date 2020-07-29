<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \constants.php
//
// ======================================


define('FF_VERSION', 0.121);

define('FF_DEVELOPMENT', (isset($_SERVER['FF_DEVELOPMENT']) && $_SERVER['FF_DEVELOPMENT'] == '1'));

define('FF_WORK_DIR', __DIR__);
define('FF_LIB_DIR', FF_WORK_DIR .'/library');
define('FF_UPLOAD_DIR', FF_WORK_DIR .'/library/uploads');
define('FF_ERR_DIR', FF_WORK_DIR .'/errors');// Directory errors are logged
define('FF_LOG_DIR', FF_WORK_DIR .'/logs');// Directory logs are stored
define('FF_CHARSET', ini_get("default_charset"));
define('FF_TIME', time());// Unix time, calling time() all the time is ugly.
define('FF_DATE', FF_TIME);// kept forgetting whether it was time or date.
define('FF_MICROTIME', microtime(true));
define('FF_UNSUPPORTED_UA', ['Trident']);

define('FF_MINUTE', 60);
define('FF_HOUR', FF_MINUTE * 60);
define('FF_DAY', FF_HOUR * 24);
define('FF_WEEK', FF_DAY * 7);
define('FF_MONTH', FF_DAY * 30);
define('FF_YEAR', FF_DAY * 365);

define('FF_BYTE', 1);
define('FF_KB', FF_BYTE * 1024);
define('FF_MB', FF_KB * 1024);
define('FF_GB', FF_MB * 1024);
define('FF_TB', FF_GB * 1024);

// {UNIX_TIME}_{NANOSECOND_OF_UNIX}_{RANDOM}
define('FF_REQUEST_ID', str_replace('.', '_', microtime(true)) .'_'. mt_rand(10000000, 99999999));

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \index.php
//
// ======================================


date_default_timezone_set('UTC');
header_remove('x-powered-by');
ini_set('display_errors', 0);


// Main includes.
require_once __DIR__ .'/constants.php';
require_once FF_LIB_DIR .'/autoloader.php';
autoloader::load('functions');
handlers_exception::register();
handlers_error::register();


$ff_config = new config(FF_WORK_DIR .'/config.php');
$ff_request = new request();
$ff_response = new response();
$ff_sql = new sql(
	$ff_config->get('mysql.username'),
	$ff_config->get('mysql.password'),
	$ff_config->get('mysql.hostname'),
	$ff_config->get('mysql.database')
);
$ff_router = new router($ff_config->get('work-url'));


// Registering all routes.
$ff_router->registerSpecial('404', new routes_special_404());
$ff_router->registerSpecial('db', new routes_special_db());
$ff_router->register(new routes_asset());
$ff_router->register(new routes_landing());
$ff_router->register(new routes_manifest());
$ff_router->register(new routes_tos());
$ff_router->register(new routes_pp());
$ff_router->register(new routes_login());
$ff_router->register(new routes_register());
$ff_router->register(new routes_recovery());
$ff_router->register(new routes_post());
$ff_router->register(new routes_emailverif());
$ff_router->register(new routes_mlt());// Mailing List Terms
$ff_router->register(new routes_redirect());
$ff_router->register(new routes_containers_landing());
$ff_router->register(new routes_robots());
$ff_router->register(new routes_contact());
$ff_router->register(new routes_badpages());
$ff_router->register(new routes_credits());
$ff_router->register(new routes_ping());// Used for status page
$ff_router->register(new routes_status());
$ff_router->register(new routes_sitemap());
$ff_router->register(new routes_faq());

// internal api endpoints
$ff_router->register(new routes_internalapi_ffrpc_new());
$ff_router->register(new routes_internalapi_ffrpc_list());


// Documentation / Knowledge Base
$ff_router->register(new routes_knowbase_landing());
$ff_router->register(new routes_knowbase_category());
$ff_router->register(new routes_knowbase_article());

// User CP pages
$ff_router->register(new routes_cp_landing());
$ff_router->register(new routes_cp_additionalauth());
$ff_router->register(new routes_cp_reauth());
$ff_router->register(new routes_cp_settings());
$ff_router->register(new routes_cp_settings_email());
$ff_router->register(new routes_cp_settings_password());
$ff_router->register(new routes_cp_history_email());
$ff_router->register(new routes_cp_history_password());
$ff_router->register(new routes_cp_support_new());
$ff_router->register(new routes_cp_support_view());
$ff_router->register(new routes_cp_support_landing());
$ff_router->register(new routes_cp_payments_method());
$ff_router->register(new routes_cp_payments_view());
$ff_router->register(new routes_cp_payments_list());
$ff_router->register(new routes_cp_payments_plans());
$ff_router->register(new routes_cp_giftcard());
$ff_router->register(new routes_cp_announcements());
$ff_router->register(new routes_cp_package_download());
$ff_router->register(new routes_cp_review());

// CP Installation Guides
$ff_router->register(new routes_cp_install_landing());
$ff_router->register(new routes_cp_install_ios());
$ff_router->register(new routes_cp_install_android());
$ff_router->register(new routes_cp_install_windows());
$ff_router->register(new routes_cp_install_osx());
$ff_router->register(new routes_cp_install_linux());

// Control panel payment processing
$ff_router->register(new routes_cp_payments_paypal_cancel());
$ff_router->register(new routes_cp_payments_paypal_success());

// Mod items within CP page..
$ff_router->register(new routes_cp_mod_audit());
$ff_router->register(new routes_cp_mod_feedback());
$ff_router->register(new routes_cp_mod_language_edit());
$ff_router->register(new routes_cp_mod_language_list());
$ff_router->register(new routes_cp_mod_language_new());
$ff_router->register(new routes_cp_mod_language_outdated());
$ff_router->register(new routes_cp_mod_language_unfound());
$ff_router->register(new routes_cp_mod_user_landing());
$ff_router->register(new routes_cp_mod_user_find());
$ff_router->register(new routes_cp_mod_user_manage());
$ff_router->register(new routes_cp_mod_group_landing());
$ff_router->register(new routes_cp_mod_support_list());
$ff_router->register(new routes_cp_mod_support_view());
$ff_router->register(new routes_cp_mod_giftcard());
$ff_router->register(new routes_cp_mod_ffrpc_landing());
$ff_router->register(new routes_cp_mod_ffrpc_new());
$ff_router->register(new routes_cp_mod_announcement());
$ff_router->register(new routes_cp_mod_package_new());
$ff_router->register(new routes_cp_mod_package_landing());
$ff_router->register(new routes_cp_mod_nodes_landing());
$ff_router->register(new routes_cp_mod_nodes_manage());
$ff_router->register(new routes_cp_mod_nodes_new());
$ff_router->register(new routes_cp_mod_review());
$ff_router->register(new routes_cp_mod_internalapi_new());
$ff_router->register(new routes_cp_mod_internalapi_list());
$ff_router->register(new routes_cp_mod_internalapi_edit());

// WebContainer automatic detector and redirecter.
$ff_router->register(new routes_containers_redirect());

// Windows WebContainer.
$ff_router->register(new routes_containers_windows_landing());
$ff_router->register(new routes_containers_windows_login());
$ff_router->register(new routes_containers_windows_additionalauth());
$ff_router->register(new routes_containers_windows_reauth());

// WinNative
$ff_router->register(new routes_containers_winnative_api_list());
$ff_router->register(new routes_containers_winnative_api_context());
$ff_router->register(new routes_containers_winnative_api_connect());
$ff_router->register(new routes_containers_winnative_api_heartbeet());
$ff_router->register(new routes_containers_winnative_api_authenticate());
$ff_router->register(new routes_containers_winnative_api_openvpnconfig());

// NixNative
$ff_router->register(new routes_containers_nixnative_api_list());
$ff_router->register(new routes_containers_nixnative_api_context());
$ff_router->register(new routes_containers_nixnative_api_connect());
$ff_router->register(new routes_containers_nixnative_api_heartbeet());
$ff_router->register(new routes_containers_nixnative_api_authenticate());
$ff_router->register(new routes_containers_nixnative_api_openvpnconfig());

// Payments
$ff_router->register(new routes_payments_gateways_paypal_ipn());
$ff_router->register(new routes_payments_gateways_paypal_redirect());

if($ff_sql->connect()) {
	// Creating FF context (requires MySQL)
	$ff_context = new context();

	// At the end of this block of code, we may not want to run the router, as
	// we may be printing output without it.
	$runRouter = true;

	// Check errors
	$errors = $ff_request->check();
	if(count($errors) > 0) {
		$runRouter = false;
		$ff_response->setHttpStatus(400);
		$ff_response->setHttpHeader('Content-type', 'text/plain');

		$ff_response->appendBody('Errors Experienced:');
		foreach ($errors as $error) {
			$ff_response->appendBody("\n$error");

			// Log error to log file
			$ff_context->getLogger()->error("Error: $error", $error);
		}
	}

	// HTTP Upgrade
	if(
		$ff_config->get('secure-server') &&
		$ff_request->getHeader('upgrade-insecure-requests') == '1' &&
		!$ff_request->isSecure()
	) {
		$runRouter = false;

		$redirectPath = "https://{$ff_request->getHeader('host')}{$ff_request->getPath()}";
		if(strlen($ff_request->getQuery()) > 0) {
			$redirectPath .= '?'. $ff_request->getQuery();
		}

		$ff_response->redirect($redirectPath);
	}

	// Hostname redirect
	$hostnameRedirects = $ff_config->get('hostname-redirects');
	$httpHostHeader = $ff_request->getHeader('host');
	if(isset($hostnameRedirects[$httpHostHeader])) {
		$runRouter = false;

		$redirectPath = "//{$hostnameRedirects[$httpHostHeader]}{$ff_request->getPath()}";
		if($ff_request->getQuery()) {
			$redirectPath .= '?'. $ff_request->getQuery();
		}

		$ff_response->redirect($redirectPath);
	}

	// run routes.
	if ($runRouter) {
		$ff_router->run();
	}
}

// Finishing off the response.
$ff_response->setHttpHeader(
	'X-Memory-Usage',
	ff_getSizeAsVisual(memory_get_peak_usage())
);
$ff_response->setHttpHeader(
	'X-Duration',
	strval(round((microtime(true) - FF_MICROTIME) * 1000, 2)) .'ms'
);
$ff_response->setHttpHeader(
	'X-Powered-By',
	'github:eithan1231/fried-fame'
);
$ff_response->setHttpHeader('X-Query-Count', $ff_sql->queryCount());
$ff_response->setHttpHeader('X-UA-Compatible', 'IE=edge');
$ff_response->setHttpHeader('X-Frame-Options', 'sameorigin');
$ff_response->setHttpHeader('X-Request-Id', FF_REQUEST_ID);
$ff_response->flush();

// Saving log
if(isset($ff_context)) {
	$ff_context->getLogger()->commit();
}

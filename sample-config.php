<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \sample-config.php
//
// ======================================


/**
* All the configuration variables.
*/
return [
	// WARNING: If you're in production, this being true WILL break functionality.
	// For example, Paypal will use Sandbox API calls, rendering payments
	// unprocessable.
	'development' => false,


	// ===========================================================================
	// MySQL Credentials

	'mysql.username' => "",
	'mysql.password' => "",
	'mysql.database' => "",
	'mysql.hostname' => "",


	// ===========================================================================
	// General Inforamtion

	# The name of this project.. Try not to include grammer in this ('!', '?', ':', etc.)
	'project-name' => "Example VPN",

	# Short version of your projects name. Try make this shorter than 10 characters.
	'project-name-short' => "eVPN",

	# The directory we are working in relitive to the request path...
	# If this project is in '/var/www/html/fried-fame', that would more than likely
	# mean we are in the '/fried-fame' directory.
	'work-url' => "/",

	# Whether or not we are in cloudflare-mode
	'cloudflare-mode' => false,

	# Whether or not we're behind a nginx reverse proxy
	#
	# set http header 'X-Client-IP' to the client's IP.
	# set http header 'https' to on if a secure connection was used, otherwise 0.
	'nginx-reverse-proxy-mode' => false,

	# This will stop PHP from delivering assets and palm it off to the web-server.
	'proxy-asset-bypass' => false,

	# Name of the session cookie
	'session-cookie' => "ff-session",

	# Session duration in seconds, default is 4 years~.
	'session-valid-duration' => FF_YEAR * 4,

	# Default language.
	'session-default-language' => "en",

	# The primary hostname. If you have this configured on a public domain, set this
	# to that domain. This is used in routing, and privacy policy templater, and more
	# places, but those are the main
	'primary-hostname' => "localhost",

	# List of hostnames that are considered trusted. seperate them with a comma.
	'trusted-hostnames' => ['localhost', '127.0.0.1'],

	# Hostname redirects. Can be used for redirecting non-www domains to www
	# domains, see below.
	'hostname-redirects' => [
		# Redirect example.com to www.example.com.
		# NOTE: Things wont function correctly if both aren't considered trusted
		# hostnames.
		'example.com' => 'www.example.com'
	],

	# All the Nix* clients (OSX, Debian, Ubuuntu, CentOS, etc) host a local server
	# which is the control panel. This is the configuration for that.
	'nix' => [

		# A hostname that points at 127.0.0.1 or [1::]. It may just be more
		# appealing to users for it to show 'xx.example.com' rather than 'localhost'.
		'hostname' => 'localhost',

		# Port on which the local HTTP server operates.
		'port' => 3422,
	],

	# The vultr API key. This will be used for auto-scaling, and things as such.
	'vultr-api-key' => "removed",

	# This is for the theme-color meta tag. Recommend to leave this as default.
	'theme-color' => "#f8f9fa",

	# some sidebar crap
	'cookie-sidebar-hidden' => "ff-sidebar",
	'sidebar-width' => "220px",

	# Whether or not we support html support posts. I would highly advise this to
	# be enabled. If you need to disable it (security reasons), escaped HTML code
	# WILL be displayed to users. While we will try and remove the escaped HTML,
	# there's only so much we can do.
	'allow-html-support-posts' => true,

	# Whether or not we're hiding behind a secure connection.
	'secure-server' => false,

	# Error Pushing. If an error, or exception happens, this is a string-list of
	# emails that the error description will be sent to. Example "xx@xx.xx, test@xx.xx"
	'error-pushing-addresses' => 'user@example.com',

	# the type of logger we want to use
	# file:
	#		Logs in files
	# none:
	#		No logs are kept
	'logger' => 'file',

	# Status page URL (generally placed off-site in an iframe)
	# set to a empty string or false to disable status page.
	# NOTE: Try make sure it's on the same hostname. use {domain} for the current
	# domain
	'status-page' => false,


	// ===========================================================================
	// Knowledge base

	# Enable/Disable the knowledge base.
	'knowbase-enabled' => true,


	// ===========================================================================
	// Analytics

	# enable/disable google analytics
	'google-analytics' => false,
	'google-analytics-tracking-id' => '--test--',

	# Analytics engine Countly.
	'countly-analytics' => false,
	'countly-analytics-api-key' => '',
	'countly-analytics-endpoint' => '',



	// ===========================================================================
	// Captcha

	# The current recaptcha mode.
	# Modes (Case SENSITIVE):
	#		recaptcha2: reCAPTCHA v2 mode, you will need to set all configs that start with recaptcha2
	#		none: No captcha.
	'catpcha-mode' => "none",

	# reCAPTCHA v2 site key - used for client/google communications
	'recaptcha2-site-key' => "",

	# reCAPTCHA v2 secret key - used for site/google communications
	'recaptcha2-secret-key' => "",


	// ===========================================================================
	// Payments

	// Information for paypal
	'paypal-enabled' => false,
	'paypal-merchant-id' => '',
	'paypal-email-address' => '',


	// ===========================================================================
	// Cache

	# The active cache mode, additional configuring may be required.
	# possible values:
	#	 sql: This stores cache objects in database. Not ideal, but speeds some
	#		things up.
	#	 redis: Stores all cache objects in redis cache. Great speed, but consumes
	#		memory.. possibly a lot..
	#	 none: No cache. This is not recommended, but will work fine. Expect page
	#		load times to increase.
	'cache-mode' => "redis",

	# Redis caching system configuration
	'cache-redis-config' => [
		'hostname' => '127.0.0.1'
	],


	// ===========================================================================
	// smtp
	// NOTE: Not used anymore. Though we still may add support in future.
	'smtp-hostname' => "smtp.gmail.com",
	'smtp-port' => 465,
	'smtp-security' => "tls",
	'smtp-username' => "username",
	'smtp-password' => "password",


	// ===========================================================================
	// Verification

	# Whether or not we want email verification.
	'email-verification-enabled' => true,

	# Group assigned to user before email verification (pending verification)
	# NOTE: If email verification is turned of, we will default to this group.
	'group-pre-email-verification' => 1,

	# Group Assigned to user after email verification (he's verified)
	'group-post-email-verification' => 2,


	// ===========================================================================
	// Security

	# The cost for the password_hash function. Changing this may impact server
	# performance. Recommended to leave at default.
	'password-hash-cost' => 8,

	// ===========================================================================
	// One-time setup.

	# Password hash pepper. This MUST NOT change after its initial configuration.
	# Changing this will break ALL users passwords, making EVERYONE unable to login.
	'pepper' => '!!! constant random string !!!',

	# Whenever you access a application, and it needs to request from the server,
	# this xsrc token is needed to be set in "x-src" header, otherwise it will act
	# as though you are not accessing from an application. This doesn't need to be a
	# secret, you could probably leave it black. The primary objective of this token
	# is to prevent normal web access.
	'xsrc' => "FF X-Src Validation",
];

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\special\unsupportedbrowser.php
//
// ======================================


// Getting globals
global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<meta name="robots" content="noindex, nofollow" />
		<title>Unsupported browser</title>

		<?php snippets_cssincl::render() ?>
	</head>
	<body>
		<div class="container">
			<h1 class="text-center">Unsupported Browser</h1>
			<p class="text-center">
				Hello there!
				<br /><br />
				The web browser you are using to load this page is not supported. Please consider downloading <a href="https://www.google.com/chrome/">Chrome</a>, <a href="https://www.mozilla.org/en-US/firefox/new/">Firefox</a>, or a browser with better compatibility.
			</p>
		</div>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
?>

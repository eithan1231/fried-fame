<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\special\db.php
//
// ======================================


// Getting globals
global $ff_request, $ff_response, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<title>Database Error</title>
		<meta name="robots" content="noindex, nofollow" />
	</head>
	<body>
		<!--
			NOTE: Remember this page needs to be english (or a defaulted language), as
			we cannot fetch phrases from database.
		-->
		<h1>Database Error</h1>
		<p>
			Sorry, we are unable to deliver this page. We are currently experiencing
			issues with our database.
		</p>

		<p>
			If you see one of our engineers, give him this code! <b><?= ff_esc(FF_REQUEST_ID) ?></b>.
		</p>

		<hr>

		<div style="text-align: center; margin-top: 100px;">
			<img style="height: 200px" src="<?= $ff_router->getPath('asset', [
				'extension' => 'png',
				'asset' => 'db-meme'
			]) ?>" alt="meme"/>
		</div>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

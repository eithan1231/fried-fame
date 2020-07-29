<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\special\404.php
//
// ======================================


// Getting globals
global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

$language = $ff_context->getLanguage();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= $language->getPhrase('404-title') ?></title>
		<meta name="robots" content="noindex, nofollow" />

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<h1 class="text-center"><?= $language->getPhrase('404-header') ?></h1>
		<p class="text-center">
			<?= $language->getPhrase('404-body', [
				'home' => $ff_router->getPath('landing')
			]) ?>
		</p>

		<hr>

		<div style="text-align: center; margin-top: 100px;">
			<img style="height: 200px" src="<?= $ff_router->getPath('asset', [
				'extension' => 'png',
				'asset' => '404-meme'
			]) ?>" alt="meme"/>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

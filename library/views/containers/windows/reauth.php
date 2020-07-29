<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\containers\windows\reauth.php
//
// ======================================


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
		<title><?= ff_esc($ff_config->get('project-name')) ?></title>
    <meta name="robots" content="noindex, nofollow" />

    <?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_containers_windows_navbar::render() ?>

    <div class="container pre-scrollable fixOverflow">
      <h3 class="align-center" lang="en">Reauthentication. This page should not be reached.</h3>
    </div>

    <?php snippets_scriptincl::render(['include' => ['windows']]) ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

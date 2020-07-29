<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\faq.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-pp', $language)) ?></title>
		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render() ?>
	</head>
	<body>
		<?php snippets_navbar::render() ?>

		<div class="container">

		</div>

		<?php snippets_footer::render() ?>
		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

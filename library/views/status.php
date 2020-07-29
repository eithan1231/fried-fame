<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\status.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$statusParameters = ff_getViewParameters();
$statusPage = $statusParameters['status-page'];

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-status-page', $language)) ?></title>


		<?php snippets_opengraph::render() ?>
		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'description' => 'meta-og-status-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-status-title',
			'description' => 'meta-og-status-description',
		]) ?>

		<script type="text/javascript">
		function handleResize(frame) {
			try {
				frame.height = (frame.contentWindow.document.body.scrollHeight + 'px');
			}
			catch(err) {
				frame.height = "300px";
			}
		}
		</script>

		<style media="screen">
			iframe {
				border: none;
				width: 100%;
				overflow-y: visible;
			}
		</style>
	</head>
	<body>
		<?php snippets_navbar::render() ?>

		<div class="container" style="padding: 10px 0 10px 0;">
			<h1><?= $language->getPhrase('oneword-status-page') ?></h1>
			<hr>
			<iframe onload="handleResize(this)" src="<?= ff_esc($statusPage) ?>"></iframe>
		</div>

		<?php snippets_footer::render() ?>
		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
?>

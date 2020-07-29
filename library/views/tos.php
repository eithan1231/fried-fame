<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\tos.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

// TODO: Fix this, should be able to dynamically edit it.
$lastEditUnix = 1546167062;

// Get last edit time
$lastEdited = (function($t) {
	global $ff_context;
	if($session = $ff_context->getSession()) {
		if($user = $session->getActiveLinkUser()) {
			return $user->date($user->dateFormat(), $t);
		}
		else {
			return date('F j, Y, g:i a', $t);
		}
	}
	return 'Unknown';
})($lastEditUnix);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-tos', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'description' => 'meta-og-tos-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-tos-title',
			'description' => 'meta-og-tos-description',
		]) ?>
	</head>
	<body>
		<?php snippets_navbar::render() ?>

		<div class="container">
			<div style="margin-bottom: 30px">
				<h1><?= ff_esc($language->getPhrase('tos-title')) ?></h1>
				<sub><?= ff_esc($language->getPhrase('tos-title-sub', [
					'date' => $lastEdited
				])) ?></sub>
				<hr>
			</div>

			<div>
				<h3><?= $language->getPhrase('tos-basic-compliance-title') ?></h3>
				<ul>
					<li><?= $language->getPhrase('tos-basic-compliance-1') ?></li>
					<li><?= $language->getPhrase('tos-basic-compliance-2') ?></li>
				</ul>
			</div>

			<h3><?= $language->getPhrase('tos-payments-title') ?></h3>
			<div>
				<ul>
					<li><?= $language->getPhrase('tos-payments-1') ?></li>
					<li><?= $language->getPhrase('tos-payments-2', [
						'project' => $ff_config->get('project-name')
					]) ?></li>
				</ul>
			</div>

			<h3><?= $language->getPhrase('tos-warranties-title') ?></h3>
			<div>
				<p><?= $language->getPhrase('tos-warranties-p1') ?></p>
				<p><?= $language->getPhrase('tos-warranties-p2', [
					'project' => $ff_config->get('project-name')
				]) ?></p>
				<ul>
					<li><?= $language->getPhrase('tos-warranties-ul1-li1') ?></li>
					<li><?= $language->getPhrase('tos-warranties-ul1-li2') ?></li>
					<li><?= $language->getPhrase('tos-warranties-ul1-li3') ?></li>
				</ul>
			</div>
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

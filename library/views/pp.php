<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\pp.php
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
			'description' => 'meta-og-pp-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-pp-title',
			'description' => 'meta-og-pp-description',
		]) ?>
	</head>
	<body>
		<?php snippets_navbar::render() ?>

		<div class="container">
			<h1><?= $language->getPhrase('pp-header') ?></h1>
			<p>
				<?= $language->getPhrase('pp-header-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>
			<hr>

			<h3><?= $language->getPhrase('pp-header-cookies') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-cookies-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-collect') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-collect-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-protect') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-protect-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-sharing-info') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-sharing-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-3rdparty') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-3rdparty-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-changes') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-changes-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-acceptance') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-acceptance-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>

			<h3><?= $language->getPhrase('pp-header-contact') ?></h3>
			<p>
				<?= $language->getPhrase('pp-header-contact-description', [
					'hostname' => $ff_config->get('primary-hostname'),
					'name' => $ff_config->get('project-name')
				]) ?>
			</p>
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

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\contact.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$security_token = $ff_context->getSession()->getSecurityToken(true);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-contact-us', $language)) ?></title>
		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'description' => 'meta-og-contact-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-contact-title',
			'description' => 'meta-og-contact-description',
		]) ?>
	</head>
	<body>
		<?php snippets_navbar::render([
			'contact' => true
		]) ?>

		<div class="container" style="padding: 10px;">
			<div class="card card-info">
				<div class="card-header">
					<h4><?= $language->getPhrase('oneword-contact-us') ?></h4>
				</div>
				<div class="card-body">
					<form action="<?= $ff_router->getPath('post', [
						'security_token' => $security_token->getToken(),
						'action' => 'contact'
					]) ?>" method="post">
						<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-name') ?></span>
							</div>
							<input name="name" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-name') ?>" value="<?= ff_esc($ff_request->get('name')) ?>" autofocus>

							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-email') ?></span>
							</div>
							<input name="email" type="email" class="form-control" placeholder="<?= $language->getPhrase('oneword-email') ?>" value="<?= ff_esc($ff_request->get('email')) ?>">
						</div>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-subject') ?></span>
							</div>
							<input name="subject" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-subject') ?>">
						</div>

						<div class="input-group mb-3">
							<textarea name="body" rows="8" cols="80" class="form-control" placeholder="<?= $language->getPhrase('misc-describe-contact-reason') ?>"></textarea>
						</div>

						<?php $ff_context->getCaptcha()->renderFormElement() ?>

						<span><?= $language->getPhrase('util-agreement', [
							'tos_url' => $ff_router->getPath('tos'),
							'pp_url' => $ff_router->getPath('pp')
						]) ?></span>

						<button type="submit" class="btn btn-primary float-right"><?= $language->getPhrase('oneword-submit') ?></button>
					</form>
				</div>
			</div>
		</div>


		<?php snippets_footer::render() ?>
		<?php snippets_scriptincl::render() ?>
		<?php $ff_context->getCaptcha()->renderScriptElements() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $security_token);

?>

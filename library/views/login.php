<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\login.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

// Generating secury token
$security_token = $ff_context->getSession()->getSecurityToken(true);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-login', $language)) ?></title>
		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'description' => 'meta-og-login-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-login-title',
			'description' => 'meta-og-login-description',
		]) ?>
	</head>
	<body>
		<?php snippets_navbar::render([
			// Render home button as active
			'login' => 1
		]) ?>
		<div class="container" style="padding: 10px;">
			<div class="card card-info">
				<div class="card-header">
					<h4><?= $language->getPhrase('oneword-login') ?></h4>
				</div>
				<div class="card-body">
					<form action="<?= $ff_router->getPath('post', [
						'security_token' => $security_token->getToken(),
						'action' => 'login'
					]) ?>" method="post">
						<?php snippets_alert::render([
							'phrase' => strval($ff_request->get('phrase', request::METHOD_GET)),// The selected phrase
							'trusted_phrases' => [
								'default'
							]
						]) ?>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-username') ?></span>
							</div>
							<input name="username" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-username') ?>" value="<?= ff_esc($ff_request->get('username')) ?>" autofocus>
						</div>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-password') ?></span>
							</div>
							<input name="password" type="password" class="form-control" placeholder="<?= $language->getPhrase('oneword-password') ?>">
						</div>

						<?php $ff_context->getCaptcha()->renderFormElement() ?>

						<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
					</form>
				</div>
				<div class="card-footer">
					<div class="btn-group">
						<a href="<?= $ff_router->getPath('register') ?>" class="btn btn-primary"><?= $language->getPhrase('oneword-registration') ?></a>
						<a href="<?= $ff_router->getPath('recovery') ?>" class="btn btn-primary"><?= $language->getPhrase('oneword-recovery') ?></a>
					</div>
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

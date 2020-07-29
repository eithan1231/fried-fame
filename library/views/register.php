<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\register.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

// Generating secury token
$security_token = $ff_context->getSession()->getSecurityToken(true);

// Check whether this has been refered from email mailing list signup thing
$isFromLandingRedirect = ff_stringToBool($ff_request->get('landing-page-redirect'));

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-register', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'description' => 'meta-og-register-description',
		]) ?>
		<?php snippets_altlang::render() ?>
		<?php snippets_opengraph::render([
			'title' => 'meta-og-register-title',
			'description' => 'meta-og-register-description',
		]) ?>
	</head>
	<body>
		<?php snippets_navbar::render() ?>
		<div class="container" style="padding: 10px;">
			<div class="card card-info">
				<div class="card-header">
					<h4><?= $language->getPhrase('oneword-registration') ?></h4>
				</div>
				<div class="card-body">
					<form action="<?= $ff_router->getPath('post', [
						'security_token' => $security_token->getToken(),
						'action' => 'register'
					])?>" method="post">
						<?php snippets_alert::render([
							'phrase' => strval($ff_request->get('phrase', request::METHOD_GET))
						]) ?>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 150px"><?= $language->getPhrase('oneword-username') ?></span>
							</div>
							<input name="username" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-username') ?>" value="<?= ff_esc($ff_request->get('username')) ?>" autofocus required>
						</div>

						<?php if ($isFromLandingRedirect && $ff_request->get('email')): ?>
							<input name="email" type="hidden" value="<?= ff_esc($ff_request->get('email')) ?>">
							<input name="from-email-sub" type="hidden" value="1">
						<?php else: ?>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 150px"><?= $language->getPhrase('oneword-email') ?></span>
								</div>
								<input name="email" type="email" class="form-control" placeholder="<?= $language->getPhrase('oneword-email') ?>" value="<?= ff_esc($ff_request->get('email')) ?>" required>
							</div>
						<?php endif; ?>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 150px"><?= $language->getPhrase('oneword-password') ?></span>
							</div>
							<input name="password" type="password" class="form-control" placeholder="<?= $language->getPhrase('oneword-password') ?>" required>
						</div>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 150px"><?= $language->getPhrase('oneword-verify-password') ?></span>
							</div>
							<input name="password2" type="password" class="form-control" placeholder="<?= $language->getPhrase('oneword-verify-password') ?>" required>
						</div>

						<?php $ff_context->getCaptcha()->renderFormElement() ?>

						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" checked><?= $language->getPhrase('register-tos-pp-agree-snippet', [
									'tos' => $ff_router->getPath('tos'),
									'pp' => $ff_router->getPath('pp'),
								]) ?>
							</label>
						</div>

						<div class="form-group form-check">
							<label class="form-check-label">
								<input class="form-check-input" name="mailing-list" value="true" type="checkbox" <?= $isFromLandingRedirect ? 'disabled ' : '' ?>checked><?= $language->getPhrase('register-mlt-agree-snippet', [
									'mlt' => $ff_router->getPath('mlt'),
								]) ?>
							</label>
						</div>

						<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
					</form>
				</div>
				<div class="card-footer">
					<div class="btn-group">
						<a href="<?= $ff_router->getPath('login') ?>" class="btn btn-primary"><?= $language->getPhrase('oneword-login') ?></a>
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

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\reauth.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

// Getting user language (used to get phrases)
$language = $ff_context->getLanguage();

// Getting session infromation, and user linked w/ session
$session = $ff_context->getSession();
$activeUserLink = $session->getActiveLink();
$userObject = user::getUserById($activeUserLink['user_id']);
if(!$userObject) {
	// Unable to get infroamtion linked with user. We are also not redirecting,
	// because in the route of this class, it checks permissions there, and it deems
	// we should be here.... so if we redirect, it might cause a loop.
	$ff_response->setHttpStatus(500);
	$ff_response->setHttpHeader('Content-Type', 'text/plain');
	$ff_response->clearBody();
	$ff_response->appendBody('Internal Error: User not found.');
	// Ideally we should be sending this response with translation, but /cares.
	return;
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-reauth', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<div class="container" style="padding-top: 10px;">
			<div class="card card-info">
				<div class="card-header">
					<h4><?= ff_esc($language->getPhrase('misc-reauthenticate')) ?></h4>
				</div>
				<div class="card-body">
					<form action="<?= $ff_router->getPath('post', [
						'security_token' => $ff_context->getSession()->getSecurityToken(),
						'action' => 'sessionreauth'
					]) ?>" method="post">
						<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

						<p><?= $language->getPhrase('page-reauth-paragraph', [
							'account-name' => $userObject->getUsername()
						]) ?></p>
						<hr>

						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-password')) ?></span>
							</div>
							<input name="password" type="password" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-password')) ?>" autofocus>
						</div>

						<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
					</form>
				</div>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $session, $activeUserLink, $userObject, $additionalAuth, $formRaw);

?>

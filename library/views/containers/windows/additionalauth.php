<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\containers\windows\additionalauth.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

$language = $ff_context->getLanguage();


$security_token = $ff_context->getSession()->getSecurityToken(true);

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

$additionalAuth = additionalauth::getUserAuth($userObject);
if(!$additionalAuth) {
	// Additional auth information not found. We are also not redirecting,
	// because in the route of this class, it checks permissions there, and it deems
	// we should be here.... so if we redirect, it might cause a loop.
	$ff_response->setHttpStatus(500);
	$ff_response->setHttpHeader('Content-Type', 'text/plain');
	$ff_response->clearBody();
	$ff_response->appendBody('Internal Error: Additional authentication not found.');
	// Ideally we should be sending this response with translation, but /cares.
	return;
}

if(!$additionalAuth->requiresForm()) {
	// Form is not required, but we're here, and cannot redirect without
	// verification, so I guess we're staying here.. We are also not redirecting,
	// because in the route of this class, it checks permissions there, and it deems
	// we should be here.... so if we redirect, it might cause a loop.
	$ff_response->setHttpStatus(500);
	$ff_response->setHttpHeader('Content-Type', 'text/plain');
	$ff_response->clearBody();
	$ff_response->appendBody('Internal Error: Form not required.');
	// Ideally we should be sending this response with translation, but /cares.
	return;
}

$formRaw = $additionalAuth->buildForm();

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
			<form action="<?= $ff_router->getPath('post', [
				'security_token' => $security_token->getToken(),
				'action' => 'additionalauth'
			]) ?>" method="post">
				<h3><?= ff_esc($language->getPhrase($formRaw['title'])) ?></h3>

				<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

				<input type="hidden" name="returntype" value="windows">

				<?php foreach($formRaw['input'] as $key => $value): ?>
					<div class="input-group input-group-sm mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="a<?= hash('md5', $key) ?>" style="width: 135px"><?= ff_esc($language->getPhrase($value['label'])) ?></span>
						</div>
						<input name="<?= ff_esc($key) ?>" type="<?= ff_esc($value['type']) ?>" area-describedBy="a<?= hash('md5', $key) ?>" class="form-control" placeholder="<?= ff_esc($language->getPhrase($value['label'])) ?>"autofocus>
					</div>
				<?php endforeach; ?>

				<?php $ff_context->getCaptcha()->renderFormElement([
          'recaptcha2' => [
            'attributes' => [
              'style' => 'transform: scale(0.9); transform-origin: 0 0;'
            ]
          ]
        ]) ?>

				<button type="submit" class="btn btn-sm btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
			</form>
    </div>

    <?php snippets_scriptincl::render(['include' => ['windows']]) ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

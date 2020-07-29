<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\containers\windows\login.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

$language = $ff_context->getLanguage();
$security_token = $ff_context->getSession()->getSecurityToken(true);

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
        'action' => 'login'
      ]) ?>" method="post">
				<h2><?= $language->getPhrase('oneword-login') ?></h2>

				<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

        <input type="hidden" name="returntype" value="windows">

        <div class="input-group input-group-sm mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text" id="username-sizing-sm" style="width: 90px"><?= $language->getPhrase('oneword-username') ?></span>
          </div>
          <input name="username" type="text" class="form-control" area-describedBy="username-sizing-sm" placeholder="<?= $language->getPhrase('oneword-username') ?>" value="<?= ff_esc($ff_request->get('username')) ?>" required autofocus>
        </div>

        <div class="input-group input-group-sm mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text" id="password-sizing-sm" style="width: 90px"><?= $language->getPhrase('oneword-password') ?></span>
          </div>
          <input name="password" type="password" class="form-control" area-describedBy="username-sizing-sm" placeholder="<?= $language->getPhrase('oneword-password') ?>" required>
        </div>

        <?php $ff_context->getCaptcha()->renderFormElement([
          'recaptcha2' => [
            'attributes' => [
              'style' => 'transform: scale(0.9); transform-origin: 0 0;'
            ]
          ]
        ]) ?>

        <div class="btn-group">
          <button type="submit" class="btn btn-sm btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
          <a class="btn btn-sm btn-primary" href="<?= $ff_router->getPath('register') ?>" title="<?= $ff_router->getPath('register') ?>" onclick="return windows.openUrlElsewhere(this)">
            <?= $language->getPhrase('oneword-registration') ?>
          </a>
        </div>
      </form>
    </div>

    <?php snippets_scriptincl::render(['include' => ['windows']]) ?>
    <?php $ff_context->getCaptcha()->renderScriptElements() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language);

?>

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\giftcard.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$user = $ff_context->getSession()->getActiveLinkUser();
$security_token = $ff_context->getSession()->getSecurityToken(true);
$plans = plan::getPlans(100);

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-giftcard', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" stlye="padding-top: 10px;">
				<div class="card card-info">
					<div class="card-header">
						<h4><?= ff_esc($language->getPhrase('misc-generate-giftcards')) ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'creategiftcodes'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-message')) ?></span>
								</div>
								<input name="message" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('misc-message-optional')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-plan')) ?></span>
								</div>
								<select name="plan" class="form-control">
									<?php foreach ($plans as $plan): ?>
										<option value="<?= ff_esc($plan->getId()) ?>"><?= ff_esc($plan->getName()) ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-count')) ?></span>
								</div>
								<input name="count" type="number" value="1" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-count')) ?>" autofocus>
							</div>


							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>
			</div>
			</div>
		</div>
		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

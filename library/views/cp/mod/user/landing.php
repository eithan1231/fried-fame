<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\user\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$security_token = $ff_context->getSession()->getSecurityToken(true);

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc($ff_config->get('project-name')) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 10px">
				<?php snippets_invalidemail::render() ?>

				<h1 style="margin-bottom: 35px"><?= $language->getPhrase('mod-user-title') ?></h1>

				<div class="card bg-light mb-3" style="max-width: 18rem;">
				  <div class="card-header"><?= $language->getPhrase('oneword-search-user') ?></div>
				  <div class="card-body">
						<form action="<?= $ff_router->getPath('cp_mod_user_find') ?>" method="GET">
							<div class="input-group input-group-sm mb-3">
							  <div class="input-group-prepend">
							    <span class="input-group-text" id="inputGroup-sizing-sm"><?= $language->getPhrase('oneword-username') ?></span>
							  </div>
							  <input type="text" name="user" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" required>
							</div>

							<button type="submit" class="btn btn-primary btn-sm btn-block"><?= $language->getPhrase('oneword-submit') ?></button>
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

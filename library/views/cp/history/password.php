<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\history\password.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

// User object.
$userObject = $ff_context->getSession()->getActiveLinkUser();
if(!$userObject) {
	$ff_response->setHttpStatus(500);
	$ff_response->setHttpHeader('Content-Type', 'text/plain');
	$ff_response->clearBody();
	$ff_response->appendBody('Internal Error: User not found.');
	return;
}

// Getting password history.
$passwordHistory = $userObject->getPasswordHistory();
if(!$passwordHistory) {
	$ff_response->redirect($ff_router->getPath('cp_settings'));
	return;
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-email-history', $language)) ?></title>

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

				<h1><?= $language->getPhrase('password-history-title') ?></h1>
				<table class="table">
				  <thead class="thead-light">
				    <tr>
				      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-platform') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-browser') ?></th>
				    </tr>
				  </thead>
				  <tbody>
						<?php foreach($passwordHistory as $value): ?>
							<?php $ua = new useragent($value['useragent']) ?>
							<tr class="thead-light">
								<td>
									<?= ff_esc($userObject->date($userObject->dateFormat(), $value['date'])) ?>
								</td>

								<td title="<?= ff_esc($value['useragent']) ?>">
									<?= ff_esc($ua->getPlatform()) ?>
								</td>

								<td title="<?= ff_esc($value['useragent']) ?>">
									<?= ff_esc($ua->getBrowser()) ?>
								</td>
							</tr>
						<?php endforeach; ?>
				  </tbody>
				</table>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $userObject, $passwordHistory);

?>

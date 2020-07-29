<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\announcements.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$activeUser = $ff_context->getSession()->getActiveLinkUser();
$announcements = announcement::getActiveAnnouncements();
if(!$announcements) {
	return $ff_response->redirect($ff_router->getPath('cp_landing'));
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-redeem-giftcard', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>

		<style media="screen">
			.announcementContainer {
				padding-bottom: 30px;
			}

			.announcementContainer > .header {
				margin-bottom: 5px;
			}

			.announcementContainer > .body {
				text-indent: 30px;
				margin-top: 30px;
			}
		</style>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container">
				<h1>Announcement history viewer</h1>
				<hr>

				<?php foreach ($announcements as $announcement): ?>
					<div class="announcementContainer">
						<h3 class="header"><?= ff_esc($announcement->getSubject()) ?></h3>
						<small class="text-muted"><?= ff_esc($activeUser->date($activeUser->dateFormat(), $announcement->getDate())) ?></small>
						<div class="body">
							<?= $announcement->getCleanBody() ?>
						</div>
						<hr>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
		<?php $ff_context->getCaptcha()->renderScriptElements() ?>
	</body>
</html>
<?php $ff_response->stopOutputBuffer() ?>

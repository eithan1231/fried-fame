<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\announcement.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');



$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-announcement', $language)) ?></title>

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
						<h4><?= ff_esc($language->getPhrase('misc-new-announcement')) ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'announcement'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<input type="hidden" id="sv-internal-body" name="body">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-duration')) ?></span>
								</div>
								<select name="duration" class="form-control">
									<option value="<?= FF_DAY ?>"><?= $language->getPhrase('misc-1-day') ?></option>
									<option value="<?= FF_WEEK ?>"><?= $language->getPhrase('misc-1-week') ?></option>
									<option value="<?= FF_MONTH ?>"><?= $language->getPhrase('misc-1-month') ?></option>
									<option value="<?= (FF_MONTH * 3) ?>"><?= $language->getPhrase('misc-3-months') ?></option>
									<option value="<?= (FF_MONTH * 6) ?>"><?= $language->getPhrase('misc-6-months') ?></option>
									<option value="<?= FF_YEAR ?>"><?= $language->getPhrase('misc-1-year') ?></option>
								</select>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-subject')) ?></span>
								</div>
								<input name="subject" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-subject')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div id="sv-body-pell" class="pell">
									<!--
										Everything within this div should be removed when pell is
										loaded, what is here now, is acting as a backup in the event
										there is a javscript problem (no js browser, or error).
									-->
									<textarea class="form-control" name="body" rows="3" placeholder="<?= ff_esc($language->getPhrase('oneword-body')) ?>"></textarea>
								</div>
							</div>


							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>
			</div>
			</div>
		</div>
		<?php snippets_scriptincl::render(['include' => ['pell']]) ?>
		<script type="text/javascript">
			window.addEventListener('load', () => {
				let internalBody = document.getElementById('sv-internal-body');
				ff_pell.init({
					element: document.getElementById('sv-body-pell'),
					onChange: content => internalBody.value = content
				});
			});
		</script>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

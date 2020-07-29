<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\support\new.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$security_token = $ff_context->getSession()->getSecurityToken(true);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-support-new', $language)) ?></title>

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

				<div class="card card-info">
					<div class="card-header">
						<h4><?= $language->getPhrase('oneword-support-thread') ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'newsupport'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<input type="hidden" id="sv-internal-body" name="body">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-subject') ?></span>
								</div>
								<input name="subject" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-subject') ?>" value="<?= ff_esc($ff_request->get('username')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div id="sv-body-pell" class="pell">
									<!--
										Everything within this div should be removed when pell is
										loaded, what is here now, is acting as a backup in the event
										there is a javscript problem (no js browser, or error).
									-->
									<textarea class="form-control" name="body" rows="3" placeholder="<?= ff_esc($language->getPhrase('support-new-body-placeholder')) ?>"></textarea>
								</div>
							</div>

							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php snippets_scriptincl::render(['include' => ['pell']]) ?>
		<?php if ($ff_config->get('allow-html-support-posts')): ?>
			<script type="text/javascript">
				window.addEventListener('load', () => {
					let internalBody = document.getElementById('sv-internal-body');
					ff_pell.init({
						element: document.getElementById('sv-body-pell'),
						onChange: content => internalBody.value = content
					});
				});
			</script>
		<?php endif; ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $security_token);

?>

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\payments\paypal\cancel.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$security_token = $ff_context->getSession()->getSecurityToken(true);

$hideFeedback = $ff_request->get('hide-feedback');
$hideFeedback = $hideFeedback ? ff_stringToBool($hideFeedback) : false;

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-paypal-cancel', $language)) ?></title>

		<style media="screen">
			@media (max-width: 768px) {
				.cstm-feedback-container {
					width: 100%;
				}
			}

			@media (min-width: 768px) {
				.cstm-feedback-container {
					width: 50%;
				}
			}
		</style>

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
						<h4><?= $language->getPhrase('misc-processing-payment') ?></h4>
					</div>
					<div class="card-body">
						<p>
							<?= $language->getPhrase('missc-pp-cancel-text') ?>
						</p>

						<?php if (!$hideFeedback): ?>
							<div class="cstm-feedback-container">
								<hr>
								<h4><?= $language->getPhrase('misc-feedback')?></h4>
								<form action="<?= $ff_router->getPath('post', [
									'security_token' => $security_token->getToken(),
									'action' => 'feedback'
								], [
									'query' => [
										'return' => $ff_router->getPath('cp_landing', [], [
											'mode' => 'host'
										])
									]
								]) ?>" method="post">
									<div class="input-group mb-3">
										<textarea name="body" class="form-control" rows="3"></textarea>
									</div>

									<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit')?></button>
								</form>
							</div>
						<?php endif; ?>
					</div>
				</div>


			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php $ff_response->stopOutputBuffer() ?>
<?php unset($language) ?>

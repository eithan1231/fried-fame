<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\payments\paypal\success.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$parameters = ff_getViewParameters();
$state = $parameters['state'];
$user = $parameters['user'];

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-paypal-success', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'js_cfg' => [
				'state_token' => $state->getToken(),
				'phrases' => [
					'permission-denied' => $language->getPhrase('misc-permission-denied'),
					'success' => $language->getPhrase('misc-success'),
				],
				'routes' => [
					'cp_landing' => $ff_router->getPath('cp_landing')
				]
			]
		]) ?>
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
					<div id="status-body" class="card-body">

					</div>
				</div>
			</div>
		</div>

		<!-- Still processing template -->
		<script id="processing" type="text/html">
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 50%"></div>
			</div>

			<div style="margin-top: 15px;">
				<p><?= $language->getPhrase('paypal-success-processing') ?></p>
			</div>
		</script>

		<!-- Processed complete template -->
		<script id="processed" type="text/html">
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
			</div>

			<div style="margin-top: 15px;">
				<p><?= $language->getPhrase('paypal-success-processed', [
					'payment_view' => $ff_router->getPath('cp_payments_view', [
						'id' => '___id___'
					]),
					'new_support' => $ff_router->getPath('cp_support_new')
				])?></p>
			</div>
		</script>

		<?php snippets_scriptincl::render() ?>
		<!-- Process Ping Checker -->
		<script type="text/javascript">
		(function() {
			function completed(id) {
				let elem = document.getElementById('status-body');
				elem.innerHTML = document.getElementById('processed').innerHTML.replace('___id___', id);
			}

			function processing() {
				let elem = document.getElementById('status-body');
				elem.innerHTML = document.getElementById('processing').innerHTML;
			}

			window.addEventListener('load', function() {
				processing();

				/**
				* Every 10 seconds, send ping.
				*/
				setInterval(function() {
					ff_custom.payment.getStatus(ff_config.state_token, (function(err, cmd, data) {
						if(err) {
							console.error(err);
							return;
						}
						switch(cmd) {
							case 'permission-denied':
							case 'missing-parameters':
							case 'bad-state': {
								/**
								* All these are errors. Cant be bothered handling, so let's just
								* redirect.
								*/
								window.href = ff_config.routes.cp_landing;
								break;
							}

							case 'completed': {
								completed(data.id);
								break;
							}

							case 'processing': {
								processing();
								break;
							}

							default: break;
						}
					}));
				}, 1000 * 10);
			});
		})();
		</script>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $parameters, $thread, $posts);
?>

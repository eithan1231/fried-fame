<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\payments\method.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$parameters = ff_getViewParameters();
$plan = $parameters['plan'];

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-payments-method', $language)) ?></title>

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
						<h4><?= $language->getPhrase('misc-payment-configuration') ?></h4>
					</div>
					<div class="card-body">
            <form method="get">
  						<?php snippets_alert::render([
  							'phrase' => strval($ff_request->get('phrase', request::METHOD_GET))
              ]) ?>

              <input type="hidden" name="plan_id" value="<?= ff_esc($plan->getId()) ?>">

              <div>
                <h2><?= $language->getPhrase('payments-plan-heading', [
									'name' => $plan->getName()
									]) ?>
								</h2>
              </div>
							<div>
								<small><?= $language->getPhrase('payment-method-concurrent', [
									'number' => $plan->getMaximumConcurrentConnections()
								])?></small>
							</div>

              <?php if ($plan->getDiscountable()): ?>
								<hr>
								<div class="input-group mb-3">
	  							<div class="input-group-prepend">
	  								<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-coupon') ?></span>
	  							</div>
	  							<input name="coupon_code" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-coupon') ?>" value="<?= ff_esc($ff_request->get('coupon')) ?>">
	  						</div>

	              <div>
									<!-- Affiliation - May be automatically set, but the automated setting is incomplete. -->
	              </div>

								<hr>
							<?php else: ?>
								<div>
									<small><?= $language->getPhrase('payments-non-discountable') ?></small>
								</div>

								<hr>
              <?php endif; ?>
							<div style="text-align: right; font-size: 20px; width: auto;">
								<div title="<?= ff_esc(currency::getCurrencyPrefix($plan->getCurrency()) .' '. $plan->getCurrency()) ?>">
									<?= $language->getPhrase('payments-subtotal-indication', [
										'total' => currency::getCurrencyPrefix($plan->getCurrency()) . $plan->getPrice()
									]) ?>
								</div>
							</div>

							<div style="width: 70%" class="float-left">
								<div style="margin-bottom: 13px; max-width: 400px;">
									<span><?= $language->getPhrase('misc-purchase-tos-agreement', [
										'plan' => $plan->getName(),
										'tos_url' => $ff_router->getPath('tos'),
										'pp_url' => $ff_router->getPath('pp')
									]) ?></span>
								</div>
							</div>
							<div style="margin-top: 30px;" class="float-right">
								<?php if ($ff_config->get('paypal-enabled')): ?>
	                <button type="submit" formaction="<?= ff_esc($ff_router->getPath('payments_gateways_paypal_redirect')) ?>" class="btn btn-primary float-right">
	                  <?= $language->getPhrase('oneword-paypal') ?>
	                </button>
	              <?php endif; ?>
							</div>
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
unset($language, $user, $group, $parameters, $thread, $posts, $security_token);
?>

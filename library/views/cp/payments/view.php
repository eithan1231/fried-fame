<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\payments\view.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$parameters = ff_getViewParameters();
$payment = $parameters['payment'];
$paymentInfo = $payment->getGetwayInfo();
$paymentState = $payment->getPaymentState();
$paymentPlan = $paymentState->getPlan();
$paymentCoupon = $payment->getCoupon();


// Just a constant for paypal.
const VIEW_GATEWAY_PAYPAL = 'paypal';

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-payments-view', $language, [
			'id' => "#{$payment->getId()}"
		])) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>

		<style>
		.height {
			min-height: 200px;
		}

		.table > tbody > tr > .emptyrow {
			border-top: none;
		}

		.table > thead > tr > .emptyrow {
			border-bottom: none;
		}

		.table > tbody > tr > .highrow {
			border-top: 3px solid;
		}

		.card {
			margin-bottom: 15px;
		}
		</style>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 10px">
				<?php snippets_invalidemail::render() ?>
				<?php if ($payment->getGatewayName() == VIEW_GATEWAY_PAYPAL && ff_stringToBool($paymentInfo['test_ipn'])): ?>
					<?php snippets_alert::render([
						'style' => snippets_alert::ALERT_DANGER,
						'phrase' => 'notice-paypal-test-ipn',
						'trusted_phrases' => ['notice-paypal-test-ipn']
					]) ?>
				<?php endif; ?>


				<div class="row">
					<div class="col-12">
						<h2><?= $language->getPhrase('payment-view-h', [
							'id' => $payment->getId()
						]) ?></h2>
						<hr>
						<div class="row">
							<div class="col-12 col-sm-4">
								<div class="card height">
									<h6 class="card-header"><?= $language->getPhrase('oneword-payment-information') ?></h6>
									<div class="card-body" style="font-size: 14px;">
										<?php if ($payment->getGatewayName() == VIEW_GATEWAY_PAYPAL): ?>

											<strong><?= $language->getPhrase('oneword-method') ?>:</strong> <span class="float-right"><?= $language->getPhrase('oneword-paypal') ?></span><br>
											<strong><?= $language->getPhrase('oneword-payment-status') ?>:</strong>
											<span class="float-right">
												<?php if ($payment->getStatus() == payment::STATUS_SUCCESSFUL): ?>
													<?= $language->getPhrase('oneword-complete') ?>
												<?php elseif ($payment->getStatus() == payment::STATUS_CHARGEBACK_PROCESS): ?>
													<?= $language->getPhrase('misc-chargeback-processing') ?>
												<?php elseif ($payment->getStatus() == payment::STATUS_CHARGEBACKED): ?>
													<?= $language->getPhrase('misc-chargeback') ?>
												<?php elseif ($payment->getStatus() == payment::STATUS_FAILED): ?>
													<?= $language->getPhrase('misc-failed') ?>
												<?php elseif ($payment->getStatus() == payment::STATUS_REFUNDED): ?>
													<?= $language->getPhrase('misc-refunded') ?>
												<?php elseif ($payment->getStatus() == payment::STATUS_BAD_INPUT): ?>
													<?= $language->getPhrase('misc-bad-input') ?>
												<?php elseif ($payment->getStatus() == payment::STATUS_REVERSED): ?>
													<?= $language->getPhrase('misc-chargeback-reversal') ?>
												<?php else: ?>
													<?= $language->getPhrase('misc-unknown') ?>
												<?php endif; ?>
											</span><br>
											<strong><?= $language->getPhrase('oneword-transaction') ?>:</strong> <span class="float-right"><?= ff_esc($paymentInfo['txn_id']) ?></span><br>
											<strong><?= $language->getPhrase('oneword-your-email') ?>:</strong> <span class="float-right"><?= ff_esc($paymentInfo['payer_email']) ?></span><br>
											<strong><?= $language->getPhrase('oneword-you-paid') ?>:</strong> <span class="float-right"><?= ff_esc($paymentInfo['business']) ?></span><br>

										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="col-12 col-sm-4">
								<div class="card height">
									<h6 class="card-header"><?= $language->getPhrase('oneword-support') ?></h6>
									<div class="card-body" style="font-size: 14px;">
										<?= $language->getPhrase('payment-view-support', [
											'support-create-url' => $ff_router->getPath('cp_support_new')
										]) ?>
									</div>

								</div>
							</div>
							<div class="col-12 col-sm-4">
								<div class="card height">
									<h6 class="card-header">Plan Information</h6>
									<div class="card-body" style="font-size: 14px;">
										<?php if (!$paymentPlan->getEnabled()): ?>
											<strong><?= $language->getPhrase('oneword-plan-is-disabled') ?></strong></br>
										<?php endif; ?>
										<strong><?= $language->getPhrase('oneword-plan-name') ?>:</strong> <span class="float-right"><?= ff_esc($paymentPlan->getName()) ?></span><br>
										<strong><?= $language->getPhrase('oneword-price') ?>:</strong> <span class="float-right"><?= ff_esc(currency::getCurrencyPrefix($paymentPlan->getCurrency()) . number_format($paymentPlan->getPrice(), 2)) ?></span><br>
										<strong><?= $language->getPhrase('oneword-avg-month-price') ?>:</strong> <span class="float-right"><?= ff_esc(currency::getCurrencyPrefix($paymentPlan->getCurrency()) . number_format($paymentPlan->monthlyPrice(), 2)) ?></span><br>
										<strong><?= $language->getPhrase('oneword-duration') ?>:</strong> <span class="float-right"><?= ff_esc($paymentPlan->getDurationString()) ?></span><br>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h3><strong><?= $language->getPhrase('oneword-order-summary') ?></strong></h3>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-sm">
										<thead>
											<tr>
												<td><strong><?= $language->getPhrase('oneword-item-name') ?></strong></td>
												<td><strong><?= $language->getPhrase('oneword-item-price') ?></strong></td>
												<td><strong><?= $language->getPhrase('oneword-item-quantity') ?></strong></td>
												<td class="text-right"><strong><?= $language->getPhrase('oneword-total') ?></strong></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><?= ff_esc($paymentPlan->getName()) ?></td>
												<td><?= ff_esc(currency::getCurrencyPrefix($paymentPlan->getCurrency()) . number_format($paymentPlan->getPrice(), 2)) ?></td>
												<td>1</td>
												<td class="text-right"><?= ff_esc(currency::getCurrencyPrefix($paymentPlan->getCurrency()) . number_format($paymentPlan->getPrice(), 2)) ?></td>
											</tr>
											<tr>
												<td class="highrow"></td>
												<td class="highrow"></td>
												<td class="highrow "><strong><?= $language->getPhrase('oneword-subtotal') ?></strong></td>
												<td class="highrow text-right"><?= ff_esc(currency::getCurrencyPrefix($paymentPlan->getCurrency()) . number_format($paymentPlan->getPrice(), 2)) ?></td>
											</tr>

											<?php if ($paymentCoupon): ?>
												<tr>
													<td class="emptyrow"></td>
													<td class="emptyrow"></td>
													<td class="emptyrow "><strong><?= $language->getPhrase('oneword-coupon') ?></strong> (<?= $paymentCoupon->getCode() ?>)</td>
													<td class="emptyrow text-right"><?= currency::getCurrencyPrefix($paymentPlan->getCurrency()) ?><?= (($paymentPlan->getPrice() / 100) * $paymentCoupon->getDiscountPercentage()) ?>(<?= $paymentCoupon->getDiscountPercentage() ?>%)</td>
												</tr>
											<?php else: ?>
												<tr>
													<td class="emptyrow"></td>
													<td class="emptyrow"></td>
													<td class="emptyrow "><strong><?= $language->getPhrase('oneword-coupon') ?></strong></td>
													<td class="emptyrow text-right">-</td>
												</tr>
											<?php endif; ?>

											<tr>
												<td class="emptyrow"></td>
												<td class="emptyrow"></td>
												<td class="emptyrow "><strong><?= $language->getPhrase('oneword-affiliation') ?></strong></td>
												<td class="emptyrow text-right">-</td>
											</tr>


											<tr>
												<td class="emptyrow"></td>
												<td class="emptyrow"></td>
												<td class="emptyrow "><strong><?= $language->getPhrase('oneword-total') ?></strong></td>
												<td class="emptyrow text-right"><?= ff_esc(currency::getCurrencyPrefix($payment->getCurrency()) . number_format($payment->getGross(), 2)) ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div><!-- ./container -->
		</div><!-- ./sidebar-body-->

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $parameters, $thread, $posts, $security_token);
?>

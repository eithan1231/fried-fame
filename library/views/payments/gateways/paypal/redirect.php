<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\payments\gateways\paypal\redirect.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');
$ff_response->setHttpHeader('X-Robots-Tag', 'noindex, nofollow');

$language = $ff_context->getLanguage();
$viewParameters = ff_getViewParameters();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc($ff_config->get('project-name')) ?></title>
    <meta name="robots" content="noindex, nofollow" />
		<style media="screen">
		button {
			align-items: normal;
		  background-color: rgba(0,0,0,0);
		  border-color: rgb(0, 0, 238);
		  border-style: none;
		  box-sizing: content-box;
		  color: rgb(0, 0, 238);
		  cursor: pointer;
		  display: inline;
		  font: inherit;
		  height: auto;
		  padding: 0;
		  perspective-origin: 0 0;
		  text-align: start;
		  text-decoration: underline;
		  transform-origin: 0 0;
		  width: auto;
		  -moz-appearance: none;
		  -webkit-logical-height: 1em; /* Chrome ignores auto, so we have to use this hack to set the correct height  */
		  -webkit-logical-width: auto; /* Chrome ignores auto, but here for completeness */
		}
		</style>
	</head>
	<body>
		<form id="toSubmitForm" action="<?= ff_esc(payment_gateway_paypal::getBuyNow()) ?>" method="post" target="_top">
			<input type="hidden" name="cmd" value="_xclick">

			<!-- Return paths  -->
			<input type="hidden" name="return" value="<?= ff_esc($ff_router->getPath('cp_payments_paypal_success', [], [
				'query' => [
					'token' => $viewParameters['payment_state']->getToken()
				],
				'mode' => 'host',
				'allowForceParam' => false
			])) ?>">
			<input type="hidden" name="cancel_return" value="<?= ff_esc($ff_router->getPath('cp_payments_paypal_cancel', [], [
				'mode' => 'host',
				'allowForceParam' => false
			])) ?>">

			<!-- Custom Field (holds payment state) -->
			<input type="hidden" name="custom" value="<?= ff_esc($viewParameters['payment_state']->getToken()) ?>">

			<!-- Merchant identifier -->
			<input type="hidden" name="business" value="<?= ff_esc($ff_config->get('paypal-merchant-id')) ?>">

			<!-- Plan Information -->
			<input type="hidden" name="item_name" value="<?= ff_esc($viewParameters['subscription_plan']->getName()) ?>">
			<input type="hidden" name="item_number" value="<?= ff_esc($viewParameters['subscription_plan']->getId()) ?>">
			<input type="hidden" name="currency_code" value="<?= ff_esc($viewParameters['subscription_plan']->getCurrency()) ?>">
			<input type="hidden" name="amount" value="<?= ff_esc($viewParameters['subscription_plan']->getPrice()) ?>">

			<!-- Discounts -->
			<?php if($viewParameters['subscription_plan']->getDiscountable() && $viewParameters['coupon']): ?>

				<!-- Generating discount amount. Formula: (amount / 100) * discount_percentage -->
				<input type="hidden" name="discount_amount" value="<?= ff_esc(
					($viewParameters['subscription_plan']->getPrice() / 100) * $viewParameters['coupon']->getDiscountPercentage()
				) ?>">
			<?php endif; ?>

			<!-- Misc -->
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="lc" value="<?= ff_esc($language->languageCode()) ?>">
			<input type="hidden" name="undefined_quantity" value="0">

			<!-- Image for store -->
			<input type="hidden" name="image_url" value="<?= $ff_router->getPath('asset', [
				'asset' => 'paypal_150x50',
  			'extension' => 'png'
			], [
				'mode' => 'host',
				'allowForceParam' => false
			]) ?>">


			<div>
				<h2><?= ff_esc($language->getPhrase('misc-redirect-loading')) ?></h2>
				<button type="submit"><?= ff_esc($language->getPhrase('oneword-click-here')) ?></button>
			</div>
		</form>


		<script type="text/javascript">
		window.addEventListener('load', function() {
			document.getElementById('toSubmitForm').submit();
		});
		</script>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();

// Unsetting global variables defined in this class.
unset($language, $action);

?>

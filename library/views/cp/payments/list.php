<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\payments\list.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$user = $ff_context->getSession()->getActiveLinkUser();

$parameters = ff_getViewParameters();
$payments = $parameters['payments'];

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-payments-list', $language)) ?></title>

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

				<h2>
					<?= $language->getPhrase('misc-billing-history') ?>
				</h2>

				<hr>

				<div class="table-responsive">
					<table class="table">
						<thead class="thead-light">
							<tr>
								<th style="border-bottom: 0px" scope="col">#</th>
								<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?><!-- Date--></th>
								<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-status') ?><!-- Status--></th>
								<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-amount') ?><!-- Amount--></th>
								<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-gateway') ?><!-- Gateway (IE: paypal, bitcoin)--></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($payments as $payment): ?>
								<?php $paymentViewPage = $ff_router->getPath('cp_payments_view', [
									'id' => $payment['id']
								]) ?>

								<tr style="overflow: hidden; white-space: nowrap;" class="ff-table-light noselect" data-href="<?= ff_esc($paymentViewPage) ?>" onclick="window.location = this.dataset.href">
									<th>
										<!-- ID -->
										<a href="<?= ff_esc($paymentViewPage) ?>" class="clean-a">
											<?= ff_esc($payment['id']) ?>
										</a>
									</th>

									<td>
										<?= ff_esc($user->date($user->dateFormat(), $payment['date'])) ?>
									</td>

									<td>
										<?php if ($payment['status'] == payment::STATUS_SUCCESSFUL): ?>
											<?= $language->getPhrase('oneword-complete') ?>
										<?php elseif ($payment['status'] == payment::STATUS_CHARGEBACK_PROCESS): ?>
											<?= $language->getPhrase('misc-chargeback-processing') ?>
										<?php elseif ($payment['status'] == payment::STATUS_CHARGEBACKED): ?>
											<?= $language->getPhrase('misc-chargeback') ?>
										<?php elseif ($payment['status'] == payment::STATUS_FAILED): ?>
											<?= $language->getPhrase('misc-failed') ?>
										<?php elseif ($payment['status'] == payment::STATUS_REFUNDED): ?>
											<?= $language->getPhrase('misc-refunded') ?>
										<?php elseif ($payment['status'] == payment::STATUS_BAD_INPUT): ?>
											<?= $language->getPhrase('misc-bad-input') ?>
										<?php elseif ($payment['status'] == payment::STATUS_REVERSED): ?>
											<?= $language->getPhrase('misc-chargeback-reversal') ?>
										<?php else: ?>
											<?= $language->getPhrase('misc-unknown') ?>
										<?php endif; ?>
									</td>

									<td>
										<?= ff_esc(currency::getCurrencyPrefix($payment['currency']) . number_format($payment['gross'], 2)) ?>
									</td>

									<td>
										<?= ff_esc($payment['gateway_name']) ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="pagination pagination-sm">
					<li class="page-item <?= $parameters['page'] <= 0 ? 'disabled' : '' ?>">
						<a class="page-link" href="<?= $ff_router->getPath('cp_payments_list', [], [
							'query' => [
								'page' => $parameters['page'] - 1
							]
						]) ?>">
							&lt;&lt;
						</a>
					</li>

					<li class="page-item <?= (count($payments) < $parameters['pp'] || !$payments) ? 'disabled' : '' ?>">
						<a class="page-link" href="<?= $ff_router->getPath('cp_payments_list', [], [
							'query' => [
								'page' => $parameters['page'] + 1
							]
						]) ?>">
							&gt;&gt;
						</a>
					</li>
				</div>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $tickets);
?>

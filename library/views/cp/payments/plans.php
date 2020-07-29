<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\payments\plans.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$user = $ff_context->getSession()->getActiveLinkUser();

$parameters = ff_getViewParameters();
$plans = $parameters['plans'];

// Body depends on this.
if(!($plans && count($plans) >= 3)) {
	throw new Exception('Plans is invalid');
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-plans-list', $language)) ?></title>

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

				<section class="text-center">
					<div class="container">
						<div class="row">

							<?php $plan = $plans[1]; ?>
							<div class="col-lg-4" style="padding-top: 2em;">
								<div class="card">
									<div class="card-header">
										<?= ff_esc($plan->getName()) ?>
									</div>
									<div class="card-body">
										<div>
											<h2 class="card-title"><?= currency::getCurrencyPrefix($plan->getCurrency()) ?><?= ff_esc($plan->monthlyPrice()) ?><sup><small>/mo</small></sup></h2>
										</div>

										<ul class="list-group list-group-flush">
											<li class="list-group-item"><?= $language->getPhrase('purchase-concurrent-connections', ['concurrent-users' => $plan->getMaximumConcurrentConnections()]) ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-constant-support') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-unlimited') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-no-traffic-logs') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-servers-worldwide') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-active-development') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-constant-uptime') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-cross-platform') ?></li>
										</ul>

										<div>
											<sub class="card-text"><?= $language->getPhrase('misc-price-per', [
												'symbol' => currency::getCurrencyPrefix($plan->getCurrency()),
												'price' => $plan->getPrice(),
												'duration' => $plan->getDurationString(),
											]) ?></sub>
											<div style="margin-top: 1em">
												<a href="<?= ff_esc($ff_router->getPath('cp_payments_method', [
													'plan-id-name' => ff_idAndSubject($plan->getId(), $plan->getName())
												])) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-get-now') ?></a>
											</div>
										</div>
									</div>
								</div>
							</div>

							<?php $plan = $plans[0]; ?>
							<div class="col-lg-4" style="padding-top: 2em; margin-top: -1em">
								<div class="card border-success" style="border-width: 2px">
									<div class="card-header bg-success">
										<?= ff_esc($plan->getName()) ?>
									</div>
									<div class="card-body">
										<div>
											<h2 class="card-title"><?= currency::getCurrencyPrefix($plan->getCurrency()) ?><?= ff_esc($plan->monthlyPrice()) ?><sup><small>/mo</small></sup></h2>
										</div>

										<ul class="list-group list-group-flush">
											<li class="list-group-item"><strong><?= $language->getPhrase('purchase-bast-value') ?></strong></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-concurrent-connections', ['concurrent-users' => $plan->getMaximumConcurrentConnections()]) ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-constant-support') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-unlimited') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-no-traffic-logs') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-servers-worldwide') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-active-development') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-constant-uptime') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-cross-platform') ?></li>
										</ul>

										<div>
											<sub class="card-text"><?= $language->getPhrase('misc-price-per', [
												'symbol' => currency::getCurrencyPrefix($plan->getCurrency()),
												'price' => $plan->getPrice(),
												'duration' => $plan->getDurationString(),
											]) ?></sub>
											<div style="margin-top: 1em">
												<a href="<?= ff_esc($ff_router->getPath('cp_payments_method', [
													'plan-id-name' => ff_idAndSubject($plan->getId(), $plan->getName())
												])) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-get-now') ?></a>
											</div>
										</div>
									</div>
								</div>
							</div>

							<?php $plan = $plans[2]; ?>
							<div class="col-lg-4" style="padding-top: 2em;">
								<div class="card">
									<div class="card-header">
										<?= ff_esc($plan->getName()) ?>
									</div>
									<div class="card-body">
										<div>
											<h2 class="card-title"><?= currency::getCurrencyPrefix($plan->getCurrency()) ?><?= ff_esc($plan->monthlyPrice()) ?><sup><small>/mo</small></sup></h2>
										</div>

										<ul class="list-group list-group-flush">
											<li class="list-group-item"><?= $language->getPhrase('purchase-concurrent-connections', ['concurrent-users' => $plan->getMaximumConcurrentConnections()]) ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-constant-support') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-unlimited') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-no-traffic-logs') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-servers-worldwide') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-active-development') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-constant-uptime') ?></li>
											<li class="list-group-item"><?= $language->getPhrase('purchase-cross-platform') ?></li>
										</ul>

										<div>
											<sub class="card-text"><?= $language->getPhrase('misc-price-per', [
												'symbol' => currency::getCurrencyPrefix($plan->getCurrency()),
												'price' => $plan->getPrice(),
												'duration' => $plan->getDurationString(),
											]) ?></sub>
											<div style="margin-top: 1em">
												<a href="<?= ff_esc($ff_router->getPath('cp_payments_method', [
													'plan-id-name' => ff_idAndSubject($plan->getId(), $plan->getName())
												])) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-get-now') ?></a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $tickets);
?>

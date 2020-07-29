<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$user = $ff_context->getSession()->getActiveLinkUser();
$signedIn = $user !== false;

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc($ff_config->get('project-name')) ?></title>


		<?php snippets_opengraph::render() ?>
		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>

		<style media="screen">
			h1,
			h2,
			h3,
			h4,
			h5,
			h6 {
				font-weight: 700;
			}

			header.masthead {
				position: relative;
				background-color: #343a40;
				background: url("<?= $ff_router->getPath('asset', [
					'asset' => 'ds-3800x1330',
					'extension' => 'webp'
				]) ?>") no-repeat center center;
				background-size: cover;
				padding-top: 8rem;
				padding-bottom: 8rem;
			}

			header.masthead .overlay {
				position: absolute;
				background-color: #212529;
				height: 100%;
				width: 100%;
				top: 0;
				left: 0;
				opacity: 0.3;
			}

			header.masthead h1 {
				font-size: 2rem;
			}

			@media (min-width: 768px) {
				header.masthead {
					padding-top: 12rem;
					padding-bottom: 12rem;
				}
				header.masthead h1 {
					font-size: 3rem;
				}
			}

			.showcase .showcase-text {
				padding: 3rem;
			}

			.showcase .showcase-img {
				min-height: 20rem;
				background-size: contain;
				background-repeat: no-repeat;
				background-position: center;
			}

			@media (min-width: 768px) {
				.showcase .showcase-text {
					padding: 7rem;
				}
			}

			.features-icons {
				padding-top: 7rem;
				padding-bottom: 7rem;
			}

			.features-icons .features-icons-item {
				max-width: 20rem;
			}

			.features-icons .features-icons-item .features-icons-icon {
				height: 7rem;
			}

			.features-icons .features-icons-item .features-icons-icon i {
				font-size: 4.5rem;
			}

			.features-icons .features-icons-item:hover .features-icons-icon i {
				font-size: 5rem;
			}

			.testimonials {
				padding-top: 7rem;
				padding-bottom: 7rem;
			}

			.testimonials .testimonial-item {
				max-width: 18rem;
			}

			.testimonials .testimonial-item img {
				max-width: 12rem;
				-webkit-box-shadow: 0px 5px 5px 0px #adb5bd;
				box-shadow: 0px 5px 5px 0px #adb5bd;
			}
		</style>
	</head>
	<body>
		<?php snippets_navbar::render([
			// Render home button as active
			'home' => 1
		]) ?>

		<!-- Masthead -->
		<header class="masthead text-white text-center">
			<div class="overlay"></div>
			<div class="container">
				<div class="row">
					<div class="col-xl-9 mx-auto">
						<h1 class="mb-5"><?= $language->getPhrase('landing-catch-phrase') ?></h1>
					</div>
				</div>
				<?php if(!$signedIn): ?>
					<div class="row">
						<form class="mx-auto" action="<?= ff_esc($ff_router->getPath('register')) ?>" method="get">
							<div class="form-row align-items-center">
								<input type="hidden" name="landing-page-redirect" value="true">

								<div class="col-10">
									<label class="sr-only" for="inlineEmailSignup"><?= $language->getPhrase('landing-enter-email') ?></label>
									<input type="email" class="form-control mb-2" id="inlineEmailSignup" placeholder="<?= $language->getPhrase('landing-enter-email') ?>" required autofocus>
								</div>

								<div class="col-2">
									<button type="submit" class="btn btn-primary mb-2"><?= $language->getPhrase('landing-signup') ?></button>
								</div>

							</div>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</header>

		<!-- Icons Grid -->
		<section class="features-icons bg-light text-center">
			<div class="container">
				<div class="row">
					<div class="col-lg-4">
						<div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3 n">
							<!-- speed-100x100.png -->
							<img class="noselect" src="<?= $ff_router->getPath('asset', [
								'asset' => 'speed-100x100',
								'extension' => 'png'
							]) ?>" alt="">
							<h3><?= $language->getPhrase('landing-speed') ?></h3>
							<p class="lead mb-0"><?= $language->getPhrase('landing-speed-description') ?></p>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
							<!-- privacy-100x100.png -->
							<img class="noselect" src="<?= $ff_router->getPath('asset', [
								'asset' => 'privacy-100x100',
								'extension' => 'png'
							]) ?>" alt="">
							<h3><?= $language->getPhrase('landing-privacy') ?></h3>
							<p class="lead mb-0"><?= $language->getPhrase('landing-privacy-description') ?></p>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
							<!-- simplicity-100x100.png -->
							<img class="noselect" src="<?= $ff_router->getPath('asset', [
								'asset' => 'simplicity-100x100',
								'extension' => 'png'
							]) ?>" alt="">
							<h3><?= $language->getPhrase('landing-simplicity') ?></h3>
							<p class="lead mb-0"><?= $language->getPhrase('landing-simplicity-description') ?></p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Subscription Plans -->
		<?php $plans = plan::getPlans(3); ?>
		<?php if ($plans && count($plans) >= 3): ?>
			<section class="bg-light text-center" style="padding-top: 4em; padding-bottom: 4em;">
				<div class="container">
					<div class="row">

						<?php $plan = $plans[1]; ?>
						<div class="col-lg-4" style="padding-top: 2em;">
							<div class="card">
							  <div class="card-header">
							    <?= ff_esc($plan->getName()) ?>
							  </div>
							  <div class="card-body">
									<h2 class="card-title"><?= currency::getCurrencyPrefix($plan->getCurrency()) ?><?= ff_esc($plan->monthlyPrice()) ?><sup><small>/mo</small></sup></h2>

									<ul class="list-group list-group-flush">
										<li class="list-group-item"><?= $language->getPhrase('purchase-concurrent-connections', ['concurrent-users' => $plan->getMaximumConcurrentConnections()]) ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-constant-support') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-unlimited') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-no-traffic-logs') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-servers-worldwide') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-cross-platform') ?></li>
									</ul>

									<sub class="card-text"><?= $language->getPhrase('misc-price-per', [
										'symbol' => currency::getCurrencyPrefix($plan->getCurrency()),
										'price' => $plan->getPrice(),
										'duration' => $plan->getDurationString(),
									]) ?></sub>

									<div style="margin-top: 1em">
										<?php if ($signedIn): ?>
											<a href="<?= ff_esc($ff_router->getPath('cp_payments_method', [
												'plan-id-name' => ff_idAndSubject($plan->getId(), $plan->getName())
											])) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-get-now') ?></a>
										<?php else: ?>
											<a href="<?= ff_esc($ff_router->getPath('register')) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-signup-sub') ?></a>
										<?php endif; ?>
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
									<h2 class="card-title"><?= currency::getCurrencyPrefix($plan->getCurrency()) ?><?= ff_esc($plan->monthlyPrice()) ?><sup><small>/mo</small></sup></h2>

									<ul class="list-group list-group-flush">
										<li class="list-group-item"><strong><?= $language->getPhrase('purchase-bast-value') ?></strong></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-concurrent-connections', ['concurrent-users' => $plan->getMaximumConcurrentConnections()]) ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-constant-support') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-unlimited') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-no-traffic-logs') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-servers-worldwide') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-cross-platform') ?></li>
									</ul>

									<sub class="card-text"><?= $language->getPhrase('misc-price-per', [
										'symbol' => currency::getCurrencyPrefix($plan->getCurrency()),
										'price' => $plan->getPrice(),
										'duration' => $plan->getDurationString(),
									]) ?></sub>

									<div style="margin-top: 1em">
										<?php if ($signedIn): ?>
											<a href="<?= ff_esc($ff_router->getPath('cp_payments_method', [
												'plan-id-name' => ff_idAndSubject($plan->getId(), $plan->getName())
											])) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-get-now') ?></a>
										<?php else: ?>
											<a href="<?= ff_esc($ff_router->getPath('register')) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-signup-sub') ?></a>
										<?php endif; ?>
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
									<h2 class="card-title"><?= currency::getCurrencyPrefix($plan->getCurrency()) ?><?= ff_esc($plan->monthlyPrice()) ?><sup><small>/mo</small></sup></h2>

									<ul class="list-group list-group-flush">
										<li class="list-group-item"><?= $language->getPhrase('purchase-concurrent-connections', ['concurrent-users' => $plan->getMaximumConcurrentConnections()]) ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-constant-support') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-unlimited') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-no-traffic-logs') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-servers-worldwide') ?></li>
										<li class="list-group-item"><?= $language->getPhrase('purchase-cross-platform') ?></li>
									</ul>

									<sub class="card-text"><?= $language->getPhrase('misc-price-per', [
										'symbol' => currency::getCurrencyPrefix($plan->getCurrency()),
										'price' => $plan->getPrice(),
										'duration' => $plan->getDurationString(),
									]) ?></sub>

									<div style="margin-top: 1em">
										<?php if ($signedIn): ?>
											<a href="<?= ff_esc($ff_router->getPath('cp_payments_method', [
												'plan-id-name' => ff_idAndSubject($plan->getId(), $plan->getName())
											])) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-get-now') ?></a>
										<?php else: ?>
											<a href="<?= ff_esc($ff_router->getPath('register')) ?>" class="btn btn-primary"><?= $language->getPhrase('misc-signup-sub') ?></a>
										<?php endif; ?>
									</div>
							  </div>
							</div>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- Image Showcases -->
		<section class="showcase">
			<div class="container container-fluid p-0">

				<div class="row no-gutters">
					<div class="col-lg-3 order-lg-2 text-white showcase-img" style="background-image: url('<?= ff_esc($ff_router->getPath('asset', [
						'asset' => 'gaming',
						'extension' => 'svg',
					])) ?>')"></div>
					<div class="col-lg-9 order-lg-1 my-auto showcase-text">
						<h2><?= $language->getPhrase('landing-games-optim') ?></h2>
						<p class="lead mb-0"><?= $language->getPhrase('landing-games-optim-desc') ?></p>
					</div>
				</div>


				<div class="row no-gutters">
					<div class="col-lg-3 text-white showcase-img" style="background-image: url('<?= ff_esc($ff_router->getPath('asset', [
						'asset' => 'protection',
						'extension' => 'svg',
					])) ?>')"></div>
					<div class="col-lg-9 my-auto showcase-text">
						<h2><?= $language->getPhrase('landing-privacy') ?></h2>
						<p class="lead mb-0"><?= $language->getPhrase('landing-privacy-desc') ?></p>
					</div>
				</div>

				<div class="row no-gutters">
					<div class="col-lg-3 order-lg-2 text-white showcase-img" style="background-image: url('<?= ff_esc($ff_router->getPath('asset', [
						'asset' => 'social-care',
						'extension' => 'svg',
					])) ?>')"></div>
					<div class="col-lg-9 order-lg-1 my-auto showcase-text">
						<h2><?= $language->getPhrase('landing-gradea-support') ?></h2>
						<p class="lead mb-0"><?= $language->getPhrase('landing-gradea-support-desc') ?></p>
					</div>
				</div>
			</div>
		</section>

		<!-- Testimonials -->
		<?php $reviews = review::getPreferredReviews(3, $language->languageCode()); ?>
		<?php if($reviews && count($reviews) >= 3): ?>
			<section class="testimonials text-center bg-light">
				<div class="container">
					<h2 class="mb-5"><?= $language->getPhrase('landing-review-title') ?></h2>
					<div class="row">
						<?php foreach ($reviews as $review): ?>
							<?php $reviewer = user::getUserById($review['user_id']); ?>
							<div class="col-lg-4">
								<div class="testimonial-item mx-auto mb-5 mb-lg-0">
									<h5 style="margin-bottom: 0px"><?= ff_esc($reviewer->getCensoredUsername()) ?></h5>

									<div style="margin-bottom: 10px">
										<img class="noselect" style="padding: 0px; margin: 0px; box-shadow: none;" src="<?= $ff_router->getPath('asset', [
											'asset' => 'stars_' . $review['stars'],
											'extension' => 'png'
										]) ?>" alt="">
									</div>

									<p class="font-weight-light mb-0"><?= ff_esc($review['body']) ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php snippets_footer::render() ?>
		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

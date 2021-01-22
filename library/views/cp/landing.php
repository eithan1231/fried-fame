<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

// Possible options for connection usage
$connectionOptions = [
	'1 week' => FF_WEEK,
	'1 month' => FF_MONTH,
	'3 months' => FF_MONTH * 3,
	'6 months' => FF_MONTH * 6,
	'1 year' => FF_YEAR,
];

// Getting announcments
$announcements = announcement::getActiveAnnouncements();
$user = $ff_context->getSession()->getActiveLinkUser();
$userGroup = $user->getGroup();
$userSubscription = $user->getSubscription();
$userSubscriptionPlan = ($userSubscription ? plan::getPlanById($userSubscription->subscrption_plan_id) : null);

// Getting connection information.
$connectionOption = $ff_request->get('connection-duration') ?: '';
if(!isset($connectionOptions[$connectionOption])) {
	$connectionOption = array_keys($connectionOptions)[0];
}
$connectionDuration = $connectionOptions[$connectionOption];
$connectionPercision = ff_calculatePrecision($connectionDuration);
$connectionStatistics = $user->getConnectionStatistics(
	FF_TIME  - $connectionDuration,
	$connectionDuration,
	$connectionPercision
);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc($ff_config->get('project-name')) ?></title>

		<?php snippets_cssincl::render([
			'include' => [
				'morris'
			]
		]) ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 10px">
				<?php snippets_invalidemail::render() ?>

				<?php if ($announcements && count($announcements) > 0): ?>
					<?php $announcement = $announcements[0]; ?>
					<div style="margin: 20px 0 20px" class="row">
						<div class="card" style="width: 100%;">
							<div class="card-header text-center">
								<?= $language->getPhrase('oneword-announcement') ?>
							</div>
							<div class="card-body">
								<h5 class="card-title text-center"><?= ff_esc($announcement->getSubject()) ?></h5>
								<hr>
								<p class="card-text">
									<?= $announcement->getCleanBody() ?>
								</p>
							</div>
							<?php if (count($announcements) > 1): ?>
								<div class="card-footer text-muted">
									<a href="<?= $ff_router->getPath('cp_announcements') ?>"><?= $language->getPhrase('cp-landing-view-other-announcements') ?></a>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="row">
	        <div class="col-sm-6 col-md-12 col-lg-6">
						<div class="card mb-4 box-shadow">
		          <div class="card-header text-center">
		            <h4 class="my-0 font-weight-normal"><?= $language->getPhrase('cp-landing-account-details') ?></h4>
		          </div>
							<div class="card-body" style="font-size: 1em;">
								<strong><?= $language->getPhrase('cp-landing-username-indication') ?></strong> <span class="float-right"><?= ff_esc($user->getUsername()) ?></span><br>
								<strong><?= $language->getPhrase('cp-landing-email-indication') ?></strong> <a href="mailto:<?= ff_esc($user->getEmail()) ?>" class="float-right"><?= ff_esc($user->getEmail()) ?></a><br>
								<?php if ($userSubscription): ?>
									<strong><?= $language->getPhrase('cp-landing-subscription-indication') ?></strong>
									<span class="float-right"><?= ff_esc($userSubscriptionPlan->getName()) ?></span><br>

									<strong><?= $language->getPhrase('cp-landing-subscription-exp-indication') ?></strong>
									<?php if (!$userSubscription->valid): ?>
										<!-- Expired -->
										<a class="float-right" href="<?= ff_esc($ff_router->getPath('cp_payments_plans')) ?>"><?= $language->getPhrase('oneword-expired') ?></a><br>
									<?php else: ?>
										<!-- Valid subscription, with a week or more left. -->
										<span class="float-right"><?= $user->date($user->dateFormat(), $userSubscription->expiry) ?></span><br>
									<?php endif; ?>
								<?php else: ?>
									<strong><?= $language->getPhrase('cp-landing-subscription-indication') ?></strong> <span class="float-right">N/A</span><br>
								<?php endif; ?>
								<strong><?= $language->getPhrase('cp-landing-user-group-indication') ?></strong> <span class="float-right"><?= ff_esc($userGroup->getName()) ?></span><br>
								<strong><?= $language->getPhrase('cp-landing-local-time-indication') ?></strong> <span class="float-right"><?= ff_esc($user->date($user->dateFormat())) ?></span><br>
							</div>
		        </div>
	        </div>

					<?php if (!$userSubscription || !$userSubscription->valid): ?>
						<div class="col-sm-6 col-md-12 col-lg-6">
							<div class="card mb-4 box-shadow">
			          <div class="card-header text-center">
			            <h4 class="my-0 font-weight-normal">Recommended Plan</h4>
			          </div>
			          <div class="card-body">
									... Coming soon
			          </div>
			        </div>
						</div>
					<?php endif; ?>

					<div class="col-sm-6 col-md-12 col-lg-6">
						<div class="card mb-4 box-shadow">
							<div class="card-header text-center">
								<h4 class="my-0 font-weight-normal">Knowledge Base</h4>
							</div>
							<div class="card-body">
								... Coming soon
							</div>
						</div>
					</div>

					<!--


					<div class="col-sm-6 col-md-12 col-lg-6">
						<div class="card mb-4 box-shadow">
		          <div class="card-header text-center">
		            <h4 class="my-0 font-weight-normal">Pro</h4>
		          </div>
		          <div class="card-body">
		          </div>
		        </div>
					</div>-->

					<?php if ($userSubscription): ?>
						<div class="col-sm-12 col-md-12 col-lg-12">
							<div class="card mb-4 box-shadow">
			          <div class="card-header text-center">
			            <h4 class="my-0 font-weight-normal"><?= $language->getPhrase('cp-landing-data-usage') ?></h4>
			          </div>
			          <div class="card-body">
									<form method="get">
										<select class="form-control form-control-sm" onchange="this.parentElement.submit()" name="connection-duration">
											<?php foreach ($connectionOptions as $key => $value): ?>
												<?php if ($key === $connectionOption): ?>
													<option value="<?= ($key) ?>" selected><?= ff_esc($key) ?></option>
												<?php else: ?>
													<option value="<?= ($key) ?>"><?= ff_esc($key) ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
										</select>
									</form>
									<hr>
									<div id="data-usage" style="height: 175px"></div>
			          </div>
			        </div>
						</div>
					<?php endif; ?>
	      </div>
			</div>
		</div>

		<?php snippets_scriptincl::render([
			'include' => [
				'raphael',
				'morris',
			]
		]) ?>

		<script type="text/javascript">
		window.addEventListener('load', e => {
			if(Morris) {
				let element = document.getElementById('data-usage');
				if(element) {
					Morris.Line(Object.assign(
						{ element },
						<?= json_encode([
						'data' => array_map(function($row) use(&$user) {
							$row['data_sent'] /= 1024;
							$row['data_sent'] /= 1024;
							$row['data_sent'] = round($row['data_sent'], 2);

							$row['data_received'] /= 1024;
							$row['data_received'] /= 1024;
							$row['data_received'] = round($row['data_received'], 2);

							$row['date'] = $user->date('F j, Y', $row['date']);
							return $row;
						}, $connectionStatistics),

						'xkey' => 'date',

						'ykeys' => [
							'data_sent',
							'data_received',
						],

						'labels' => [
							$language->getPhrase('misc-data-sent-mb'),
							$language->getPhrase('misc-data-received-mb'),
						],

						'parseTime' => false,
						'resize' => true,
						'redraw' => true,
					]) ?>));
				}
			}
		});
		</script>
	</body>
</html>
<?php $ff_response->stopOutputBuffer() ?>

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\feedback.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$pp = 128;
$index = $ff_request->get('index');
$index = $index ? intval($index) : 0;
$feedbackData = feedback::getFeedback($index, $pp);
$user = $ff_context->getSession()->getActiveLinkUser();
$group = $user->getGroup();

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-feedback', $language)) ?></title>

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

				<h1><?= $language->getPhrase('mod-feedback-title') ?></h1>

				<table class="table table-lg">
					<thead class="thead-light">
						<tr>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-username') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-body') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!$feedbackData): ?>
							<!-- ========================================================= -->
							<!-- No Results Found -->
							<tr>
								<td>
									<?= $language->getPhrase('oneword-no-results-found') ?>
								</td>
							</tr>
						<?php else: ?>
							<!-- ========================================================= -->
							<!-- Feedback found, output them.  -->
							<?php foreach($feedbackData as $feedback): ?>
								<?php $feedbackUser = user::getUserById($feedback['user_id']) ?>
								<tr>
									<td>
										<?= ff_esc($feedback['id']) ?>
									</td>

									<td>
										<?= ff_esc($user->date($user->dateFormat(), $feedback['date'])) ?>
									</td>

									<td>
										<?php if ($group->can('mod_users')): ?>
											<a href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
												'query' => [
													'user' => $feedbackUser->getId()
												]
											])) ?>"><?= ff_esc($feedbackUser->getUsername()) ?></a>
										<?php else: ?>
											<?= ff_esc($feedbackUser->getUsername()) ?>
										<?php endif; ?>
									</td>

									<td style="width: 60%;">
										<?= str_replace("\n", '<br />', ff_esc($feedback['body'])) ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
				<?php if ($feedbackData): ?>
					<div class="pagination pagination-sm">
						<li class="page-item <?= $index <= 0 ? 'disabled' : '' ?>">
							<a class="page-link" href="<?= $ff_router->getPath('cp_mod_feedback', [], [
								'query' => [
									'index' => $index - 1
								]
							]) ?>">
								&lt;&lt;
							</a>
						</li>

						<li class="page-item <?= (count($feedbackData) < $pp || !$feedbackData) ? 'disabled' : '' ?>">
							<a class="page-link" href="<?= $ff_router->getPath('cp_mod_feedback', [], [
								'query' => [
									'index' => $index + 1
								]
							]) ?>">
								&gt;&gt;
							</a>
						</li>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

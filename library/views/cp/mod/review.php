<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\review.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$filter = [
	'hide_deleted' => ff_stringToBool($ff_request->get('hide_deleted')),
	'hide_approved' => ff_stringToBool($ff_request->get('hide_approved')),
];
$index = $ff_request->get('index');
$index = $index ? intval($index) : 0;
$count = 32;
$reviews = review::getReviews($index, $count, $filter);
$latestReview = ($reviews
	? end($reviews)
	: null
);

$user = $ff_context->getSession()->getActiveLinkUser();
$group = $user->getGroup();

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-reviews', $language)) ?></title>

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

				<h1><?= $language->getPhrase('mod-reviews-title') ?></h1>

				<div>
					<a href="<?= $ff_router->getPath('cp_mod_review', [], [
						'query' => array_merge($_GET, [
							'hide_deleted' => !$filter['hide_deleted']
						])
					])?>">
						<?= ff_esc($language->getPhrase($filter['hide_deleted'] ? 'misc-show-deleted' : 'misc-hide-deleted')) ?>
					</a>
				</div>

				<div>
					<a href="<?= $ff_router->getPath('cp_mod_review', [], [
						'query' => array_merge($_GET, [
							'hide_approved' => !$filter['hide_approved']
						])
					])?>">
						<?= ff_esc($language->getPhrase($filter['hide_approved'] ? 'misc-show-approved' : 'misc-hide-approved')) ?>
					</a>
				</div>

				<table class="table table-lg">
					<thead class="thead-light">
						<tr>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-action') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-status') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-username') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-stars') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-body') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!$reviews): ?>
							<tr>
								<td>
									<?= $language->getPhrase('oneword-no-results-found') ?>
								</td>
							</tr>
						<?php else: ?>
							<!-- ========================================================= -->
							<?php foreach($reviews as $review): ?>
								<?php $reviewUser = user::getUserById($review['user_id']) ?>
								<tr>
									<td>
										<?= ff_esc($review['id']) ?>
									</td>

									<td>
										<?= ff_esc($user->date($user->dateFormat(), $review['date'])) ?>
									</td>

									<td>
										<form method="post">
											<input type="hidden" name="id" value="<?= $review['id'] ?>">
											<input type="hidden" name="return" value="<?= $ff_router->getPath('cp_mod_review', [], [
												'query' => $_GET,
												'mode' => 'host'
											]) ?>">

											<!-- NOTE: Sorry for this messy little block. No real clean way to do it haha -->
											<?php if (!$review['deleted']): ?>
												<button type="submit" class="ff-link" formaction="<?= $ff_router->getPath('post', [
													'security_token' => $ff_context->getSession()->getSecurityToken(),
													'action' => 'deletereview'
												]) ?>"><?= $language->getPhrase('oneword-delete') ?></button>

												<?php if (!$review['approved']): ?>
													/ <button type="submit" class="ff-link" formaction="<?= $ff_router->getPath('post', [
														'security_token' => $ff_context->getSession()->getSecurityToken(),
														'action' => 'approvereview'
													]) ?>"><?= $language->getPhrase('oneword-approve') ?></button>
												<?php endif; ?>
											<?php else: ?>
												<button type="submit" class="ff-link" formaction="<?= $ff_router->getPath('post', [
													'security_token' => $ff_context->getSession()->getSecurityToken(),
													'action' => 'undeletereview'
												]) ?>"><?= $language->getPhrase('oneword-undelete') ?></button>
											<?php endif; ?>
										</form>
									</td>

									<td>
										<?php if ($review['deleted']): ?>
											<span style="color: red"><?= $language->getPhrase('oneword-deleted') ?></span>
										<?php elseif ($review['approved']): ?>
											<span style="color: green"><?= $language->getPhrase('oneword-approved') ?></span>
										<?php else: ?>
											<?= $language->getPhrase('oneword-pending-action') ?>
										<?php endif; ?>
									</td>

									<td>
										<?php if ($group->can('mod_users')): ?>
											<a href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
												'query' => [
													'user' => $reviewUser->getId()
												]
											])) ?>"><?= ff_esc($reviewUser->getUsername()) ?></a>
										<?php else: ?>
											<?= ff_esc($reviewUser->getUsername()) ?>
										<?php endif; ?>
									</td>

									<td>
										<img src="<?= $ff_router->getPath('asset', [
											'asset' => "stars_{$review['stars']}",
											'extension' => 'png'
										]) ?>" alt="">
									</td>

									<td style="width: 45%;">
										<?= str_replace("\n", '<br />', ff_esc($review['body'])) ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
				<?php if ($reviews): ?>
					<div class="pagination pagination-sm">
						<li class="page-item <?= $index <= 0 ? 'disabled' : '' ?>">
							<a class="page-link" href="<?= $ff_router->getPath('cp_mod_review', [], [
								'query' => array_merge($_GET, [
									'index' => $index - $count
								])
							]) ?>">
								&lt;&lt;
							</a>
						</li>

						<li class="page-item <?= (count($reviews) < $count || !$reviews) ? 'disabled' : '' ?>">
							<a class="page-link" href="<?= $ff_router->getPath('cp_mod_review', [], [
								'query' => array_merge($_GET, [
									'index' => $latestReview['id']
								])
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

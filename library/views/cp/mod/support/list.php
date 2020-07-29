<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\support\list.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$subjectUser = $ff_request->get('subject');
$showClosed = $ff_request->get('show_closed');
$showDeleted = $ff_request->get('show_deleted');
$ascending = $ff_request->get('ascending');

if($subjectUser && is_numeric($subjectUser)) {
	$subjectUser = user::getUserById(intval($subjectUser));
	if(!$subjectUser) {
		$subjectUser = null;
	}
}
else {
	$subjectUser = null;
}
if($showDeleted) {
	$showDeleted = ff_stringToBool($showDeleted);
}
else {
	$showDeleted = false;
}
if($showClosed) {
	$showClosed = ff_stringToBool($showClosed);
}
else {
	$showClosed = false;
}
if($ascending) {
	$ascending = ff_stringToBool($ascending);
}
else {
	$ascending = true;
}


$language = $ff_context->getLanguage();
$user = $ff_context->getSession()->getActiveLinkUser();

$tickets = support_thread::getThreadsForAdmins($user, $subjectUser, [
	'closed' => $showClosed,
	'deleted' => $showDeleted,
	'order' => $ascending ? 'asc' : 'desc',
]);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-support-list', $language)) ?></title>

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

				<div style="margin-bottom: 30px">
					<h1><?= ff_esc($language->getPhrase('misc-admin-support-page-header')) ?></h1>

					<!-- Filter for hiding and showing deleted threads. -->
					<div>
						<a href="<?= $ff_router->getPath('cp_mod_support_list', [], [
							'query' => array_merge($_GET, [
								'show_deleted' => !$showDeleted
							])
						])?>">
							<?= ff_esc($language->getPhrase($showDeleted ? 'misc-hide-deleted' : 'misc-show-deleted')) ?>
						</a>
					</div>

					<!-- Filter for hiding and showing closed threads. -->
					<div>
						<a href="<?= $ff_router->getPath('cp_mod_support_list', [], [
							'query' => array_merge($_GET, [
								'show_closed' => !$showClosed
							])
						])?>">
							<?= ff_esc($language->getPhrase($showClosed ? 'misc-hide-closed' : 'misc-show-closed')) ?>
						</a>
					</div>
				</div>

				<?php if (!$tickets || count($tickets) === 0): ?>
					<h4><?= ff_esc($language->getPhrase('misc-no-results')) ?></h4>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table">
						  <thead class="thead-light">
						    <tr>
						      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-subject') ?><!-- Subject--></th>
						      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-last-reply') ?><!-- Last Reply--></th>
									<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-starter') ?><!-- Thread Starter--></th>
									<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-status') ?><!-- Status--></th>
						    </tr>
						  </thead>
						  <tbody>
								<?php foreach($tickets as $ticket): ?>
									<?php $threadObject = support_thread::getThreadById($ticket['id'], $user)->data ?>
									<?php $threadCreator = $threadObject->getUser() ?>
									<?php $recentPost = $threadObject->getRecentMostPost() ?>
									<?php $recentPostUser = user::getUserById($recentPost['user_id']) ?>
									<?php $threadLink = $ff_router->getPath('cp_mod_support_view', [
										'id' => $threadObject->getId()
									]) ?>

									<tr style="overflow: hidden; white-space: nowrap;" class="ff-table-light noselect" data-href="<?= ff_esc($threadLink) ?>" onclick="window.location = this.dataset.href">
										<th scope="row" style="width: 60%; text-overflow: ellipsis;">
											<!-- Subject -->
											<a href="<?= ff_esc($threadLink) ?>" class="clean-a">
												<?= ff_esc($ticket['subject']) ?>
											</a>
										</th>

										<td>
											<!-- Last reply -->
											<span style="font-size: 14px;">
												<?= $language->getPhrase('supportlist-by-date', [
													'username' => $recentPostUser->getUsername(),
													'date' => $user->date($user->dateFormat(), $recentPost['date'])
												]) ?>
											</span>
										</td>

										<td>
											<a href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
												'query' => [
													'user' => $threadCreator->getId()
												]
											])) ?>"><?= ff_esc($threadCreator->getUsername()) ?></a>
										</td>

										<td>
											<!-- Status -->
											<?php if($ticket['is_deleted']): ?>
												<span style="color: #f44336;"><?= $language->getPhrase('oneword-deleted') ?></span>
											<?php elseif($ticket['is_closed']): ?>
												<?= $language->getPhrase('oneword-closed') ?>
											<?php else: ?>
												<span style="color: #32ad32;"><?= $language->getPhrase('oneword-open') ?></span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
						  </tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $tickets);
?>

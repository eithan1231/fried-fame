<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\support\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$user = $ff_context->getSession()->getActiveLinkUser();
$group = $user->getGroup();
if($group->can('mod_support')) {
	$tickets = support_thread::getThreadsByUser($user);
}
else {
	// Not a support mod, hide deleted threads.
	$tickets = support_thread::getThreadsByUser($user, ['deleted' => false]);
}

if(!$tickets || count($tickets) === 0) {
	// No tickets, lets just redirect to create a new one.
	$ff_response->redirect($ff_router->getPath('cp_support_new'));
	return;
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-support-landing', $language)) ?></title>

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

				<div style="padding: 15px; padding-left: 0px;">
					<a class="btn btn-primary" href="<?= $ff_router->getPath('cp_support_new') ?>"><?= $language->getPhrase('misc-create-new-thread') ?></a>
				</div>
				<div class="table-responsive">
					<table class="table">
					  <thead class="thead-light">
					    <tr>
					      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-subject') ?><!-- Subject--></th>
					      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-last-reply') ?><!-- Last Reply--></th>
					      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-status') ?><!-- Status--></th>
					    </tr>
					  </thead>
					  <tbody>
							<?php foreach($tickets as $ticket): ?>
								<?php $threadObject = support_thread::getThreadById($ticket['id'], $user)->data ?>
								<?php $recentPost = $threadObject->getRecentMostPost() ?>
								<?php $recentPostUser = user::getUserById($recentPost['user_id']) ?>
								<?php $threadLink = $ff_router->getPath('cp_support_view', [
									'id-subject' => ff_idAndSubject($threadObject->getId(), $threadObject->getSubject())
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
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $tickets);
?>

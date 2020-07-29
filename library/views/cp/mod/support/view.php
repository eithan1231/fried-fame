<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\support\view.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$user = $ff_context->getSession()->getActiveLinkUser();
$group = $user->getGroup();
$language = $ff_context->getLanguage();
$parameters = ff_getViewParameters();
$thread = $parameters['thread'];
$posts = $thread->getPosts();
$security_token = $ff_context->getSession()->getSecurityToken(true);

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-support-view', $language, ['subject' => $thread->getSubject()])) ?></title>

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
				<h2><?= ff_esc($thread->getSubject()) ?></h2>

				<hr>

				<form method="post" style="margin: 20px 0 20px 0">
					<input type="hidden" name="support_thread_id" value="<?= ff_esc($thread->getId()) ?>">
					<?php if ($thread->isDeleted()): ?>
						<!-- THREAD IS CLOSED -->

						<button type="submit" class="btn btn-primary" formaction="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'undeletesupportthread'
						]) ?>"><?= $language->getPhrase('misc-undelete-thread') ?></button>
					<?php elseif ($thread->isClosed()): ?>
						<!-- THREAD IS CLOSED -->

						<button type="submit" class="btn btn-primary" formaction="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'opensupportthread'
						]) ?>"><?= $language->getPhrase('misc-open-thread') ?></button>
					<?php else: ?>
						<!-- THREAD IS OPEN -->

						<button type="submit" class="btn btn-danger" formaction="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'closesupportthread'
						]) ?>"><?= $language->getPhrase('misc-close-thread') ?></button>

						<button type="submit" class="btn btn-danger" formaction="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'deletesupportthread'
						]) ?>"><?= $language->getPhrase('misc-delete-thread') ?></button>
					<?php endif; ?>


				</form>

				<?php if($thread->isClosed()): ?>
					<small><?= $language->getPhrase('support-view-closed') ?></small>
				<?php else: ?>
					<hr>
					<form action="<?= $ff_router->getPath('post', [
						'security_token' => $security_token->getToken(),
						'action' => 'newmodsupportpost'
					]) ?>" method="post">

						<input type="hidden" name="thread" value="<?= $thread->getId() ?>">
						<input type="hidden" id="sv-internal-body" name="body">

						<div class="input-group mb-3">
							<div id="sv-body-pell" class="pell">
								<!--
									Content here will be removed when loaded pell. But in the
									case user has scripts disabled, this will act as a backup.
								-->
								<textarea class="form-control" name="body" rows="3" placeholder="<?= ff_esc($language->getPhrase('support-view-body-placeholder')) ?>"></textarea>
							</div>
						</div>

						<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
					</form>
				<?php endif; ?>

				<hr>
				<div>
					<h4><?= $language->getPhrase('oneword-thread-messages') ?></h4>

					<?php foreach($posts as $post): ?>
						<?php
						$postObject = support_post::getPostById($post['id']);
						$posterUserObject = $postObject->getUser();
						$posterGroupObject = $posterUserObject->getGroup();
						?>
						<div class="sv-message <?= $posterUserObject->getId() === $user->getId() ? 'sv-message-you' : 'sv-message-them' ?>">
							<div class="sv-info">
								<span class="sv-name">
									<?php if ($group->can('mod_users')): ?>
										<a style="color: inherit;" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
											'query' => [
												'user' => $posterUserObject->getId()
											]
										])) ?>"><?= ff_esc($posterUserObject->getUsername()) ?></a>
									<?php else: ?>
										<?= ff_esc($posterUserObject->getUsername()) ?>
									<?php endif; ?>
								</span>

								<span class="badge" style="background-color: <?= ff_esc($posterGroupObject->getColor()) ?>;">
									<?= ff_esc($posterGroupObject->getName()) ?>
								</span>

								<span class="sv-date">
									<?= ff_esc($user->date($user->dateFormat(), $postObject->getDate())) ?>
								</span>
							</div>
							<hr>
							<div class="sv-body">
								<!--
									getCleanBody is cleaned/escaped in the psot class, so dont
									add escaping here!
								-->
								<?= $postObject->getCleanBody() ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php snippets_scriptincl::render(['include' => ['pell']]) ?>
		<?php if ($ff_config->get('allow-html-support-posts')): ?>
			<script type="text/javascript">
				window.addEventListener('load', () => {
					let internalBody = document.getElementById('sv-internal-body');
					ff_pell.init({
						element: document.getElementById('sv-body-pell'),
						onChange: content => internalBody.value = content
					});
				});
			</script>
		<?php endif; ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $parameters, $thread, $posts, $security_token);
?>

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\support\view.php
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
		<title><?= ff_esc(ff_buildTitle('title-support-view', $language, ['subject' => $thread->getSubject()])) ?></title>

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

				<div>
					<h2><?= ff_esc($thread->getSubject()) ?></h2>
				</div>

				<?php if($thread->isClosed()): ?>
					<small><?= $language->getPhrase('support-view-closed') ?></small>
				<?php else: ?>
					<hr>
					<form action="<?= $ff_router->getPath('post', [
						'security_token' => $security_token->getToken(),
						'action' => 'newsupportpost'
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
						if(!$group->can('mod_support')) {
							if($post['is_deleted']) {
								// deleted, and isnt support moderator.
								continue;
							}
						}

						$postObject = support_post::getPostById($post['id']);
						$posterUserObject = $postObject->getUser();
						$posterGroupObject = $posterUserObject->getGroup();
						?>
						<div class="sv-message <?= $posterUserObject->getId() === $user->getId() ? 'sv-message-you' : 'sv-message-them' ?>">
							<div class="sv-info">
								<span class="sv-name">
									<?= ff_esc($posterUserObject->getUsername()) ?>
								</span>

								<?php if($posterGroupObject->can('mod_support')): ?>
									<span class="badge" style="background-color: <?= ff_esc($posterGroupObject->getColor()) ?>;">
										<?= ff_esc($posterGroupObject->getName()) ?>
									</span>
								<?php endif; ?>

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
								<?= $postObject->getCleanBody($postObject->getDate()) ?>
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
					let bodyPell = document.getElementById('sv-body-pell');
					if(internalBody && internalBody) {
						ff_pell.init({
							element: bodyPell,
							onChange: content => internalBody.value = content
						});
					}
				});
			</script>
		<?php endif; ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language, $user, $group, $parameters, $thread, $posts, $security_token);
?>

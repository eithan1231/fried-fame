<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\user\find.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

// Users name we want to find.
$queryUser = $ff_request->get('user');
$users = user::queryUsers($queryUser);
if(!$users || count($users) == 0) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_user_landing', [], [
		'query' => [
			'phrase' => 'misc-no-results'
		]
	]));
}
else if(count($users) == 1) {
	// There's one user found, let's just redirect to his profile.
	return $ff_response->redirect($ff_router->getPath('cp_mod_user_manage', [], [
		'query' => [
			'user' => $users[0]['user_id']
		]
	]));
}

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-user-query', $language)) ?></title>

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

				<h1 style="margin-bottom: 35px"><?= $language->getPhrase('mod-user-find-title', [
					'query' => $queryUser
				]) ?></h1>

				<div class="table-responsive">
					<table class="table">
					  <thead class="thead-light">
					    <tr>
					      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-username') ?></th>
								<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-email') ?></th>
								<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-group') ?></th>
					    </tr>
					  </thead>
					  <tbody>
							<?php foreach($users as $user): ?>
								<?php $profilePage = $ff_router->getPath('cp_mod_user_manage', [], [
									'query' => [
										'user' => $user['user_id']
									]
								]) ?>
								<tr style="overflow: hidden; white-space: nowrap;" class="ff-table-light noselect" data-href="<?= ff_esc($profilePage) ?>" onclick="window.location = this.dataset.href">
									<th scope="row" style="text-overflow: ellipsis;">
										<a href="<?= ff_esc($profilePage) ?>" class="clean-a">
											<?= ff_esc($user['user_username']) ?>
										</a>
									</th>

									<td>
										<?= ff_esc($user['user_email']) ?>
									</td>

									<td>
										<span style="color: <?= ff_esc($user['group_color']) ?>">
											<?= ff_esc($user['group_name']) ?>
										</span>
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
unset($language);
?>

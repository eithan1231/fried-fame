<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\user\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$security_token = $ff_context->getSession()->getSecurityToken(true);

// user count
$userCount = user::getUsersCount();

// generating page data from indexes and lengths
$page = intval($ff_request->get('page')) ?: 1;
$length = intval($ff_request->get('perpage')) ?: 100;

// User list data
$index = ($page - 1) * $length;
$pageCount = $userCount / $length;
$userList = user::getUsers($index, $length);


$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc($ff_config->get('project-name')) ?></title>

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

				<h1 style="margin-bottom: 35px"><?= $language->getPhrase('mod-user-title') ?></h1>

				<div class="card bg-light mb-3" style="max-width: 18rem;">
				  <div class="card-header"><?= $language->getPhrase('oneword-search-user') ?></div>
				  <div class="card-body">
						<form action="<?= $ff_router->getPath('cp_mod_user_find') ?>" method="GET">
							<div class="input-group input-group-sm mb-3">
							  <div class="input-group-prepend">
							    <span class="input-group-text" id="inputGroup-sizing-sm"><?= $language->getPhrase('oneword-username') ?></span>
							  </div>
							  <input type="text" name="user" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" required>
							</div>

							<button type="submit" class="btn btn-primary btn-sm btn-block"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
				  </div>
				</div>

				<?php if ($userList): ?>
					<div>
						<div class="table-responsive">
							<table class="table">
							  <thead class="thead-light">
							    <tr>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
							      <th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-username') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-email') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-group') ?></th>
							    </tr>
							  </thead>
							  <tbody>
									<?php foreach($userList as $user): ?>
										<?php $profilePage = $ff_router->getPath('cp_mod_user_manage', [], [
											'query' => [
												'user' => $user['id']
											]
										]) ?>
										<tr
										style="overflow: hidden; white-space: nowrap;"
										class="ff-table-light noselect"
										data-href="<?= ff_esc($profilePage) ?>"
										onclick="window.location = this.dataset.href">
											<td>
												<?= ff_esc($user['id']) ?>
											</td>

											<th scope="row" style="text-overflow: ellipsis;">
												<a href="<?= ff_esc($profilePage) ?>" class="clean-a">
													<?= ff_esc($user['username']) ?>
												</a>
											</th>

											<td>
												<?= ff_esc($user['email']) ?>
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
						<?php if ($userCount > $length): ?>
							<?php snippets_pagebuttons::render([
								'page' => $page,
								'perpage' => $length,
								'pagecount' => $pageCount
							]) ?>
						<?php endif; ?>
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

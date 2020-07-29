<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\package\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$user = $ff_context->getSession()->getActiveLinkUser();
$group = $user->getGroup();

$index = $ff_request->get('index');
if(!$index) { $index = 0; }
$count = 32;
$packages = packages::getPackages($index, $count);

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-package-list', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" stlye="padding-top: 10px;">
				<table class="table">
					<thead class="thead-light">
						<tr>
							<th style="border-bottom: 0px" scope="col">#</th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-creator') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-platform') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-version') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-size') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-download') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ($packages): ?>
							<?php foreach($packages as $package): ?>
								<?php $userSubject = user::getUserById($package['creator']) ?>
								<tr style="overflow: hidden; white-space: nowrap;" id="package_<?= ff_esc($package['id']) ?>">
									<th>
										<?= ff_esc($package['id']) ?>
									</th>

									<td><?= ff_esc($user->date($user->dateFormat(), $package['date'])) ?></td>

									<td>
										<?php if ($group->can('mod_users')): ?>
											<a href="<?= $ff_router->getPath('cp_mod_user_manage', [], [
												'query' => [
													'user' => $userSubject->getId()
												]
											]) ?>"><?= ff_esc($userSubject->getUsername()) ?></a>
										<?php else: ?>
											<?= ff_esc($userSubject->getUsername()) ?>
										<?php endif; ?>
									</td>

									<td><?= ff_esc($package['platform']) ?></td>

									<td><?= ff_esc($package['version']) ?></td>

									<td><?= ff_esc(ff_getSizeAsVisual($package['filesize'])) ?></td>

									<td>
										<a href="<?= $ff_router->getPath('cp_package_download', [
											'id' => $package['id'],
											'platform' => $package['platform'],
											'version' => $package['version'],
											'filename' => $package['filename'],
										]) ?>"><?= $language->getPhrase('oneword-download') ?></a>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<div class="pagination pagination-sm">
					<li class="page-item <?= $index <= 0 ? 'disabled' : '' ?>">
						<a class="page-link" href="<?= $ff_router->getPath('cp_mod_package_landing', [], [
							'query' => [
								'index' => $index - $count
							]
						]) ?>">
							&lt;&lt;
						</a>
					</li>

					<li class="page-item <?= (($packages && count($packages) < $count) || !$index) ? 'disabled' : '' ?>">
						<a class="page-link" href="<?= $ff_router->getPath('cp_payments_list', [], [
							'query' => [
								'page' => $index + $count
							]
						]) ?>">
							&gt;&gt;
						</a>
					</li>
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

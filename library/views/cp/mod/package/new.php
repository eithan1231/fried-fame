<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\package\new.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$packages = packages::getPackagePlatforms();

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-mod-new-package', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" stlye="padding-top: 10px;">
				<div class="card card-info">
					<div class="card-header">
						<h4><?= ff_esc($language->getPhrase('misc-upload-package')) ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'uploadpackage'
						]) ?>" enctype="multipart/form-data" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-platform')) ?></span>
								</div>
								<input name="platform" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-platform')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-version')) ?></span>
								</div>
								<input name="version" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-version')) ?>">
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-package')) ?></span>
								</div>
								<input name="package_file" type="file" class="form-control">
							</div>

							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>

						<?php if ($packages): ?>
							<hr>

							<div style="margin-top: 15px;">
								<table class="table">
									<thead class="thead-light">
										<tr>
											<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('misc-platform') ?></th>
											<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('misc-storage-consumption') ?></th>
											<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('misc-count') ?></th>
											<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('misc-latest-version') ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($packages as $package): ?>
											<tr style="overflow: hidden; white-space: nowrap;">
												<th>
													<?= ff_esc($package['platform']) ?>
												</th>

												<td>
													<?= ff_esc(ff_getSizeAsVisual($package['space'])) ?>
												</td>

												<td>
													<?= ff_esc($package['uploads']) ?>
												</td>

												<td>
													<?= ff_esc($package['recent_version']) ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						<?php endif; ?>
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

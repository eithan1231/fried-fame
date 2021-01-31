<?php


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$activeUser = $ff_context->getSession()->getActiveLinkUser();

$internalAPIs = internalapi::getInternalAPIList();
if(!$internalAPIs) {
	$ff_response->redirect($ff_router->getPath('cp_mod_internalapi_new'));
}

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-internalapi-list', $language)) ?></title>

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

				<h1><?= $language->getPhrase('mod-internalapi-list') ?></h1>
				<a href="<?= $ff_router->getPath('cp_mod_internalapi_new') ?>"><?= $language->getPhrase('mod-internalapi-list-create-new') ?></a>

				<table class="table table-lg">
					<thead class="thead-light">
						<tr>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-permit') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-creator') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-expires') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-enabled') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-action') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-auth-token') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($internalAPIs as $internalAPI): ?>
							<?php $internalAPIUser = $internalAPI->getUser() ?>
							<tr id="<?= ff_esc("internalapi_{$internalAPI->getId()}") ?>">
								<td>
									<?= ff_esc($internalAPI->getId()) ?>
								</td>

								<td>
									<?= ff_esc($internalAPI->getPermit()) ?>
								</td>

								<td>
									<?php if ($activeUser->getGroup()->can('mod_users')): ?>
										<a href="<?= $ff_router->getPath('cp_mod_user_manage', [], [
											'query' => [
												'user' => $internalAPIUser->getId()
											]
										])?>"><?= ff_esc($internalAPIUser->getUsername()) ?></a>
									<?php else: ?>
										<?= ff_esc($internalAPIUser->getUsername()) ?>
									<?php endif; ?>
								</td>

								<td>
									<?= ff_esc($activeUser->date('F j, Y', $internalAPI->getDate())) ?>
								</td>

								<td>
									<?= ff_esc($activeUser->date('F j, Y', $internalAPI->getExpiry())) ?>
								</td>

								<td>
									<?php if ($internalAPI->getEnabled()): ?>
										<?= $language->getPhrase('oneword-true') ?>
									<?php else: ?>
										<?= $language->getPhrase('oneword-false') ?>
									<?php endif; ?>
								</td>

								<td>
									<a href="<?= $ff_router->getPath('cp_mod_internalapi_edit', [], [
										'query' => [
											'id' => $internalAPI->getId()
										]
									])?>"><?= ff_esc($language->getPhrase('mod-internal-api-edit-view')) ?></a>
								</td>

								<td>
									<?= ff_censorToken($internalAPI->getToken()) ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

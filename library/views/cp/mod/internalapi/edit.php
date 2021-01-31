<?php
global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$activeUser = $ff_context->getSession()->getActiveLinkUser();

$id = intval($ff_request->get('id'));
if(!$id) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_internalapi_list'));
}

$internalAPI = internalapi::getInternalAPIById($id);
if(!$internalAPI) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_internalapi_list'));
}

// Audit log for viewing InternalAPI
audits_admin_internalapiview::insert($activeUser, $internalAPI);


$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-internalapi-edit', $language)) ?></title>

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

				<div class="card card-info">
					<div class="card-header">
						<h4><?= $language->getPhrase('mod-internalapi-edit-title') ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'internalapiedit'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<input type="hidden" name="id" value="<?= ff_esc($internalAPI->getId()) ?>">

							<div class="text-muted">
								<?= $language->getPhrase('mod-internalapi-edit-text', [
									'id' => $internalAPI->getId(),
									'token' => $internalAPI->getToken(),
									'enabled' => ($internalAPI->getEnabled()
										? $language->getPhrase('oneword-true')
										: $language->getPhrase('oneword-false')
									),
									'permit' => $internalAPI->getPermit(),
									'date' => $activeUser->date($activeUser->dateFormat(), $internalAPI->getDate()),
									'expiry' => $activeUser->date($activeUser->dateFormat(), $internalAPI->getExpiry()),
								]) ?>
								<hr>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-enabled') ?></span>
								</div>
								<select name="enable" class="form-control">
									<?php if ($internalAPI->getEnabled()): ?>
										<option value="1" selected><?= $language->getPhrase('oneword-true') ?></option>
										<option value="0"><?= $language->getPhrase('oneword-false') ?></option>
									<?php else: ?>
										<option value="1"><?= $language->getPhrase('oneword-true') ?></option>
										<option value="0" selected><?= $language->getPhrase('oneword-false') ?></option>
									<?php endif; ?>
								</select>
							</div>
							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>
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

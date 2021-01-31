<?php


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-internalapi-new', $language)) ?></title>

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
						<h4><?= $language->getPhrase('mod-internalapi-new-title') ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'internalapinew'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<div class="text-muted">
								<?= $language->getPhrase('mod-internalapi-new-text') ?>
								<hr>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-permit') ?></span>
								</div>
								<select name="permit" class="form-control">
									<?php foreach (internalapi::PERMIT_TYPES as $type): ?>
										<?php if ($type === $ff_request->get('type')): ?>
											<option selected value="<?= ff_esc($type) ?>"><?= ff_esc($type) ?></option>
										<?php else: ?>
											<option value="<?= ff_esc($type) ?>"><?= ff_esc($type) ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
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

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\language\edit.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$phraseId = $ff_request->get('id');

if(!$phraseId) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_language_new', [], [
		'query' => [
			'phrase' => 'misc-phrase-not-found'
		]
	]));
}

$language = $ff_context->getLanguage();
$security_token = new security_token($ff_context->getSession());
$languages = $ff_context->getLanguages();
$phrase = language::getPhraseInformation($phraseId);

if(!$phrase) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_language_new', [], [
		'query' => [
			'phrase' => 'misc-phrase-not-found'
		]
	]));
}

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-lang-edit', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render([
			'js_cfg' => [
				'route_post' => $ff_router->getPath('post', [
					'security_token' => $security_token->getToken(),
					'action' => '__action__'
				]),
			]
		]) ?>
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
						<h4><?= $language->getPhrase('mod-phrase-edit-title') ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'setphrase'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>
							<input type="hidden" name="phrase_revision" value="<?= ff_esc($phrase['rev'] + 1) ?>">
							<input type="hidden" name="response_type" value="cp_mod_language_edit">
							<input type="hidden" name="phrase_language" value="<?= ff_esc($phrase['language_code']) ?>">
							<input type="hidden" name="phrase_name" value="<?= ff_esc($phrase['phrase_name']) ?>">


							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-language') ?></span>
								</div>
								<!-- ==================== SORRY FOR THIS MESS!!!! ==================== -->
								<?php foreach ($languages as $__lang): ?>
									<?php if ($__lang->languageCode() == $phrase['language_code']): ?>
										<!-- This isn't used, just there for styling purposes. -->
										<input disabled type="text" class="form-control" value="<?= ff_esc($__lang->languageName()) ?>">
									<?php endif; ?>
								<?php endforeach; ?>
							</div>


							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-phrase-name') ?></span>
								</div>
								<!-- This isn't used, just there for styling purposes. -->
								<input disabled type="text" class="form-control" value="<?= ff_esc($phrase['phrase_name']) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-phrase') ?></span>
								</div>
								<textarea name="phrase_body" class="form-control" rows="8" cols="80"><?= ff_esc($phrase['phrase']) ?></textarea>
							</div>

							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>
				</div>
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

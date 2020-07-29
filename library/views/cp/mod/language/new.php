<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\language\new.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$language = $ff_context->getLanguage();
$security_token = new security_token($ff_context->getSession());
$languages = $ff_context->getLanguages();

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-lang-new', $language)) ?></title>

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
						<h4><?= $language->getPhrase('mod-lang-new-title') ?></h4>
					</div>
					<div class="card-body">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'setphrase'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>
							<input type="hidden" name="phrase_revision" value="0">
							<input type="hidden" name="response_type" value="cp_mod_language_new">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-language') ?></span>
								</div>
								<select class="form-control" name="phrase_language">
									<?php foreach ($languages as $key => $_lang): ?>
										<?php if ($_lang->languageCode() == $language->languageCode()): ?>
											<option selected value="<?= ff_esc($_lang->languageCode()) ?>"><?= ff_esc($_lang->languageName()) ?></option>
										<?php else: ?>
											<option value="<?= ff_esc($_lang->languageCode()) ?>"><?= ff_esc($_lang->languageName()) ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</div>


							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-phrase-name') ?></span>
								</div>
								<input name="phrase_name" type="text" class="form-control" placeholder="<?= $language->getPhrase('oneword-phrase-name') ?>" value="<?= ff_esc($ff_request->get('phrase-name')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= $language->getPhrase('oneword-phrase') ?></span>
								</div>
								<textarea name="phrase_body" class="form-control" rows="8" cols="80"></textarea>
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

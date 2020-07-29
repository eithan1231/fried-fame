<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\language\unfound.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

$unfoundPhrases = language::getUnfoundPhrases();
if(!$unfoundPhrases) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_language_list', [], [
		'query' => [
			'phrase' => 'misc-no-unfound-phrases'
		]
	]));
}

$language = $ff_context->getLanguage();
$languageCodeToName = function($code) {
	global $ff_context;
	$languages = $ff_context->getLanguages();
	foreach ($languages as $language) {
		if($language->languageCode() == $code) {
			return $language->languageName();
		}
	}
	return $code;
};
$security_token = new security_token($ff_context->getSession());

$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-lang-unfound', $language)) ?></title>

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

				<h1><?= $language->getPhrase('mod-lang-unfound-title') ?></h1>
				<table class="table table-sm">
					<thead>
						<tr>
							<th scope="col"><?= $language->getPhrase('oneword-phrase-name') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-phrase-preview-language') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-intended-language') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-update') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($unfoundPhrases as $unfoundPhrase): ?>
							<tr>
								<td>
									<?= ff_esc($unfoundPhrase['phrase_name']) ?>
								</td>

								<td>
									<?= ff_esc($languageCodeToName($unfoundPhrase['found_language_code'])) ?>
								</td>

								<td>
									<?= ff_esc($languageCodeToName($unfoundPhrase['not_found_language'])) ?>
								</td>

								<td>
									<div>
										<textarea style="margin-bottom: 5px;" class="form-control" rows="2" cols="100"><?= ff_esc($unfoundPhrase['found_phrase']) ?></textarea>

										<button data-phrasename="<?= ff_esc($unfoundPhrase['phrase_name']) ?>" data-phraselanguage="<?= ff_esc($unfoundPhrase['not_found_language']) ?>" data-phraserevision="<?= ff_esc($unfoundPhrase['found_rev']) ?>" class="btn btn-primary" onclick="ff_custom.admin.language.automaticHandleOutdated(this)">
											<?= $language->getPhrase('oneword-submit') ?>
										</button>
									</div>
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

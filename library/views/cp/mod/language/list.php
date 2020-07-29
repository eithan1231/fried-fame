<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\language\list.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

// haha
$filter = $_GET;

$page = intval($ff_request->get('page')
	? $ff_request->get('page')
	: 1
);
$perpage = 32;
$phrases = language::getPhrases(($page - 1) * $perpage, $perpage, $filter);
$phraseCount = language::getPhraseCount($filter);
$userObject = $ff_context->getSession()->getActiveLinkUser();

if(!$phrases) {
	return $ff_response->redirect($ff_router->getPath('cp_landing', [], [
		'query' => [
			'phrase' => 'misc-no-phrase-found'
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
		<title><?= ff_esc(ff_buildTitle('title-lang-list', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 10px">
				<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>
				<?php snippets_invalidemail::render() ?>

				<h1><?= $language->getPhrase('mod-phrase-list-title') ?></h1>
				<table class="table table-sm">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col"><?= $language->getPhrase('oneword-revision') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-language-code') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-phrase-name') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-phrase') ?></th>
							<th scope="col"><?= $language->getPhrase('oneword-options') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($phrases as $phrase): ?>
							<tr>
								<td>
									<?= ff_esc($phrase['id']) ?>
								</td>

								<td>
									<?= ff_esc($phrase['rev']) ?>
								</td>

								<td>
									<?= ff_esc($phrase['language_code']) ?>
								</td>

								<td>
									<?= ff_esc($phrase['phrase_name']) ?>
								</td>

								<td>
									<?= ff_esc($phrase['phrase']) ?>
								</td>

								<td>
									<a href="<?= $ff_router->getPath('cp_mod_language_edit', [], [
										'query' => [
											'id' => $phrase['id']
										]
									]) ?>"><?= $language->getPhrase('oneword-edit') ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php snippets_pagebuttons::render([
					'page' => $page,
					'perpage' => $perpage,
					'pagecount' => intval(ceil($phraseCount / $perpage)),
					'renderLink' => function($page, $perpage) {
						global $ff_router;
						return $ff_router->getPath('cp_mod_language_list', [], [
							'query' => [
								'page' => $page
							]
						]);
					}
				]) ?>
			</div>
		</div>

		<?php snippets_scriptincl::render() ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

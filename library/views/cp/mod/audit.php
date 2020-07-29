<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\audit.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

// The user whose audit history we are getting.
$auditHistoryUser;

// Filter crap
$filter = [];
if($uid = $ff_request->get('uid')) {
	if(is_numeric($uid)) {
		$filter['user_id'] = intval($uid);
		$uid = $filter['user_id'];
		$auditHistoryUser = user::getUserById($uid);
		if(!$auditHistoryUser) {
			// User not found
			return $ff_response->redirect($ff_router->getPath('cp_landing'));
		}
	}
	else {
		$uid = false;
	}
}

// Purge count cache
if($ff_request->get('purgecache') === 'do') {
	// Will purge cache
	$auditHistoryCount = audit::getAdminAuditHistoryCount($filter, false);

	// Redirect to this page without purge cache crap.
	$query = $_GET;
	unset($query['purgecache']);
	return $ff_response->redirect($ff_router->getPath('cp_mod_audit', [], [
		'query' => $query
	]));
}


$page = intval($ff_request->get('page')
	? $ff_request->get('page')
	: 1
);
$perpage = 32;
$auditHistory = audit::getAdminAuditHistory(($page - 1) * $perpage, $perpage, $filter);
$auditHistoryCount = audit::getAdminAuditHistoryCount($filter);
$userObject = $ff_context->getSession()->getActiveLinkUser();

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-audit', $language)) ?></title>

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

				<h1><?= $language->getPhrase('mod-audit-title') ?></h1>

				<p><?= $language->getPhrase('mod-audit-cache-notice', [
					'clear_cache_url' => $ff_router->getPath('cp_mod_audit', [], [
						'query' => array_merge([
							'purgecache' => 'do'
						], $_GET)
					])
				]) ?></p>

				<?php if (isset($auditHistoryUser) && $auditHistoryUser): ?>
					<h3><?= $language->getPhrase('mod-audit-on-user', [
						'group' => $auditHistoryUser->getGroup()->getName(),

						// TODO: add this when group cp page is done
						'user_mod_page' => '#DO_THIS_SHIT!!!SILENT',
						'username' => $auditHistoryUser->getUsername(),
					])?></h3>
				<?php endif; ?>

				<table class="table table-sm">
					<thead class="thead-light">
						<tr>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-action') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-username') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-group') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-information') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(!$auditHistory): ?>
							<!-- ========================================================= -->
							<!-- No Results Found -->
							<tr>
								<td>
									<?= $language->getPhrase('oneword-no-results-found') ?>
								</td>
							</tr>
						<?php else: ?>
							<!-- ========================================================= -->
							<!-- Audits found, output them.  -->
							<?php foreach($auditHistory as $audit): ?>
								<tr>
									<td>
										<?= ff_esc($audit['id']) ?>
									</td>

									<td>
										<?= ff_esc($userObject->date($userObject->dateFormat(), $audit['date'])) ?>
									</td>

									<td>
										<?= ff_esc($audit['name']) ?>
									</td>

									<td>
										<?= ff_esc($audit['user_name']) ?>
									</td>

									<td>
										<?php $auditUserGroup = group::getGroupById($audit['user_group_id']) ?>
										<?= ff_esc($auditUserGroup->getName()) ?>
									</td>

									<td>
										<?php audit::renderSnippetFromName($audit['name'], $audit['value']) ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
				<div style="margin-bottom: 15px;">
					<?php snippets_pagebuttons::render([
						'page' => $page,
						'perpage' => $perpage,
						'pagecount' => intval(ceil($auditHistoryCount / $perpage)),
						'renderLink' => (function($page, $perpage) {
							global $ff_router, $ff_request;

							$query = [];
							$query['page'] = $page;
							if($uid = $ff_request->get('uid')) {
								$query['uid'] = $uid;
							}

							return $ff_router->getPath('cp_mod_audit', [], [
								'query' => $query
							]);
						})
					]); ?>
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

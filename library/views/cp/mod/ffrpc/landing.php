<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\ffrpc\landing.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

if (!$ff_context->getSession()->getActiveLinkUser()->getGroup('mod_ffrpc')) {
	// Should never happen, but let's just check once again (checked other places
	// too.) We really just cannot risk anyone viewing this page, as it would pose
	// SERIOUS security issues!!!
	return;
}
$ffrpcs = ffrpc::getRpcList();
if(!$ffrpcs) {
	$ff_response->redirect($ff_router->getPath('cp_mod_ffrpc_new'));
}

$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-ffrpc-nodes', $language)) ?></title>

		<?php snippets_cssincl::render() ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
		<script type="text/javascript">
			// Most scripts go in ff_custom and are deferred, but this is locanized to
			// this function so it should be okay.
			function updateModal(elem) {
				document.getElementById('auth-token-modal-text').innerHTML = elem.dataset.token;
			}
		</script>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="modal fade" id="authTokenModal" tabindex="-1" role="dialog" aria-labelledby="authTitleLabel" aria-hidden="true">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="authTitleLabel"><?= $language->getPhrase('oneword-auth-token') ?></h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <span id="auth-token-modal-text" style="text-overflow:">... loading</span>
			      </div>
			    </div>
			  </div>
			</div>

			<div class="container" style="padding-top: 10px">
				<?php snippets_invalidemail::render() ?>

				<h1><?= $language->getPhrase('mod-ffrpc-list') ?></h1>
				<a href="<?= $ff_router->getPath('cp_mod_ffrpc_new') ?>"><?= $language->getPhrase('mod-ffrpc-list-create-new') ?></a>

				<table class="table table-lg">
					<thead class="thead-light">
						<tr>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-type') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-auth-token') ?></th>
							<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-endpoint') ?>:<?= $language->getPhrase('oneword-port') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ($ffrpcs): ?>
							<?php foreach($ffrpcs as $ffrpc): ?>
								<tr id="<?= ff_esc("rpc_{$ffrpc['id']}") ?>">
									<td>
										<?= ff_esc($ffrpc['id']) ?>
									</td>

									<td>
										<?= ff_esc($ffrpc['type']) ?>
									</td>

									<td>
										<button data-token="<?= ff_esc($ffrpc['auth_token']) ?>" onclick="updateModal(this)" type="button" class="btn btn-link" data-toggle="modal" data-target="#authTokenModal" style="padding:0;margin:0;">
											<?= $language->getPhrase('oneword-click-here') ?>
										</button>
										<!--<span onmouseout="this.textContent=this.dataset.unfocus" onmouseover="this.textContent=this.dataset.focus" data-focus="<?= ff_esc($ffrpc['auth_token']) ?>" data-unfocus="<?= $language->getPhrase('misc-hover') ?>"><?= $language->getPhrase('misc-hover') ?></span>-->
									</td>

									<td>
										<?= ff_esc("{$ffrpc['endpoint']}:{$ffrpc['port']}") ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
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

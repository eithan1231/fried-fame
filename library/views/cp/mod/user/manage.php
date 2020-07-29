<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\views\cp\mod\user\manage.php
//
// ======================================


global $ff_request, $ff_response, $ff_context, $ff_config, $ff_router;
$ff_response->setHttpHeader('Content-Type', 'text/html');

// Things relative to the user logged in (not user to be modified)
$user = $ff_context->getSession()->getActiveLinkUser();
$userGroup = $user->getGroup();
$security_token = $ff_context->getSession()->getSecurityToken(true);

// Getting the users object
$userSubject = user::getUserById($ff_request->get('user'));
if(!$userSubject) {
	return $ff_response->redirect($ff_router->getPath('cp_mod_user_landing', [], [
		'query' => [
			'phrase' => 'misc-user-not-found'
		]
	]));
}

$tab = $ff_request->get('tab') ? $ff_request->get('tab') : 'info';
$renderActive = function($alledgedTab) use($tab) {
	return $alledgedTab == $tab ? ' active' : '';
};

// Generating information for page
$userGroupSubject = $userSubject->getGroup();
$userSubjectConnectionStatistics = $userSubject->getConnectionStatistics();
$userGroups = group::getAllGroups();


$language = $ff_context->getLanguage();
$ff_response->startOutputBuffer();
?>
<!DOCTYPE html>
<html lang="<?= $language->languageCode() ?>" dir="<?= $language->languageTextDirection() ?>" style="--sidebar-width: <?= $ff_config->get('sidebar-width') ?>">
	<head>
		<meta charset="utf-8">
		<title><?= ff_esc(ff_buildTitle('title-user-manage', $language, ['user' => $userSubject->getUsername()])) ?></title>

		<?php snippets_cssincl::render([
			'include' => [
				'morris'
			]
		]) ?>
		<?php snippets_htmlheader::render() ?>
		<?php snippets_altlang::render() ?>
	</head>
	<body>
		<?php snippets_cp_sidebar::render() ?>

		<div id="sidebar-body">
			<?php snippets_navbar::render(['cp_landing' => 1, 'sidebar' => 1]) ?>

			<div class="container" style="padding-top: 10px">
				<?php snippets_invalidemail::render() ?>

				<h1 style="margin-bottom: 35px"><?= $language->getPhrase('mod-manage-user-title') ?></h1>
				<p>
					<?= $language->getPhrase('mod-manage-user', [
						'user' => $userSubject->getUsername()
					]) ?>
				</p>

				<div style="margin-bottom: 30px;">
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link<?= $renderActive('info') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'info'
								])
							])) ?>"><?= $language->getPhrase('misc-information') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('statistics') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'statistics'
								])
							])) ?>"><?= $language->getPhrase('misc-data-usage') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('group') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'group'
								])
							])) ?>"><?= $language->getPhrase('oneword-manage-usergroup') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('additional-auth') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'additional-auth'
								])
							])) ?>"><?= $language->getPhrase('oneword-additional-auth') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('audit-logs') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'audit-logs',
									'index' => 0,
									'above_id' => true
								])
							])) ?>"><?= $language->getPhrase('misc-audit-logs') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('password-email-history') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'password-email-history'
								])
							])) ?>"><?= $language->getPhrase('misc-password-and-email-history') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('payments') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'payments'
								])
							])) ?>"><?= $language->getPhrase('misc-payments') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('send-email') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'send-email'
								])
							])) ?>"><?= $language->getPhrase('misc-send-email') ?></a>
						</li>

						<li class="nav-item">
							<a class="nav-link<?= $renderActive('reviews') ?>" href="<?= ff_esc($ff_router->getPath('cp_mod_user_manage', [], [
								'query' => array_merge($_GET, [
									'tab' => 'reviews'
								])
							])) ?>"><?= $language->getPhrase('oneword-reviews') ?></a>
						</li>

					</ul>
				</div>

				<?php if ($tab === 'info'): ?>
					<!-- ========================== INFO TAB ========================== -->
					<div class="container">
						<h2><?= ff_esc($userSubject->getUsername()) ?></h2>
						<hr>

						<?php if ($userSubject->isPendingEmailVerification()): ?>
							<div style="margin-bottom: 7px;">
								<span style="color: red"><?= $language->getPhrase('mod-manage-email-verif-notice') ?></span>
							</div>
						<?php endif; ?>

						<div>
							<span style="width: 100px; display: inline-block;"><?= $language->getPhrase('oneword-email') ?>:</span>

							<a href="mailto:<?= ff_esc($userSubject->getEmail()) ?>"><?= ff_esc($userSubject->getEmail()) ?></a>
						</div>

						<div>
							<span style="width: 100px; display: inline-block;"><?= $language->getPhrase('oneword-group') ?>:</span>

							<!-- Inline JS ;S ewwwwww! -->
							<span onmouseout="this.style.color = null;" onmouseover="this.style.color = '<?= ff_esc($userGroupSubject->getColor()) ?>'">
								<?= ff_esc($userGroupSubject->getName()) ?>
							</span>
						</div>

						<div>
							<span style="width: 100px; display: inline-block;">Node Auth:</span>

							<!-- Sorry for the line breaks. wanted to make it look somewhat nice. -->
							<span
								onmouseout="this.textContent=this.dataset.unfocus"
								onmouseover="console.log(this); this.textContent=this.dataset.focus"
								data-focus="<?= ff_esc($userSubject->getNodeAuth()) ?>"
								data-unfocus="<?= ff_esc($language->getPhrase('mod-manage-node-auth-hover')) ?>">
								<?= ff_esc($language->getPhrase('mod-manage-node-auth-hover')) ?>
							</span>
						</div>
					</div>

				<?php elseif($tab === "statistics"): ?>
					<!-- ========================== STATISTICS TAB ========================== -->

					<div class="container">
						<h4><?= $language->getPhrase('oneword-data-usage') ?></h4>
						<div id="data-usage" style="min-height: 250px;"></div>
					</div>

				<?php elseif($tab === 'group'): ?>
					<!-- ========================== USERGROUP TAB ========================== -->

					<div class="container">
						<p>
							<?= $language->getPhrase('mod-manage-user-apart-group', [
								'user' => $userSubject->getUsername(),
								'group_name' => $userGroupSubject->getName(),
								'group_page' => 'ddfdfdfdfdfdfdfdf'//TODO: URL TO USERGROUP PAGE ========================================================
							]) ?>
						</p>

						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $security_token->getToken(),
							'action' => 'changeusergroup'
						]) ?>" method="post" style="width: 50%">
							<input type="hidden" name="user_id" value="<?= ff_esc($userSubject->getId()) ?>">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text"><?= $language->getPhrase('oneword-group') ?></span>
								</div>

								<select class="form-control" name="new_group" required>
									<option><?= ff_esc($userGroupSubject->getName()) ?></option>
									<?php foreach ($userGroups as $group): ?>
										<?php if ($group->getId() != $userGroupSubject->getId()): ?>
											<option value="<?= ff_esc($group->getId()) ?>"><?= ff_esc($group->getName()) ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>

								<div class="input-group-append">
									<button class="btn btn-primary" type="submit"><?= $language->getPhrase('oneword-submit') ?></button>
								</div>
							</div>
						</form>
					</div>

				<?php elseif($tab === 'additional-auth'): ?>
					<!-- ========================== ADDITIONAL AUTH TAB ========================== -->

					<div class="container">
						Temporary <?= $tab ?> page
					</div>

				<?php elseif($tab === 'audit-logs'): ?>
					<!-- ========================== AUDIT LOGS TAB ========================== -->

					<?php
					$filter = [
						'user_id' => $userSubject->getId(),
						'above_id' => ff_stringToBool($ff_request->get('above_id'))
					];
					$index = $ff_request->get('index');
					$index = $index ? intval($index) : 0;
					$perpage = 32;
					$auditHistory = audit::getAdminAuditHistory($index, $perpage, $filter);
					$auditHistoryLast = $auditHistory ? end($auditHistory) : null;
					$auditHistoryFirst = $auditHistory ? $auditHistory[0] : null;
					$auditHistoryCount = audit::getAdminAuditHistoryCount($filter);
					?>

					<div class="container">
						<table class="table table-sm">
							<thead class="thead-light">
								<tr>
									<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
									<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
									<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-action') ?></th>
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
												<?= ff_esc($user->date($user->dateFormat(), $audit['date'])) ?>
											</td>

											<td>
												<?= ff_esc($audit['name']) ?>
											</td>

											<td>
												<?php audit::renderSnippetFromName($audit['name'], $audit['value']) ?>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>

						<?php if ($auditHistory): ?>
							<div class="pagination pagination-sm">
								<li class="page-item <?= ($auditHistoryFirst['id'] <= 1 ? 'disabled' : '') ?>">
									<a class="page-link" href="<?= $ff_router->getPath('cp_mod_user_manage', [], [
										'query' => array_merge($_GET, [
											'index' => $auditHistoryFirst['id'] - 1,
											'above_id' => false,
										])
									]) ?>">
										&lt;&lt;
									</a>
								</li>

								<li class="page-item <?= (count($auditHistory) < $perpage || !$auditHistory) ? 'disabled' : '' ?>">
									<a class="page-link" href="<?= $ff_router->getPath('cp_mod_user_manage', [], [
										'query' => array_merge($_GET, [
											'index' => $auditHistoryLast['id'],
											'above_id' => true,
										])
									]) ?>">
										&gt;&gt;
									</a>
								</li>
							</div>
						<?php endif; ?>

					</div>

				<?php elseif($tab === 'password-email-history'): ?>
					<!-- ========================== PASSWORD HISTORY TAB ========================== -->

					<div class="container">
						Temporary <?= $tab ?> page
					</div>

				<?php elseif($tab === 'send-email'): ?>
					<!-- ========================== SEND EMAIL TAB ========================== -->

					<div class="container">
						<form action="<?= $ff_router->getPath('post', [
							'security_token' => $ff_context->getSession()->getSecurityToken(),
							'action' => 'sendmail'
						]) ?>" method="post">
							<?php snippets_alert::render(['phrase' => strval($ff_request->get('phrase', request::METHOD_GET))]) ?>

							<input type="hidden" id="sv-internal-body" name="body">
							<input type="hidden" name="user" value="<?= $userSubject->getId() ?>">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="width: 135px"><?= ff_esc($language->getPhrase('oneword-subject')) ?></span>
								</div>
								<input name="subject" type="text" class="form-control" placeholder="<?= ff_esc($language->getPhrase('oneword-subject')) ?>" autofocus>
							</div>

							<div class="input-group mb-3">
								<div id="sv-body-pell" class="pell">
									<!--
										Everything within this div should be removed when pell is
										loaded, what is here now, is acting as a backup in the event
										there is a javscript problem (no js browser, or error).
									-->
									<textarea class="form-control" name="body" rows="3" placeholder="<?= ff_esc($language->getPhrase('oneword-body')) ?>"></textarea>
								</div>
							</div>


							<button type="submit" class="btn btn-primary"><?= $language->getPhrase('oneword-submit') ?></button>
						</form>
					</div>

				<?php elseif($tab === 'reviews'): ?>
					<!-- ========================== REVIEW TAB ========================== -->

					<?php $reviews = review::getReviewsByUser($userSubject) ?>

					<div class="container">
						<?php if (!$reviews): ?>
							<h4><?= $language->getPhrase('oneword-no-results-found') ?></h4>
						<?php else: ?>
							<table class="table table-lg">
								<thead class="thead-light">
									<tr>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-id') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-date') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-action') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-status') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-stars') ?></th>
										<th style="border-bottom: 0px" scope="col"><?= $language->getPhrase('oneword-body') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($reviews as $review): ?>
										<?php $reviewUser = user::getUserById($review['user_id']) ?>
										<tr>
											<td>
												<?= ff_esc($review['id']) ?>
											</td>

											<td>
												<?= ff_esc($user->date($user->dateFormat(), $review['date'])) ?>
											</td>

											<td>
												<form method="post">
													<input type="hidden" name="id" value="<?= $review['id'] ?>">
													<input type="hidden" name="return" value="<?= $ff_router->getPath('cp_mod_user_manage', [], [
														'query' => $_GET,
														'mode' => 'host'
													]) ?>">

													<!-- NOTE: Sorry for this messy little block. No real clean way to do it haha -->
													<?php if (!$review['deleted']): ?>
														<button type="submit" class="ff-link" formaction="<?= $ff_router->getPath('post', [
															'security_token' => $ff_context->getSession()->getSecurityToken(),
															'action' => 'deletereview'
														]) ?>"><?= $language->getPhrase('oneword-delete') ?></button>

														<?php if (!$review['approved']): ?>
															/ <button type="submit" class="ff-link" formaction="<?= $ff_router->getPath('post', [
																'security_token' => $ff_context->getSession()->getSecurityToken(),
																'action' => 'approvereview'
															]) ?>"><?= $language->getPhrase('oneword-approve') ?></button>
														<?php endif; ?>
													<?php else: ?>
														<button type="submit" class="ff-link" formaction="<?= $ff_router->getPath('post', [
															'security_token' => $ff_context->getSession()->getSecurityToken(),
															'action' => 'undeletereview'
														]) ?>"><?= $language->getPhrase('oneword-undelete') ?></button>
													<?php endif; ?>
												</form>
											</td>

											<td>
												<?php if ($review['deleted']): ?>
													<span style="color: red"><?= $language->getPhrase('oneword-deleted') ?></span>
												<?php elseif ($review['approved']): ?>
													<span style="color: green"><?= $language->getPhrase('oneword-approved') ?></span>
												<?php else: ?>
													<?= $language->getPhrase('oneword-pending-action') ?>
												<?php endif; ?>
											</td>

											<td>
												<img src="<?= $ff_router->getPath('asset', [
													'asset' => "stars_{$review['stars']}",
													'extension' => 'png'
												]) ?>" alt="">
											</td>

											<td style="width: 45%;">
												<?= str_replace("\n", '<br />', ff_esc($review['body'])) ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>

				<?php elseif($tab === 'payments'): ?>
					<!-- ========================== PAYMENTS TAB ========================== -->

					<div class="container">
						Temporary <?= $tab ?> page
					</div>

				<?php endif; ?>


				<?php /* <div hidden>

					<!-- Linked Accounts -->
				  <div class="card" style="margin-bottom: 5px;">
				    <div class="card-header" id="linked-accounts-heading">
				      <h5 class="mb-0">
				        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-linked-accounts" aria-expanded="false" aria-controls="collapse-linked-accounts">
				          Linked Accounts
				        </button>
				      </h5>
				    </div>
				    <div id="collapse-linked-accounts" class="collapse" aria-labelledby="linked-accounts-heading" data-parent="#accordion">
				      <div class="card-body">
				        Payment history and information
				      </div>
				    </div>
				  </div>

					<!-- Reviews -->
				  <div class="card" style="margin-bottom: 5px;">
				    <div class="card-header" id="reviews-heading">
				      <h5 class="mb-0">
				        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-reviews" aria-expanded="false" aria-controls="collapse-reviews">
				          Reviews
				        </button>
				      </h5>
				    </div>
				    <div id="collapse-reviews" class="collapse" aria-labelledby="reviews-heading" data-parent="#accordion">
				      <div class="card-body">
				        Reviews
				      </div>
				    </div>
				  </div>

					<!-- Session Management -->
				  <div class="card" style="margin-bottom: 5px;">
				    <div class="card-header" id="session-management-heading">
				      <h5 class="mb-0">
				        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-session-management" aria-expanded="false" aria-controls="collapse-session-management">
				          Session Management
				        </button>
				      </h5>
				    </div>
				    <div id="collapse-session-management" class="collapse" aria-labelledby="session-management-heading" data-parent="#accordion">
				      <div class="card-body">
				        Reviews
				      </div>
				    </div>
				  </div>

					<!-- Support -->
				  <div class="card" style="margin-bottom: 5px;">
				    <div class="card-header" id="support-heading">
				      <h5 class="mb-0">
				        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse-support" aria-expanded="false" aria-controls="collapse-support">
				          Support Threads and Posts
				        </button>
				      </h5>
				    </div>
				    <div id="collapse-support" class="collapse" aria-labelledby="support-heading" data-parent="#accordion">
				      <div class="card-body">
				        Support
				      </div>
				    </div>
				  </div>
				</div>*/ ?>
			</div>
		</div>

		<?php snippets_scriptincl::render([
			'include' => [
				'pell',
				'raphael',
				'morris',
			]
		]) ?>

		<?php if ($tab === 'statistics'): ?>
			<script type="text/javascript">
			window.addEventListener('load', e => {
				if(Morris) {
					Morris.Line(<?= json_encode([
						'element' => 'data-usage',
						'data' => array_map(function($row) use(&$user) {
							$row['data_sent'] /= 1024;
							$row['data_sent'] /= 1024;
							$row['data_sent'] = round($row['data_sent'], 2);

							$row['data_received'] /= 1024;
							$row['data_received'] /= 1024;
							$row['data_received'] = round($row['data_received'], 2);

							$row['date'] = $user->date('F j, Y', $row['date']);
							return $row;
						}, $userSubjectConnectionStatistics),

						'xkey' => 'date',

						'ykeys' => [
							'data_sent',
							'data_received',
						],

						'labels' => [
							$language->getPhrase('misc-data-sent-mb'),
							$language->getPhrase('misc-data-received-mb'),
						],

						'parseTime' => false,
						'resize' => true,
						'redraw' => true,
					]) ?>);
				}
			});
			</script>
		<?php endif; ?>

		<?php if ($tab === 'send-email'): ?>
			<script type="text/javascript">
				window.addEventListener('load', () => {
					let internalBody = document.getElementById('sv-internal-body');
					ff_pell.init({
						element: document.getElementById('sv-body-pell'),
						onChange: content => internalBody.value = content
					});
				});
			</script>
		<?php endif; ?>
	</body>
</html>
<?php
$ff_response->stopOutputBuffer();
unset($language);
?>

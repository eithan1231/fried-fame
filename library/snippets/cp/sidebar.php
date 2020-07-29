<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\cp\sidebar.php
//
// ======================================


class snippets_cp_sidebar implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_router, $ff_config, $ff_context, $ff_request, $ff_response;

    $sidebarHidden = ff_stringToBool($ff_request->getCookie($ff_config->get('cookie-sidebar-hidden')));

		$language = $ff_context->getLanguage();
    $activeUser = $ff_context->getSession()->getActiveLinkUser();
    if(!$activeUser) {
      // ok.. tf.
      return;
    }
		$userGroup = $activeUser->getGroup();
		$subscription = $activeUser->getSubscription();

		$paramSet = function($key) use($parameters) {
			return isset($parameters[$key]);
		};

		?>

		<!-- <?= __CLASS__ ?> -->

    <div id="sidebar"<?= $sidebarHidden ? ' hidden' : ''?>>
      <div class="welcome noselect">
				<a href="<?= $ff_router->getPath('landing') ?>">
					<img class="noselect" src="<?= $ff_router->getPath('asset', [
						'extension' => 'png',
						'asset' => 'sidebar_company'
					], [], [
						'allowForceParam' => false
					]) ?>" alt="<?= ff_esc($ff_config->get('project-name')) ?>">
				</a>
      </div>
      <div class="navigation noselect">
				<div class="row">
          <span class="header"><?= $language->getPhrase('oneword-general') ?></span>
        </div>
				<div class="row">
					<a href="<?= $ff_router->getPath('cp_landing') ?>" class="text"><?= $language->getPhrase('oneword-home') ?></a>
				</div>
				<?php if ($userGroup->can('support')): ?>
					<div class="row">
	          <a href="<?= $ff_router->getPath('cp_support_landing') ?>" class="text"><?= $language->getPhrase('oneword-support') ?></a>
	        </div>
				<?php endif; ?>
				<?php if ($activeUser->canReview()): ?>
					<div class="row">
	          <a href="<?= $ff_router->getPath('cp_review') ?>" class="text"><?= $language->getPhrase('oneword-review') ?></a>
	        </div>
				<?php endif; ?>

				<?php if ($userGroup->can('purchase')): ?>
					<div class="row">
	          <span class="header"><?= $language->getPhrase('oneword-subscriptions') ?></span>
	        </div>
	        <div class="row">
	          <a href="<?= $ff_router->getPath('cp_payments_plans') ?>" class="text"><?= $language->getPhrase('oneword-purchase') ?></a>
	        </div>
					<div class="row">
	          <a href="<?= $ff_router->getPath('cp_giftcard') ?>" class="text"><?= $language->getPhrase('oneword-giftcodes') ?></a>
	        </div>
					<?php if ($subscription): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_payments_list') ?>" class="text"><?= $language->getPhrase('oneword-history') ?></a>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if (
					$subscription &&
					$subscription->valid
				): ?>
					<div class="row">
						<span class="header"><?= $language->getPhrase('oneword-guides') ?></span>
					</div>
					<div class="row">
						<a href="<?= $ff_router->getPath('cp_install_windows') ?>" class="text"><?= $language->getPhrase('oneword-windows') ?></a>
					</div>
					<div class="row">
						<a href="<?= $ff_router->getPath('cp_install_android') ?>" class="text"><?= $language->getPhrase('oneword-android') ?></a>
					</div>
					<div class="row">
						<a href="<?= $ff_router->getPath('cp_install_osx') ?>" class="text"><?= $language->getPhrase('oneword-mac-osx') ?></a>
					</div>
					<div class="row">
						<a href="<?= $ff_router->getPath('cp_install_linux') ?>" class="text"><?= $language->getPhrase('oneword-linux') ?></a>
					</div>
					<div class="row">
						<a href="<?= $ff_router->getPath('cp_install_ios') ?>" class="text"><?= $language->getPhrase('oneword-ios') ?></a>
					</div>
				<?php endif; ?>

				<?php if (true): ?>
					<div class="row">
	          <span class="header"><?= $language->getPhrase('oneword-settings') ?></span>
	        </div>
	        <div class="row">
	          <a href="<?= $ff_router->getPath('cp_settings_email') ?>" class="text"><?= $language->getPhrase('oneword-email') ?></a>
	        </div>
					<?php if ($activeUser->hasEmailHistory()): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_history_email') ?>" class="text"><?= $language->getPhrase('oneword-email-history') ?></a>
		        </div>
					<?php endif; ?>
					<div class="row">
						<a href="<?= $ff_router->getPath('cp_settings_password') ?>" class="text"><?= $language->getPhrase('oneword-password') ?></a>
	        </div>
					<?php if ($activeUser->hasPasswordHistory()): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_history_password') ?>" class="text"><?= $language->getPhrase('oneword-password-history') ?></a>
		        </div>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ($userGroup->can('mod_*')): ?>
					<?php if ($userGroup->can('mod_language')): ?>
						<!-- Language Management -->
						<div class="row">
		          <span class="header"><?= $language->getPhrase('oneword-language') ?></span>
		        </div>

						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_language_list') ?>" class="text"><?= $language->getPhrase('oneword-list') ?></a>
		        </div>

						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_language_new') ?>" class="text"><?= $language->getPhrase('oneword-new') ?></a>
		        </div>

						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_language_outdated') ?>" class="text"><?= $language->getPhrase('oneword-outdated') ?></a>
		        </div>

						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_language_unfound') ?>" class="text"><?= $language->getPhrase('oneword-unfound') ?></a>
		        </div>
					<?php endif; ?>

					<div class="row">
	          <span class="header"><?= $language->getPhrase('oneword-administration') ?></span>
	        </div>

					<?php if ($userGroup->can('mod_support')): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_mod_support_list') ?>" class="text"><?= $language->getPhrase('oneword-support') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_groups')): ?>
						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_group_landing') ?>" class="text"><?= $language->getPhrase('oneword-groups') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_users')): ?>
						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_user_landing') ?>" class="text"><?= $language->getPhrase('oneword-users') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_reviews')): ?>
						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_review') ?>" class="text"><?= $language->getPhrase('oneword-review') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_audit')): ?>
						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_audit') ?>" class="text"><?= $language->getPhrase('oneword-audit') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_announcement')): ?>
						<div class="row">
		          <a href="<?= $ff_router->getPath('cp_mod_announcement') ?>" class="text"><?= $language->getPhrase('oneword-announcement') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_feedback')): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_mod_feedback') ?>" class="text"><?= $language->getPhrase('misc-feedback') ?></a>
		        </div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_giftcode')): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_mod_giftcard') ?>" class="text"><?= $language->getPhrase('oneword-giftcodes') ?></a>
						</div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_ffrpc')): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_mod_ffrpc_landing') ?>" class="text"><?= $language->getPhrase('misc-ff-rpc') ?></a>
						</div>
					<?php endif; ?>

					<?php if ($userGroup->can('mod_packages')): ?>
						<div class="row">
							<a href="<?= $ff_router->getPath('cp_mod_package_landing') ?>" class="text"><?= $language->getPhrase('misc-package-list') ?></a>
						</div>

						<div class="row">
							<a href="<?= $ff_router->getPath('cp_mod_package_new') ?>" class="text"><?= $language->getPhrase('misc-new-package') ?></a>
						</div>
					<?php endif; ?>

				<?php endif; ?>
      </div>

			<div style="margin-top: 10px; text-align: center">
				<!-- All copyright text is not stored in phrases. -->
				<small>Copyright &copy; <?= $ff_config->get('project-name-short') ?></small>
			</div>
    </div>

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}

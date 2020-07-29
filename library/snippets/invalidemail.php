<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\invalidemail.php
//
// ======================================


class snippets_invalidemail implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_router, $ff_context, $ff_context, $ff_response;

		$language = $ff_context->getLanguage();
		$activeLinkUser = $ff_context->getSession()->getActiveLinkUser();
    if(!$activeLinkUser) {
      // This is intended for users to view, and no valid links... soo..
      return;
    }

		$security_token = $ff_context->getSession()->getSecurityToken(true);


		?>
		<!-- <?= __CLASS__ ?> -->

    <?php if(!$activeLinkUser->hasValidEmail()): ?>
      <div class="alert alert-danger" role="alert">
  			<?= $language->getPhrase('snippet-invalidemail', [
          'email_reset' => $ff_router->getPath('cp_settings_email')
        ]) ?>
  		</div>
		<?php elseif($activeLinkUser->isPendingEmailVerification()): ?>
			<form class="alert alert-danger" role="alert" method="post" action="<?= $ff_router->getPath('post', [
				'security_token' => $security_token->getToken(),
				'action' => 'resendemailverification'
			]) ?>">
				<?= $language->getPhrase('snippet-pendingemailverif', [
					'email_reset' => $ff_router->getPath('cp_settings_email')
				]) ?>
				<hr>
				<div>
					<button type="submit" class="btn btn-error">Click here to resend verification.</button>
				</div>
			</form>
		<?php endif; ?>

		<!-- <?= __CLASS__ ?> END -->
		<?php
	}
}

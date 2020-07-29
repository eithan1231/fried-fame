<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\htmlheader.php
//
// ======================================


class snippets_htmlheader implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_config, $ff_router, $ff_context, $ff_request;
		$languages = $ff_context->getLanguages();

		// Getting security token.
		$security_token = $ff_context->getSession()->getSecurityToken(true);

		$user = $ff_context->getSession()->getActiveLinkUser();
		$userSettings = null;
		$hiddenUserSettings = [];
		if($user) {
			$userSettings = settings::getByUser($user);
			if($userSettings) {
				$hiddenUserSettings = $userSettings->getOptions(
					settings::MANIPULATION_TYPE_AUTO,
					false
				);
			}
		}

		$retIfSet = function($key, $default) use($parameters) {
			if(isset($parameters[$key])) {
				return $parameters[$key];
			}
			return $default;
		};

		$viewport = $retIfSet('viewport', true);
		$manifest = $retIfSet('manifest', true);
		$description = $retIfSet('description', true);
		$favicon = $retIfSet('favicon', true);
		$themeColor = $retIfSet('theme-color', $ff_config->get('theme-color'));
		$cfg = [
			'debug' => ff_stringToBool($ff_config->get('development')),
			'sidebarVisibleCookieName' => $ff_config->get('cookie-sidebar-hidden'),
			'sidebarWidth' => $ff_config->get('sidebar-width'),
			'language' => $ff_context->getLanguage()->languageCode(),
			'autoSettings' => $hiddenUserSettings,
			'route_post' => $ff_router->getPath('post', [
				'security_token' => $security_token->getToken(),
				'action' => '__action__'
			]),
		];
		$cfgParams = $retIfSet('js_cfg', []);
		foreach ($cfgParams as $key => $value) {
			$cfg[$key] = $value;
		}

		?>

		<!-- <?= __CLASS__ ?> -->

		<?php if ($user && $userSettings): ?>

		<!-- Request ID: <?= ff_esc(FF_REQUEST_ID) ?>-->
		<!-- Server Timezone: <?= date_default_timezone_get() ?> -->
		<!-- Server Time: <?= date($user->dateFormat(), FF_TIME) ?> -->
		<!-- User Time: <?= $user->date($user->dateFormat(), FF_TIME) ?> -->

		<?php endif; ?>

		<script type="application/javascript">
			window.ff_config = <?= json_encode($cfg) ?>;
		</script>

		<?php if ($favicon): ?>
			<?php if (is_string($favicon)): ?>
				<link rel="shortcut icon" href="<?= ff_esc($favicon) ?>" />
			<?php else: ?>
				<link rel="shortcut icon" href="<?= $ff_router->getPath('asset', [
					'extension' => 'ico',
					'asset' => 'favicon'
				]) ?>" />
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($viewport): ?>
			<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php endif; ?>

		<?php if ($manifest): ?>
			<meta name="manifest" content="<?= $ff_router->getPath('manifest') ?>">
		<?php endif; ?>

		<?php if ($description): ?>
			<?php if (is_string($description)): ?>
				<meta name="description" content="<?= $ff_context->getLanguage()->getPhrase($description, [
					'project' => $ff_config->get('project-name'),
					'name' => $ff_config->get('project-name')
				]) ?>">
			<?php else: ?>
				<meta name="description" content="<?= $ff_context->getLanguage()->getPhrase('meta-description', [
					'project' => $ff_config->get('project-name')
				]) ?>">
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($themeColor): ?>
			<meta name="theme-color" content="<?= ff_esc($themeColor) ?>">
		<?php endif; ?>

		<!-- https://favicomatic.com/done !!! ty -->
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-57x57'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-114x114'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-72x72'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-144x144'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-60x60'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-120x120'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-76x76'
		]) ?>" />
		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_apple-touch-icon-152x152'
		]) ?>" />
		<link rel="icon" type="image/png" sizes="196x196" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_favicon-196x196'
		]) ?>" />
		<link rel="icon" type="image/png" sizes="96x96" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_favicon-96x96'
		]) ?>" />
		<link rel="icon" type="image/png" sizes="32x32" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_favicon-32x32'
		]) ?>" />
		<link rel="icon" type="image/png" sizes="16x16" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_favicon-16x16'
		]) ?>" />
		<link rel="icon" type="image/png" sizes="128x128" href="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_favicon-128'
		]) ?>" />
		<meta name="application-name" content="<?= $ff_config->get('project-name') ?>"/>
		<meta name="msapplication-TileColor" content="#FFFFFF" />
		<meta name="msapplication-TileImage" content="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_mstile-144x144'
		]) ?>" />
		<meta name="msapplication-square70x70logo" content="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_mstile-70x70'
		]) ?>" />
		<meta name="msapplication-square150x150logo" content="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_mstile-150x150'
		]) ?>" />
		<meta name="msapplication-wide310x150logo" content="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_mstile-310x150'
		]) ?>" />
		<meta name="msapplication-square310x310logo" content="<?= $ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'logos_mstile-310x310'
		]) ?>" />

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}

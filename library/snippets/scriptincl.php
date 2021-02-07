<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\scriptincl.php
//
// ======================================


class snippets_scriptincl implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_router, $ff_context, $ff_response, $ff_config;

		$user = $ff_context->getSession()->getActiveLinkUser();
		$userGroup = (!!$user
			? $user->getGroup()
			: null
		);

		$paramSet = function($key) use($parameters) {
			return isset($parameters[$key]);
		};

    $assets = ['jquery', 'propper', 'bootstrap', 'custom'];
    if($paramSet('include')) {
      $assets = array_merge($assets, $parameters['include']);
    }

		?>

		<!-- <?= __CLASS__ ?> -->
    <?php foreach($assets as $value): ?>
      <script type="text/javascript" src="<?= $ff_router->getPath('asset', [
  			'asset' => $value,
  			'extension' => 'js'
  		], ['allowForceParam' => false]) ?>"></script>
    <?php endforeach; ?>

		<?php if ($userGroup && $userGroup->can('mod_*')): ?>

			<script type="text/javascript">
				(function() {
					// Enter a debug mode for all moderators.
					window.addEventListener('load', ff_config.enterDebug);
				})();
			</script>

		<?php endif; ?>

		<?php if ($ff_config->get('google-analytics')): ?>
			<script src="https://www.googletagmanager.com/gtag/js?id=<?= urlencode($ff_config->get('google-analytics-tracking-id')) ?>"></script>
			<script>
			  window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config',<?= json_encode($ff_config->get('google-analytics-tracking-id')) ?>);
			</script>
		<?php endif; ?>

		<?php if ($ff_config->get('countly-analytics')): ?>
			<script type='text/javascript'>
			var Countly=Countly||{};Countly.q=Countly.q||[];Countly.app_key=<?= json_encode($ff_config->get('countly-analytics-api-key')) ?>;Countly.url=<?= json_encode($ff_config->get('countly-analytics-endpoint')) ?>;Countly.q.push(['track_sessions']);Countly.q.push(['track_pageview']);
			(function(){var cly=document.createElement('script');cly.type='text/javascript';cly.async=true;cly.src=<?= json_encode($ff_router->getPath('asset', [
  			'asset' => 'countly',
  			'extension' => 'js'
  		], ['allowForceParam' => false])) ?>;cly.onload=function(){Countly.init()};var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(cly,s);})();
			</script>
		<?php endif; ?>

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}

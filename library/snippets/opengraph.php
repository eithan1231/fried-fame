<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\opengraph.php
//
// ======================================


class snippets_opengraph implements snippets_snippets
{
	/**
	* Renders the snippet
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_config, $ff_router, $ff_request, $ff_context;

		$paramSet = function($key) use($parameters) {
			return isset($parameters[$key]);
		};
		$retIfSet = function($key, $default) use($paramSet, $parameters) {
			if($paramSet($key)) {
				return $parameters[$key];
			}
			return $default;
		};

		$echoOG = function($t, $default) use (&$retIfSet, &$parameters, &$paramSet) {
			global $ff_config, $ff_context;
			$langPhrase = $retIfSet($t, $default);
			$phrase = $ff_context->getLanguage()->getPhrase($langPhrase, [
				'name' => $ff_config->get('project-name')
			]);
			?>
			<meta property="og:<?= ff_esc($t) ?>" content="<?= ff_esc($phrase) ?>">
			<?php
		};

    ?>

		<!-- <?= __CLASS__ ?> -->
		<meta property="og:type" content="website">
		<meta property="og:site_name" content="<?= ff_esc($ff_config->get('project-name')) ?>">
		<meta property="og:image" content="<?= ff_esc($ff_router->getPath('asset', [
			'extension' => 'png',
			'asset' => 'ogimage'
		], ['mode' => 'host', 'allowForceParam' => false])) ?>">
		<meta property="og:url" content="<?= ff_esc($ff_request->getPath() . ($ff_request->getQuery() ? '?'. $ff_request->getQuery() : '')) ?>">
		<?php $echoOG('title', 'default-og-title') ?>
		<?php $echoOG('description', 'default-og-description') ?>

		<!-- <?= __CLASS__ ?> END -->
		<?php
	}
}

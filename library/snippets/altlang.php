<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\altlang.php
//
// ======================================


class snippets_altlang implements snippets_snippets
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
		$sessionLanguage = $ff_context->getLanguage();

		$path = $ff_request->getPath();
		$query = $ff_request->getQuery();

		$buildLangAltLink = function($lang) use ($path, $query) {
			$parsedQuery = [];
			parse_str($query, $parsedQuery);
			$parsedQuery['__lang'] = $lang;
			return "{$path}?". http_build_query($parsedQuery);
		};

		?>

		<!-- <?= __CLASS__ ?> -->

		<?php foreach($languages as $language): ?>
			<?php if ($sessionLanguage->languagecode() === $language->languageCode()): ?>
				<?php continue; ?>
			<?php endif; ?>
			<link rel="alternate" hreflang="<?= ff_esc($language->languageCode()) ?>" href="<?= ff_esc($buildLangAltLink($language->languageCode())) ?>" />
		<?php endforeach; ?>

		<!-- <?= __CLASS__ ?> END -->

		<?php
	}
}

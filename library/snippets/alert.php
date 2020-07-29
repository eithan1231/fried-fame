<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\snippets\alert.php
//
// ======================================


class snippets_alert implements snippets_snippets
{
	const ALERT_SUCCESS = 0;
	const ALERT_INFO = 1;
	const ALERT_WARNING = 2;
	const ALERT_DANGER = 3;


	/**
	* Renders the snippet
	*
	* NOTE: All phrases that strat with misc, or alert, they are considered trusted.
	*
	* @param array $parameters
	*		Parameters for the snippet
	*/
	public static function render(array $parameters = [])
	{
		global $ff_config, $ff_router, $ff_context, $ff_request;
		$language = $ff_context->getLanguage();

		$retIfSet = function($key, $default) use($parameters) {
			if(isset($parameters[$key])) {
				return $parameters[$key];
			}
			return $default;
		};

		$trustedPhrases = $retIfSet('trusted_phrases', []);
		$style = $retIfSet('style', self::ALERT_INFO);
		$phrase = $retIfSet('phrase', 'default');
		$phraseParameters = $retIfSet('phrase_parameters', []);

		if(
			!in_array($phrase, $trustedPhrases) &&
			strtolower(substr($phrase, 0, 4)) !== 'misc' &&
			strtolower(substr($phrase, 0, 5)) !== 'alert'
		) {
			// Untrusted phrase.
			return;
		}

		switch($style) {
			case self::ALERT_SUCCESS: $styleAttr = 'alert alert-success'; break;
			case self::ALERT_WARNING: $styleAttr = 'alert alert-warning'; break;
			case self::ALERT_DANGER: $styleAttr = 'alert alert-danger'; break;
			default: case self::ALERT_INFO: $styleAttr = 'alert alert-info'; break;
		}

		?>
		<!-- <?= __CLASS__ ?> -->

		<div class="<?= $styleAttr ?>" role="alert">
			<?= $language->getPhrase($phrase, $phraseParameters) ?>
		</div>

		<!-- <?= __CLASS__ ?> END -->
		<?php
	}
}

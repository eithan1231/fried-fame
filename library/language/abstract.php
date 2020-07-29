<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\language\abstract.php
//
// ======================================


abstract class language_abstract
{
	private static $phraseCache = [];

	/**
  * This will more than likely be the same throughout all language classes, so
  * lets just let all child classes call this.
	*/
	public function getPhrase(string $phraseName, array $parameters = [], $escapeParameters = true)
	{
		$phrase = $this->getPhraseBody($phraseName, $this->languageCode());
		if(!$phrase) {
			if (ff_isDevelopment()) {
				return 'Phrase not found: <u>'. ff_esc($phraseName) .'</u>';
			}
			else {
				return 'A phrase cannot be found.';
			}
		}

		// TODO: Do a more complicated templating.
		$ret = $phrase;
		$replace_1 = [];
		$replace_2 = [];
		foreach($parameters as $key => $value) {
			if(is_null($value)) {
				continue;
			}
			$replace_1[] = '{'. $key .'}';
			$replace_2[] = ($escapeParameters
				? ff_esc($value)
				: $value
			);
		}
		$ret = str_replace($replace_1, $replace_2, $ret);

		return $ret;
	}

	public function getPhraseBody(string $name, string $language)
	{
		global $ff_context, $ff_sql;
		$cache = $ff_context->getCache();
		$cacheKey = ff_getPhraseCacheKey($name, $language);
		if($cache->isInMemory() && $cached = $cache->get($cacheKey)) {
			return $cached;
		}

		$phrase = $ff_sql->query_fetch("
			SELECT
				`id`,
				`phrase`
			FROM `phrases`
			WHERE
				`phrase_name` = ". $ff_sql->quote($name) ." AND
				`language_code` = ". $ff_sql->quote($language) ."
		");

		if(!$phrase) {
			return null;
		}

		if($cache->isInMemory()) {
			$cache->store($cacheKey, $phrase['phrase'], FF_TIME + FF_DAY);
		}

		return $phrase['phrase'];
	}
}

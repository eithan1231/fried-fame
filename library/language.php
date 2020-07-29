<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\language.php
//
// ======================================


class language
{
	/**
	* Registered languages
	*/
	private $languages = [];

	public function __construct()
	{
		$this->registerLanguage(new language_english());
		$this->registerLanguage(new language_german());
		$this->registerLanguage(new language_portuguese());
		$this->registerLanguage(new language_spanish());
		$this->registerLanguage(new language_french());
		$this->registerLanguage(new language_chinese());
	}

	/**
	* Registers a new language.
	*/
	private function registerLanguage(language_interface $language)
	{
		if($language->isEnabled()) {
			$this->languages[] = $language;
		}
	}

	/**
	* Gets all the languages
	*/
	public function getLanguages()
	{
		return $this->languages;
	}

	/**
	* Gets the default language
	*/
	public function getDefault()
	{
		global $ff_config;
		$ret = $this->getLanguage($ff_config->get('session-default-language'));
		if(!$ret) {
			return $this->languages[0];
		}
		return $ret;
	}

	/**
	* Gets a language by it's 2 character code.
	*
	* @param string $code
	*		The code of the language you want to fetch.
	*/
	public function getLanguage(string $code)
	{
		$code = strtolower($code);
		foreach($this->languages as $lang) {
			if(strtolower($lang->languageCode()) == $code) {
				return $lang;
			}
		}

		return false;
	}

	public static function getUnfoundPhrases()
	{
		global $ff_context, $ff_sql;

		$return = [];

		$langCodes = [];
		foreach($ff_context->getLanguages() as $lang) {
			$langCodes[] = $lang->languageCode();
		}

		$latestPhrases = $ff_sql->query_fetch_all("
			SELECT
				`id`,
				`rev`,
				`language_code`,
				`phrase_name`,
				`phrase`
			FROM `phrases`
			GROUP BY `phrase_name`
			ORDER BY `rev` DESC
		", [
			'id' => 'int',
			'rev' => 'int'
		]);

		foreach($latestPhrases as $latestPhrase) {
			// $latestPhrase['id']
			// $latestPhrase['language_code']
			// $latestPhrase['phrase_name']
			// $latestPhrase['phrase']
			// $latestPhrase['rev']
			foreach ($langCodes as $lang) {

				$existQuery = $ff_sql->query_fetch("
					SELECT COUNT(1) AS cnt
					FROM `phrases`
					WHERE
						`phrase_name` = ". $ff_sql->quote($latestPhrase['phrase_name']) ." AND
						`language_code` = ". $ff_sql->quote($lang) ."
					LIMIT 1
				", [
					'cnt' => 'int'
				]);

				if($existQuery['cnt'] === 0) {
					$return[] = [
						'not_found_language' => $lang,
						'phrase_name' => $latestPhrase['phrase_name'],
						'found_rev' => $latestPhrase['rev'],
						'found_id' => $latestPhrase['id'],
						'found_language_code' => $latestPhrase['language_code'],
						'found_phrase' => $latestPhrase['phrase'],
					];
				}
			}
		}

		return (count($return) === 0 ? false : $return);
	}

	public static function getOutdatedPhrases()
	{
		global $ff_sql;

		//pri_* is the outdated one
		return $ff_sql->query_fetch_all("
			SELECT
				`pri`.`id` AS pri_id,
				`pri`.`rev` AS pri_rev,
				`pri`.`language_code` AS pri_language_code,
				`pri`.`phrase_name` AS pri_phrase_name,
				`pri`.`phrase` AS pri_phrase,
				`alt`.`id` AS alt_id,
				`alt`.`rev` AS alt_rev,
				`alt`.`language_code` AS alt_language_code,
				`alt`.`phrase_name` AS alt_phrase_name,
				`alt`.`phrase` AS alt_phrase
			FROM `phrases` AS pri
			INNER JOIN `phrases` AS alt
			ON
				alt.phrase_name = pri.phrase_name AND
				pri.language_code <> alt.language_code AND
				pri.rev < alt.rev
		");
	}

	public static function setPhrase(user $user, string $name, string $language, int $revision, string $body)
	{
		global $ff_sql, $ff_context;
		$userGroup = $user->getGroup();
		$cache = $ff_context->getCache();

		if(strlen($language) > 2) {
			// Invalid language
			return ff_return(false);
		}

		if(!$userGroup->can('mod_language')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		if($cache->isInMemory()) {
			$cache->delete(ff_getPhraseCacheKey($name, $language));
		}

		// Used to see whether phrase already exists.
		$checkResult = $ff_sql->query_fetch("
			SELECT
				`id`
			FROM `phrases`
			WHERE
				`phrase_name` = ". $ff_sql->quote($name) ." AND
				`language_code` = ". $ff_sql->quote($language) ."
		");

		if($checkResult != false) {
			// Phrase exists, update existing.

			$res = $ff_sql->query("
				UPDATE `phrases`
				SET `rev` = ". $ff_sql->quote($revision) .", `phrase` = ". $ff_sql->quote($body) ."
				WHERE
					`phrase_name` = ". $ff_sql->quote($name) ." AND
					`language_code` = ". $ff_sql->quote($language) ."
			");

			// Inserting audit log
			audits_admin_setphrase::insert($user, $checkResult['id'], $revision);

			return ff_return($res != false);
		}
		else {
			// Phrase doesnt exist.

			$res = $ff_sql->query("
				INSERT INTO `phrases`
				(`id`, `rev`, `language_code`, `phrase_name`, `phrase`)
				VALUES (
					NULL,
					". $ff_sql->quote($revision) .",
					". $ff_sql->quote($language) .",
					". $ff_sql->quote($name) .",
					". $ff_sql->quote($body) ."
				)
			");

			$id = $ff_sql->getLastInsertId();

			// Audit log.
			audits_admin_setphrase::insert($user, $id, $revision);

			return ff_return($res != false);
		}
	}

	public static function getPhraseInformation(int $id)
	{
		global $ff_sql;
		return $ff_sql->query_fetch("
			SELECT `id`, `rev`, `language_code`, `phrase_name`, `phrase`
			FROM `phrases`
			WHERE `id` = ". $ff_sql->quote($id) ."
		", [
			'id' => 'int',
			'rev' => 'int'
		]);
	}

	private static function getPhraseFilterAsWheres(array $filter)
	{
		global $ff_sql;
		$return = [];

		foreach ($filter as $key => $val) {
			switch ($key) {
				case 'language': {
					$return[] = 'language_code ='. $ff_sql->quote($val);
					break;
				}

				case 'search': {
					$ks = explode(' ', $val);
					$tmp = [];
					foreach($ks as $value) {
						$tmp[] = 'phrase_name LIKE \'%'. $ff_sql->escapeWildcard($ff_sql->escape($value)) .'%\'';
					}
					if(count($tmp) > 0) {
						$return[] = '('. implode(' OR ', $tmp) .')';
					}
					break;
				}

				default: break;
			}
		}

		return $return;
	}

	public static function getPhrases(int $offset, int $count, array $filter = [])
	{
		global $ff_sql;
		$where = self::getPhraseFilterAsWheres($filter);
		$where[] = 'id > '. $ff_sql->quote($offset);
		$where = ($where
			? 'WHERE '. implode(' AND ', $where)
			: ''
		);

		$ret = $ff_sql->query_fetch_all("
			SELECT
				`id`,
				`rev`,
				`language_code`,
				`phrase_name`,
				`phrase`
			FROM `phrases`
			$where
			LIMIT ". $ff_sql->quote($count) ."
		", [
			'id' => 'int',
			'rev' => 'int'
		]);

		return $ret;
	}

	public static function getPhraseCount(array $filter = [])
	{
		global $ff_sql, $ff_context;

		$where = self::getPhraseFilterAsWheres($filter);

		// Cache
		$cache = $ff_context->getCache();
		$cacheKey = ff_cacheKey(__CLASS__ . __FUNCTION__, $where);

		// Getting cache
		if($cached = $cache->get($cacheKey)) {
			return $cached;
		}

		// We haven't returned, so we're not cached. Let's get query, and cache it.

		// Building where conditions
		$where = ($where
			? 'WHERE '. implode(' AND ', $where)
			: ''
		);

		// Building and getting query
		$ret = $ff_sql->query_fetch("
			SELECT count(1) AS cnt
			FROM `phrases`
			$where
		", [
			'cnt' => 'int'
		]);

		// Storing cache
		$cache->store($cacheKey, $ret['cnt'], FF_TIME + (FF_MINUTE * 30));

		return $ret['cnt'];
	}
}

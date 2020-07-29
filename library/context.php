<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\context.php
//
// ======================================


class context
{
	/**
	* Whether this context object is in internal mode. Being true, this will not
	* create/load user sessions, as it is not needed.
	* @var bool
	*/
	private $internalMode = false;

	/**
	* The session object - prevents reoloading object.
	* @var session
	*/
	private $session = null;

	/**
	* The language object - prevents reoloading object.
	* @var language_interface
	*/
	private $language = null;

	/**
	* The captcha object - prevents reoloading object.
	* @var captcha_interface
	*/
	private $captcha = null;

	/**
	* The language object, initialized.
	* @var language
	*/
	private $languages = null;

	/**
	* The cacheing object.
	* @var cache_interface
	*/
	private $cache = null;

	/**
	* Useragent
	* @var useragent
	*/
	private $useragent = null;

	/**
	* Object that implements logger_interface
	* @var logger_interface
	*/
	private $logger = null;

	/**
	*
	*
	* @param bool $internalMode
	*		Being true, this will NOT get/create user sessions.
	*/
	function __construct(bool $internalMode = false)
	{
		global $ff_request;
		$this->internalMode = $internalMode;

		$tryIgnoreSession = $ff_request->getHeader('x-try-ignore-session') == 'true';
		if($this->internalMode) {
			$tryIgnoreSession = true;
		}

		/*
		// NOTE: Commented out our forcing session policy.
		// If x-try-ignore-session header is true, we will try and ignore the session.
		// but if code elsewhere requires a session, one will be used/created.
		if(!$tryIgnoreSession) {
			// Force the session to initialize
			$this->getSession();

			// Force load the language. NOTE: This calls getSession, so this is why we put this here.
			$this->getLanguage();
		}
		*/


		$this->getLogger();
	}

	/**
	* Gets the automatically set language object.
	*
	* @return language_interface the language object.
	*/
	public function getLanguage()
	{
		global $ff_request, $ff_response, $ff_config;

		$defaultLanguageCode = strtolower($ff_config->get('session-default-language'));

		if($this->language) {
			return $this->language;
		}

		if(!$this->languages) {
			$this->languages = new language();
		}

		if($this->internalMode) {
			// return default language.
			if($this->language = $this->languages->getLanguage($defaultLanguageCode)) {
				return $this->language;
			}

			throw new Exception("No languages can be found.");
		}

		// Checking with user selected language code __lang get parameter.
		$languageCode = $ff_request->get('__lang');
		if($this->language = $this->languages->getLanguage($languageCode)) {
			if (!$this->internalMode) {
				// Forced parameter for __lang can be set here, as this getLanguage
				// function is called on context init, and context init is called before
				// all views, and this is needed befoer ALL views. If this were called
				// after views have been generated, the __lang wouldn't be set in most
				// generated URL's.
				global $ff_router;
				$ff_router->setForcedQueryParameter('__lang', $languageCode);
			}
			return $this->language;
		}

		// TODO: Check Accept-Language header here.

		// Trying with session linked language code
		$languageCode = $this->getSession()->getLanguageCode();
		if($this->language = $this->languages->getLanguage($languageCode)) {
			return $this->language;
		}

		// Failed to get language linked with session, so therefore we should assume
		// default.
		if($this->language = $this->languages->getLanguage($defaultLanguageCode)) {
			return $this->language;
		}

		throw new Exception("No languages can be found.");
	}

	/**
	* Returns a object that implements logger_interface.
	*/
	public function getLogger()
	{
		global $ff_config;

		if($this->logger === null) {
			$this->logger = logger::getLogger($ff_config->get('logger'));
			if(!$this->logger) {
				throw new Exception('bad logger');
			}
		}

		return $this->logger;
	}

	/**
	* Gets an array of supported languages
	* @return array an array of languages
	*/
	public function getLanguages()
	{
		if($this->languages === null) {
			$this->languages = new language();
		}

		return $this->languages->getLanguages();
	}

	/**
	* Gets the session object.
	* @return session The session linked with the user. If doesnt exists, will be created
	*/
	public function getSession()
	{
		global $ff_request, $ff_response, $ff_config;

		if($this->internalMode) {
			throw new Exception('Session cannot be accessed from within internal mode.');
		}

		if($this->session) {
			return $this->session;
		}

		if($cookie = $ff_request->getCookie($ff_config->get('session-cookie'))) {
			$this->session = session::getSessionByToken($cookie);
		}

		if(!$this->session) {
			$this->session = session::createSession();
			$ff_response->setCookie(
				$ff_config->get('session-cookie'),
				$this->session->getToken(), [
					'expires' => $this->session->getExpiry()
				]
			);
		}
		return $this->session;
	}

	/**
	* Gets the enabled captcha object.
	* @return captcha_interface The enabled captcha interface
	*/
	public function getCaptcha()
	{
		global $ff_config;

		if($this->captcha) {
			return $this->captcha;
		}

		switch(strtolower($ff_config->get('catpcha-mode'))) {
			case 'none': {
				return $this->captcha = new captcha_none();
			}

			case 'recaptcha2': {
				return $this->captcha = new captcha_recaptcha2();
			}

			default: break;
		}

		throw new Exception('Captcha not found');
	}

	/**
	* Gets the configured cache object.
	*/
	public function getCache()
	{
		global $ff_config;
		if($this->cache) {
			return $this->cache;
		}

		return $this->cache = cache::getCacheByName($ff_config->get('cache-mode'));
	}

	/**
	* Gets clients useragent
	*/
	public function getUserAgent()
	{
		global $ff_request;
		if($this->useragent) {
			return $this->useragent;
		}
		if($ua = $ff_request->getHeader('user-agent')) {
			return $this->useragent = new useragent($ua);
		}
		return null;
	}
}

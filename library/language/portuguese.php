<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\language\portuguese.php
//
// ======================================


class language_portuguese extends language_abstract implements language_interface
{
	private $languageName = 'Português';
	private $languageCode = 'pt';
	private $textDirection = 'ltr';
	private $country = 'pt';

	/**
	* Checks whether the language is enabled.
	*/
	public function isEnabled()
	{
		return false;
	}

	/**
	* Gets the phrase for the selected language.
	*
	* @param string $phraseName
	*		The phrase you want to get.
	* @param array $parameters
	*		Parameters you want to get. May change the output of the phrase.
	* @param bool $escapeParameters
	*		Boolean as to whether or not we should html-encode the parameters values.
	*/
	public function getPhrase(string $phraseName, array $parameters = [], $escapeParameters = true)
	{
		return parent::getPhrase($phraseName, $parameters, $escapeParameters);
	}

	/**
	* The name of the language
	*/
	public function languageName()
	{
		return $this->languageName;
	}

	/**
	* The code for the language (I.E.: English=EN, )
	*/
	public function languageCode()
	{
		return $this->languageCode;
	}

	/**
	* Gets the country to which this language belongs to.
	* @return string ISO-2 code.
	*/
	public function getCountry()
	{
		return $this->country;
	}

	/**
	* Gets the direction of text. This should either return "LRT" (Left to right),
	* or "RTL" (Right to left).
	*/
	public function languageTextDirection()
	{
		return $this->textDirection;
	}
}

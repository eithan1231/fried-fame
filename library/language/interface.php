<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\language\interface.php
//
// ======================================


interface language_interface
{
	/**
	* Checks whether the language is enabled.
	*/
	public function isEnabled();

	/**
	* Gets the phrase for the selected language.
	*
	* @param string $phraseName
	*		The phrase you want to get.
	* @param array $parameters
	*		Parameters you want to get. May change the output of the phrase.
	* @param bool $escapeParameters
	*		Boolean as to whether or not we should html-encode the parameters values.
	*		NOTE: This will NOT escape the phrase!!!
	*/
	public function getPhrase(string $phraseName, array $parameters = [], $escapeParameters = true);

	/**
	* The name of the language
	*/
	public function languageName();

	/**
	* The code for the language (I.E.: English=EN, )
	*/
	public function languageCode();

	/**
	* Gets the country to which this language belongs to.
	* @return string ISO-2 code.
	*/
	public function getCountry();

	/**
	* Gets the direction of text. This should either return "LRT" (Left to right),
	* or "RTL" (Right to left).
	*/
  public function languageTextDirection();
}

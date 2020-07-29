<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\captcha\interface.php
//
// ======================================


/**
* Interfaces for captches, this is to help with future implementations of captchas.
*/
interface captcha_interface
{
	/**
	* Renders the script include HTML elements.
	*/
	public function renderScriptElements();

	/**
	* Renders the form elements
	*/
	public function renderFormElement($parameters = []);

	/**
	* Gets the parameter name. Like,
	*
	* If this returns empty, server will not authenticate. This is so that we can
	* implement a 'none' captcha, and have the server logic to complete it.
	*/
	public function getParmaterName();

	/**
	* Validates a parameter name to make sure the user input wasnt faked.
	*
	* @param string $parameterData
	*		The data of the parameter we want to verify
	*/
	public function validate(string $parameterData);
}

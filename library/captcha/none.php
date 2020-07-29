<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\captcha\none.php
//
// ======================================


class captcha_none implements captcha_interface
{
	/**
	* Renders the script include HTML elements.
	*/
	public function renderScriptElements()
	{

	}

	/**
	* Renders the form elements
	*/
	public function renderFormElement($parameters = [])
	{

	}

	/**
	* Gets the parameter name. Like,
	*/
	public function getParmaterName()
	{
		return '';
	}

	/**
	* Validates a parameter name to make sure the user input wasnt faked.
	*
	* @param string $parameterData
	*		The data of the parameter we want to verify
	*/
	public function validate(string $parameterData)
	{
		return true;
	}
}

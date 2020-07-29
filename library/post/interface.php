<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\interface.php
//
// ======================================


interface post_interface
{
	public function getName();

	/**
	* Runs a post route
	*/
	public function run(request &$request, response &$response);
}

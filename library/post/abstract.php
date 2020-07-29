<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\abstract.php
//
// ======================================


class post_abstract
{
	public function getName()
	{
		throw new Exception('Not Implemented');
	}

	/**
	* Verifies that:
	* - user is logged in
	* - user doesn't require reauthentication
	* - user doesn't require additional authentication
	* @return boolean
	*		true means all's well; false means that we've modified $response and that
	*		code execution for the run method shold not continue.
	*/
	public static function validateAuthenticated()
	{
		global $ff_context, $ff_router, $ff_response;
		$session = $ff_context->getSession();
		$activeLink = $session->getActiveLink();
		if(!$activeLink) {
			// nobody is logged in
			$ff_response->redirect($ff_router->getPath('login'));
			return false;
		}

		if($activeLink['require_reauth']) {
			$ff_response->redirect($ff_router->getPath('cp_reauth'));
			return false;
		}

		if(strlen($activeLink['pending_auth']) > 0) {
			$ff_response->redirect($ff_router->getPath('cp_additionalauth'));
			return false;
		}

		return true;
	}

	/**
	* Runs a post route
	*/
	public function run(request &$request, response &$response)
	{
		return false;
	}
}

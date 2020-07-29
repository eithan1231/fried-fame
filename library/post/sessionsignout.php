<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\sessionsignout.php
//
// ======================================


class post_sessionsignout extends post_abstract
{
	public function getName()
	{
		return ff_filename(__FILE__);
	}

	/**
	* Runs a post route
	*/
	public function run(request &$request, response &$response)
	{
		global $ff_context, $ff_router;

		$session = $ff_context->getSession();
		if(!$session) {
			// should never happen, but in the weird event it does, lets just 401.
			return false;
		}

		$session->logoutLink();

		$response->redirect($ff_router->getPath('landing'));

		return true;
	}
}

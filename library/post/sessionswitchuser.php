<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\sessionswitchuser.php
//
// ======================================


class post_sessionswitchuser extends post_abstract
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

		$password = $request->get('password', request::METHOD_POST);
		$userId = $request->get('user');
		if(!$userId) {
			return false;
		}

		if(!$password) {
			$password = '';
		}

		$user = user::getUserById(intval($userId));
		if(!$user) {
			return false;
		}

		$session = $ff_context->getSession();
		if(!$session) {
			// should never happen, but in the weird event it does, lets just 401.
			return false;
		}

		$status = $session->switchLink($user, $password);
		if($status->success) {
			if($status->data['additionalAuth']) {
				$response->redirect($ff_router->getPath('cp_additionalauth'));
			}
			else {
				$response->redirect($ff_router->getPath('cp_landing'));
			}
		}
		else {
			$response->redirect($ff_router->getPath('cp_landing', [
				'phrase' => $status->message
			]));
		}

		return true;
	}
}

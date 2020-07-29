<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\changepassword.php
//
// ======================================


class post_changepassword extends post_abstract
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

		if(!parent::validateAuthenticated()) {
			return true;
		}

		$old_password = $request->get('old_password', request::METHOD_POST);
		$new_password = $request->get('new_password', request::METHOD_POST);
		$retype_new_password = $request->get('retype_new_password', request::METHOD_POST);

		$send = function($p) use (&$response) {
			global $ff_router;
			if($p->success) {
				$response->redirect($ff_router->getPath('cp_settings_password', [], [
					'query' => [
						'phrase' => 'misc-success'
					]
				]));
			}
			else {
				$response->redirect($ff_router->getPath('cp_settings_password', [], [
					'query' => [
						'phrase' => $p->message
					]
				]));
			}
			return true;
		};

		if(
			!$old_password ||
			!$new_password ||
			!$retype_new_password
		) {
			return $send(ff_return(false, [], 'misc-parameters-missing'));
		}

		$user = $ff_context->getSession()->getActiveLinkUser();
		if(!$user) {
			return $send(ff_return(false, [], 'misc-permission-denied'));
		}

		if($new_password != $retype_new_password) {
			return $send(ff_return(false, [], 'misc-new-passwords-dont-match'));
		}

		return $send($user->updatePassword($old_password, $new_password));

		return true;
	}
}

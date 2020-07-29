<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\undeletesupportthread.php
//
// ======================================


class post_undeletesupportthread extends post_abstract
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

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			// Not Authenticated.
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		if(!$user->getGroup()->can('mod_support')) {
			$response->setHttpStatus(401);
			return true;
		}

		// getting user input
		$supportThreadId = $request->get('support_thread_id', request::METHOD_POST);
		if(!$supportThreadId || !is_numeric($supportThreadId)) {
			$response->redirect($ff_router->getPath('cp_mod_support_list'));
		}

		$supportThreadId = intval($supportThreadId);
		$thread = support_thread::getThreadById($supportThreadId, $user);
		if(!$thread) {
			$response->redirect($ff_router->getPath('cp_landing'));
		}
		$thread = $thread->data;

		$actionState = $thread->undelete($user);
		if($actionState->success) {
			$response->redirect($ff_router->getPath('cp_mod_support_view', [
				'id' => $supportThreadId
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_support_view', [
				'id' => $supportThreadId
			]));
		}


		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\newsupport.php
//
// ======================================


class post_newsupport extends post_abstract
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

		$body = $request->get('body', request::METHOD_POST);
		$subject = $request->get('subject', request::METHOD_POST);

		$thread = support_thread::newThread($user, $subject, $body);
		if($thread->success) {
			$response->redirect($ff_router->getPath('cp_support_view', [
				'id-subject' => ff_idAndSubject($thread->data['id'], $subject)
			]));
			return true;
		}
		else {
			$response->redirect($ff_router->getPath('cp_support_new', [], [
				'query' => [
					'phrase' => $thread->messageKey
				]
			]));
			return true;
		}

		return true;
	}
}

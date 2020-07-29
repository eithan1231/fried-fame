<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\newmodsupportpost.php
//
// ======================================


class post_newmodsupportpost extends post_abstract
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

		// getting user input
		$body = $request->get('body', request::METHOD_POST);
		$threadId = $request->get('thread', request::METHOD_POST);

		if(!is_numeric($threadId)) {
			// not an id.. tf.
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$thread = support_thread::getThreadById($threadId, $user);
		$threadObject = $thread->data;
		if($thread->success) {
			$post = $threadObject->newPost($user, $body);
			if($post->success) {
				$response->redirect($ff_router->getPath('cp_mod_support_view', [
					'id' => $threadObject->getId()
				]));
			}
			else {
				$response->redirect($ff_router->getPath('cp_mod_support_view', [
					'id' => $threadObject->getId()
				], [
					'query' => [
						'phrase' => $post->messageKey
					]
				]));
			}
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_support_view', [], [
				'query' => [
					'phrase' => $thread->messageKey
				]
			]));
		}

		return true;
	}
}

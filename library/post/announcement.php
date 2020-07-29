<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\announcement.php
//
// ======================================


class post_announcement extends post_abstract
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
		if(!$session) {
			// Invalid Session? tf.
			$response->redirect($ff_router->getPath('landing'));
			return true;
		}

		$user = $session->getActiveLinkUser();
		if(!$user) {
			// Not Authenticated.
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		$subject = $request->get('subject', request::METHOD_POST);
		$body = $request->get('body', request::METHOD_POST);
		$duration = intval($request->get('duration', request::METHOD_POST));

		if(!$subject || !$body || !$duration) {
			$response->redirect($ff_router->getPath('cp_mod_announcement', [], [
				'query' => [
					'phrase' => 'misc-param-not-found'
				]
			]));
		}

		$announcement = announcement::createAnnouncement($user, $subject, $body, $duration);
		if($announcement->success) {
			$response->redirect($ff_router->getPath('cp_announcements'));
			return true;
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_announcement', [], [
				'query' => [
					'phrase' => $announcement->messageKey
				]
			]));
			return true;
		}

		return true;
	}
}

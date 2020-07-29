<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\sendmail.php
//
// ======================================


class post_sendmail extends post_abstract
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

		$subject = $request->get('subject', request::METHOD_POST);
		$body = $request->get('body', request::METHOD_POST);
		$userSubjectId = intval($request->get('user', request::METHOD_POST));

		if(!$subject || !$body || !$userSubjectId) {
			$response->redirect($ff_router->getPath('cp_mod_user_manage', [], [
				'query' => [
					'phrase' => 'misc-param-not-found',
					'user' => $userSubjectId,
					'tab' => 'send-email'
				]
			]));
		}

		$userSubject = user::getUserById($userSubjectId);

		// dogy as fuck... but it's 2019 and who views plaintext emails? they are
		// mainly used for snippets (previews in notifications), and that's it. For
		// that purpose, this will function fine. Plus - this will still function
		// perfectly.
		$plainText = strip_tags(str_replace(
			["<br", "</div"],
			["\r\n<br", "\r\n</div"],
			$body
		));

		$res = $userSubject->sendEmail($user, $subject, $body, $plainText);
		if($res->success) {
			$response->redirect($ff_router->getPath('cp_mod_user_manage', [], [
				'query' => [
					'phrase' => 'misc-param-not-found',
					'user' => $userSubjectId,
				]
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_user_manage', [], [
				'query' => [
					'phrase' => 'misc-param-not-found',
					'user' => $userSubjectId,
					'tab' => 'send-email'
				]
			]));
		}

		return true;
	}
}

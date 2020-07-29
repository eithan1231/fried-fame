<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\changeemail.php
//
// ======================================


class post_changeemail extends post_abstract
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

		$userObject = $ff_context->getSession()->getActiveLinkUser();
		$password = $request->get('password', request::METHOD_POST);
		$email = $request->get('email', request::METHOD_POST);

		if(!$userObject) {
			$response->redirect($ff_router->getPath('landing'));
		}

		// Limit to 4 trys, over the spam of 15 minutes.
		$limit = 1;
		$limitTimespan = FF_HOUR;
		postlimiter::insertRequest(self::getName(), $request);
		if(postlimiter::exceedsRequestLimit(self::getName(), $request, $limit, $limitTimespan, $activeCount)) {
			// Redirect without warning. Fuck em.
			$response->redirect($ff_router->getPath('cp_settings_email', [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		// Checking username and password.
		if(empty($password) || empty($email)) {
			$response->redirect($ff_router->getPath('cp_settings_email', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		$emailChange = $userObject->changeEmail($password, $email);
		if($emailChange->success) {
			$response->redirect($ff_router->getPath('cp_landing', [], [
				'query' => [
					'phrase' => $emailChange->messageKey
				]
			]));
			return true;
		}
		else {
			$response->redirect($ff_router->getPath('cp_settings_email', [], [
				'query' => [
					'phrase' => $emailChange->messageKey
				]
			]));
			return true;
		}

		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\resendemailverification.php
//
// ======================================


class post_resendemailverification extends post_abstract
{
	public function getName()
	{
		// Should return xx, if __FILE__ is /blah/xx.php
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

		// Limit to 3 trys for within 1 hour.
		$limit = 3;
		$limitTimespan = FF_HOUR;
		postlimiter::insertRequest($this->getName(), $request);
		if(postlimiter::exceedsRequestLimit($this->getName(), $request, $limit, $limitTimespan, $activeCount)) {
			$response->redirect($ff_router->getPath('cp_landing', [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		$res = $user->sendEmailVerification();
		if($res->success) {
			$response->redirect($ff_router->getPath('cp_landing', [], [
				'query' => [
					'phrase' => 'misc-email-verif-sent'
				]
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_landing', [], [
				'query' => [
					'phrase' => 'misc-email-verif-failed'
				]
			]));
		}

		return true;
	}
}

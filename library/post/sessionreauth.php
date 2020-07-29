<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\sessionreauth.php
//
// ======================================


class post_sessionreauth extends post_abstract
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

		// Limit to 10 trys, over the spam of 30 minutes.
		$limit = 10;
		$limitTimespan = FF_MINUTE * 30;
		postlimiter::insertRequest(self::getName(), $request);
		if(postlimiter::exceedsRequestLimit(self::getName(), $request, $limit, $limitTimespan, $activeCount)) {
			$response->redirect($ff_router->getPath('cp_reauth', [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		$password = $request->get('password', request::METHOD_POST);
		if(!$password) {
			$response->redirect($ff_router->getPath('cp_reauth'));
			return true;
		}

		$session = $ff_context->getSession();
		$status = $session->reauth($password);
		if($status->success) {
			$response->redirect($ff_router->getPath('cp_landing'));
		}
		else {
			$response->redirect($ff_router->getPath('cp_reauth', [], [
				'query' => [
					'phrase' => $status->message
				]
			]));
		}

		return true;
	}
}

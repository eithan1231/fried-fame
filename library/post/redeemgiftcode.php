<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\redeemgiftcode.php
//
// ======================================


class post_redeemgiftcode extends post_abstract
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
		global $ff_config, $ff_context, $ff_router;

		if(!parent::validateAuthenticated()) {
			return true;
		}

		// limit to 10 tries per hour.
		$limit = 10;
		$limitTimespan = FF_HOUR;
		postlimiter::insertRequest(self::getName(), $request);
		if(postlimiter::exceedsRequestLimit(self::getName(), $request, $limit, $limitTimespan, $activeCount)) {
			$response->redirect($ff_router->getPath('cp_giftcard', [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			$response->redirect($return);
			return true;
		}

		// getting user input
		$code = $request->get('giftcode', request::METHOD_POST);
		if(!$code) {
			$response->redirect($ff_router->getPath('cp_giftcard', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		$redeem = giftcodes::redeem($user, $code);
		$response->redirect($ff_router->getPath('cp_giftcard', [], [
			'query' => [
				'phrase' => $redeem->message,
				'activation_message' => (isset($redeem->data['activation_message'])
					? $redeem->data['activation_message']
					: ''
				)
			]
		]));


		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\creategiftcodes.php
//
// ======================================


class post_creategiftcodes extends post_abstract
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

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		// getting user input
		$count = $request->get('count', request::METHOD_POST);
		$plan_id = $request->get('plan', request::METHOD_POST);
		$message = $request->get('message', request::METHOD_POST);
		if(!$count || !is_numeric($count) || !$plan_id) {
			$response->redirect($ff_router->getPath('cp_mod_giftcard', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		if(!$message) {
			$message = '';
		}

		// Validating and checking the plan
		$plan = plan::getPlanById($plan_id);
		if(!$plan) {
			$response->redirect($ff_router->getPath('cp_mod_giftcard', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		// Creating the giftcodes.
		$createdGiftcodes = giftcodes::create($user, $plan, $message, intval($count));
		if($createdGiftcodes->success) {
			$filename = $count .' '. $plan->getName() .'.txt';
			$response->setHttpHeader('Content-Type', 'text/plain');
			$response->setHttpHeader('Content-Disposition', "attachment; filename=\"$filename\"");

			foreach ($createdGiftcodes->data['codes'] as $code) {
				$response->appendBody("$code\r\n");
			}
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_giftcard', [], [
				'query' => [
					'phrase' => $createdGiftcodes->message
				]
			]));
			return true;
		}

		return true;
	}
}

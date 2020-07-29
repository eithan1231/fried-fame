<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\recovery.php
//
// ======================================


class post_recovery extends post_abstract
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
		$captcha = $ff_context->getCaptcha();
		$email = $request->get('email', request::METHOD_POST);

		if(!$email) {
			$response->redirect($ff_router->getPath('recovery', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		// Limit to 2 trys for within 5 hours.
		$limit = 2;
		$limitTimespan = FF_HOUR * 5;
		postlimiter::insertRequest(self::getName(), $request);
		if(postlimiter::exceedsRequestLimit(self::getName(), $request, $limit, $limitTimespan, $activeCount)) {
			$response->redirect($ff_router->getPath('recovery', [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		// Checking captcha
		if(!empty($captcha->getParmaterName())) {
			$parameterData = $request->get($captcha->getParmaterName(), request::METHOD_POST);
			$captchaValidation = $captcha->validate($parameterData);
			if(!$captchaValidation->success) {
				$response->redirect($ff_router->getPath('recovery', [], [
					'query' => [
						'phrase' => 'misc-invalid-captcha'
					]
				]));
				return true;
			}
		}

		$userObject = user::getUserByEmail($email);
		if(!$userObject) {
			// Invalid email, but we dont want the user to know... so let's show success
			// response.
			$response->redirect($ff_router->getPath('recovery', [], [
				'query' => [
					'phrase' => 'misc-recovery-sent'
				]
			]));
			return true;
		}

		$res = $userObject->pushRecoveryEmail();
		if($res->success) {
			$response->redirect($ff_router->getPath('recovery', [], [
				'query' => [
					'phrase' => 'misc-recovery-sent'
				]
			]));
		}
		else {
			$response->redirect($ff_router->getPath('recovery', [], [
				'query' => [
					'phrase' => 'misc-recovery-sent'
				]
			]));
		}

		return true;
	}
}

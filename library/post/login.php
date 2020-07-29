<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\login.php
//
// ======================================


class post_login extends post_abstract
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
		$username = $request->get('username', request::METHOD_POST);
		$password = $request->get('password', request::METHOD_POST);

		// Default redirect alterntative.
		$redirectAlternative = [
			'cp_landing' => 'cp_landing',
			'landing' => 'landing',
			'login' => 'login',
			'additionalauth' => 'cp_additionalauth'
		];

		// Checking for selected redirect alternative.
		if($request->get('returntype', request::METHOD_POST)) {
			switch(strtolower($request->get('returntype', request::METHOD_POST))) {

				/**
				* For windows.
				*/
				case 'windows': {
					$redirectAlternative = [
						'cp_landing' => 'containers_windows_landing',
						'landing' => 'containers_windows_landing',
						'login' => 'containers_windows_login',
						'additionalauth' => 'containers_windows_additionalauth'
					];
					break;
				}

				default: break;
			}
		}

		// Limit to 4 trys, over the spam of 15 minutes.
		$limit = 4;
		$limitTimespan = FF_MINUTE * 15;
		postlimiter::insertRequest(self::getName(), $request);
		if(postlimiter::exceedsRequestLimit(self::getName(), $request, $limit, $limitTimespan, $activeCount)) {
			// Redirect without warning. Fuck em.
			$response->redirect($ff_router->getPath($redirectAlternative['login'], [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		// Checking username and password.
		if(empty($username) || empty($password)) {
			$response->redirect($ff_router->getPath($redirectAlternative['login'], [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		// Checking captcha
		if(!empty($captcha->getParmaterName())) {
			$parameterData = $request->get($captcha->getParmaterName(), request::METHOD_POST);
			$captchaValidation = $captcha->validate($parameterData);
			if(!$captchaValidation->success) {
				$response->redirect($ff_router->getPath($redirectAlternative['login'], [], [
					'query' => [
						'phrase' => 'misc-invalid-captcha',
						'username' => $username
					]
				]));
				return true;
			}
		}

		$session = $ff_context->getSession();
		$linkState = $session->linkUser(
			strval($username),
			$password
		);

		if($linkState->success) {
			if($linkState->data['additionalAuth']) {
				$response->redirect($ff_router->getPath($redirectAlternative['additionalauth'], [], [
					'query' => [
						'phrase' => $linkState->messageKey,
					]
				]));
				return true;
			}
			else {
				$response->redirect($ff_router->getPath($redirectAlternative['cp_landing'], [], [
					'query' => [
						'phrase' => $linkState->messageKey,
					]
				]));
				return true;
			}
		}
		else {
			$response->redirect($ff_router->getPath($redirectAlternative['login'], [], [
				'query' => [
					'phrase' => $linkState->messageKey,
					'username' => $username
				]
			]));
			return true;
		}

		return true;
	}
}

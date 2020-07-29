<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\register.php
//
// ======================================


class post_register extends post_abstract
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
		$email = $request->get('email', request::METHOD_POST);
		$password = $request->get('password', request::METHOD_POST);
		$password2 = $request->get('password2', request::METHOD_POST);
		$mailingList = ff_stringToBool($request->get('mailing-list', request::METHOD_POST));
		$isFromEmailSub = ff_stringToBool($request->get('from-email-sub', request::METHOD_POST));

		if(
			empty($username) ||
			empty($email) ||
			empty($password) ||
			empty($password2)
		) {
			$response->redirect($ff_router->getPath('register', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing',
					'username' => strval($username),
					'email' => strval($email),
				]
			]));
			return true;
		}


		// Checking captcha
		if(!empty($captcha->getParmaterName())) {
			$parameterData = $request->get($captcha->getParmaterName(), request::METHOD_POST);
			if(!$parameterData) {
				// let's 404 them for not providing captcha data.
				return false;
			}
			$captchaValidation = $captcha->validate($parameterData);
			if(!$captchaValidation->success) {
				$response->redirect($ff_router->getPath('register', [], [
					'query' => [
						'phrase' => 'misc-invalid-captcha',
						'username' => $username,
						'email' => $email,
						'from-email-sub' => $isFromEmailSub,
					]
				]));
				return true;
			}
		}

		$result = user::newUser(
			$username,
			$email,
			$password,
			$password2,
			$mailingList
		);

		if($result->success) {
			// Link user with active session.
			$linkResult = $ff_context->getSession()->linkUser($result->data->id, $password);
			if($linkResult->success) {
				// Statement was: $linkResult->data->awaitingEmailVerification
				if($linkResult->data['additionalAuth']) {
					ff_postRedirectView($ff_router->getPath('landing', [], [
						'query' => [
							'phrase' => 'misc-pending-emailverif'
						]
					]));
				}
				else {
					ff_postRedirectView($ff_router->getPath('cp_landing'));
				}
			}
			else {
				$response->redirect($ff_router->getPath('login'));
			}
		}
		else {
			$response->redirect($ff_router->getPath('register', [], [
				'query' => [
					'phrase' => $result->messageKey,
					'username' => $username,
					'email' => $email,
					'from-email-sub' => $isFromEmailSub,
				]
			]));
		}

		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\additionalauth.php
//
// ======================================


class post_additionalauth extends post_abstract
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

		$session = $ff_context->getSession();
		$activeUserLink = $session->getActiveLink();
		if(!$activeUserLink) {
			// No active link with session, so there's not much we can do.
			$response->redirect($ff_router->getPath('landing'));
			return true;
		}

		// Getting user.
		$activeListUserObject = user::getUserById($activeUserLink['user_id']);
		if(!$activeListUserObject) {
			// Invalid user linked with session-link.
			$response->redirect($ff_router->getPath('landing'));
			return true;
		}

		// Default redirect alterntative.
		$redirectAlternative = [
			'cp_landing' => 'cp_landing',
			'landing' => 'landing',
			'cp_additionalauth' => 'cp_additionalauth'
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
						'cp_additionalauth' => 'containers_windows_additionalauth'
					];
					break;
				}

				default: break;
			}
		}

		// Checking user has additional auth
		$additionalAuth = additionalauth::getUserAuth($activeListUserObject);
		if(!$additionalAuth) {
			// Invalid user linked with session-link.
			$response->redirect($ff_router->getPath($redirectAlternative['landing'], [], [
				'query' => [
					'phrase' => 'misc-additional-auth-not-found'
				]
			]));
			return true;
		}

		$form = $additionalAuth->buildForm();

		if($form['requireCaptcha']) {
			// Captcha
			$captcha = $ff_context->getCaptcha();
			if(!empty($captcha->getParmaterName())) {
				$parameterData = $request->get($captcha->getParmaterName(), request::METHOD_POST);
				$captchaValidation = $captcha->validate($parameterData);
				if(!$captchaValidation->success) {
					$response->redirect($ff_router->getPath($redirectAlternative['cp_additionalauth'], [], [
						'query' => [
							'phrase' => 'misc-invalid-captcha'
						]
					]));
					return true;
				}
			}
		}

		// Getting input parameters for the additional authenticator.
		$parameters = [];
		foreach($form['input'] as $inputName => $inputParameters) {
			if(!$request->get($inputName, request::METHOD_POST)) {
				$response->redirect($ff_router->getPath($redirectAlternative['cp_additionalauth'], [], [
					'query' => [
						'phrase' => 'misc-parameters-missing'
					]
				]));
				return true;
			}
			$parameters[$inputName] = $request->get($inputName, request::METHOD_POST);
		}

		$res = $additionalAuth->handleForm($activeListUserObject, $parameters);
		if($res->success) {
			// Remove pending auth from session
			if(!$ff_context->getSession()->removePendingAuthOnActiveLink()) {
				throw new Exception('failed to remove pending authentication');
			}

			// Redirecting....
			$response->redirect($ff_router->getPath($redirectAlternative['cp_landing']));
			return true;
		}
		else {
			$response->redirect($ff_router->getPath($redirectAlternative['cp_additionalauth'], [], [
				'query' => [
					'phrase' => $res->messageKey
				]
			]));
			return true;
		}

		return true;
	}
}

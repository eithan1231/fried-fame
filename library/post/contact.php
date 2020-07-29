<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\contact.php
//
// ======================================


class post_contact extends post_abstract
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
		$name = $request->get('name', request::METHOD_POST);
		$subject = $request->get('subject', request::METHOD_POST);
		$body = $request->get('body', request::METHOD_POST);


		// Limit to 1 try, over the span of 50 minutes.
		$limit = 1;
		$limitTimespan = FF_MINUTE * 50;
		postlimiter::insertRequest(self::getName(), $request);
		if(postlimiter::exceedsRequestLimit(self::getName(), $request, $limit, $limitTimespan, $activeCount)) {
			// Redirect without warning. Fuck em.
			$response->redirect($ff_router->getPath('contact', [], [
				'query' => [
					'phrase' => 'misc-try-again-later'
				]
			]));
			return true;
		}

		// Checking parameters.
		if(
			empty($name) || empty($email) || empty($body) || empty($subject) ||
			!is_string($name) || !is_string($email) || !is_string($body) || !is_string($subject)
		) {
			$response->redirect($ff_router->getPath('contact', [], [
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
				$response->redirect($ff_router->getPath('contact', [], [
					'query' => [
						'phrase' => 'misc-invalid-captcha'
					]
				]));
				return true;
			}
		}

		$res = support_public::createEnquiry(
			$name,
			$email,
			$subject,
			$body
		);

		$response->redirect($ff_router->getPath('contact', [], [
			'query' => [
				'phrase' => $res->message
			]
		]));

		return true;
	}
}

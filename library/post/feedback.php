<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\feedback.php
//
// ======================================


class post_feedback extends post_abstract
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

		$return = $request->get('return');
		if(!$return) {
			$return = $ff_router->getPath('cp_landing', [], [
				'mode' => 'host'
			]);
		}
		else {
			$parsedReturn = parse_url($return);
			if(
				!isset($parsedReturn['host']) ||
				!in_array($parsedReturn['host'], $ff_config->get('trusted-hostnames'))
			) {
				$return = $ff_router->getPath('cp_landing', [], [
					'mode' => 'host'
				]);
			}
		}

		$session = $ff_context->getSession();
		if(!$session) {
			$response->redirect($return);
			return true;
		}

		$user = $session->getActiveLinkUser();
		if(!$user) {
			$response->redirect($return);
			return true;
		}

		// getting user input
		$body = $request->get('body', request::METHOD_POST);
		if(!$body) {
			$response->redirect($return);
			return true;
		}

		feedback::newFeedback($user, $body);

		$response->redirect($return);

		return true;
	}
}

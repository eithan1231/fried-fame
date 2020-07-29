<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\approvereview.php
//
// ======================================


class post_approvereview extends post_abstract
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
		global $ff_context, $ff_router, $ff_config;

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
		$user = $session->getActiveLinkUser();

		$id = intval($request->get('id', request::METHOD_POST));
		$res = review::approveReview($user, $id);
		ff_postRedirectView($return, $res->message);

		return true;
	}
}

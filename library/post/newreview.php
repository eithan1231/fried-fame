<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\newreview.php
//
// ======================================


class post_newreview extends post_abstract
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
		$stars = $request->get('stars', request::METHOD_POST);
		$body = $request->get('body', request::METHOD_POST);
		if(!is_numeric($stars) || !$body) {
			$response->redirect($ff_router->getPath('cp_review', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		$stars = intval($stars);

		$review = review::newReview($user, $stars, $body, $session->getLanguageCode());
		if($review->success) {
			ff_postRedirectView($ff_router->getPath('cp_landing'), 'misc-review-created');
		}
		else {
			$response->redirect($ff_router->getPath('cp_review', [], [
				'query' => [
					'phrase' => $review->message
				]
			]));
		}

		return true;
	}
}

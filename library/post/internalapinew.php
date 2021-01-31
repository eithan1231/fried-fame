<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\internalapinew.php
//
// ======================================


class post_internalapinew extends post_abstract
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

		if(!parent::validateAuthenticated()) {
			return true;
		}

		$permit = $request->get('permit', request::METHOD_POST);

		$activeSessionLink = $ff_context->getSession()->getActiveLinkUser();
		if(!$activeSessionLink) {
			// Not Authenticated.
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		if(!$permit) {
			$response->redirect($ff_router->getPath('cp_mod_internalapi_new'));
		}

		$result = internalapi::createInternalAPI($activeSessionLink, $permit);
		if($result->success) {
			$response->redirect($ff_router->getPath('cp_mod_internalapi_edit', [], [
				'query' => [
					'id' => $result->data['instance']->getId()
				]
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_internalapi_new', [], [
				'query' => [
					'phrase' => $result->message
				]
			]));
		}



		return true;
	}
}

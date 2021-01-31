<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\internalapiedit.php
//
// ======================================


class post_internalapiedit extends post_abstract
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

		$id = intval($request->get('id', request::METHOD_POST));
		if(!$id) {
			$response->redirect($ff_router->getPath('cp_mod_internalapi_list'));
			return true;
		}

		$internalAPI = internalapi::getInternalAPIById($id);
		if(!$internalAPI) {
			$response->redirect($ff_router->getPath('cp_mod_internalapi_list'));
			return true;
		}

		$activeUser = $ff_context->getSession()->getActiveLinkUser();
		$enable = ff_stringToBool($request->get('enable', request::METHOD_POST));

		if($enable) {
			$internalAPI->enable($activeUser);
		}
		else {
			$internalAPI->disable($activeUser);
		}

		$response->redirect($ff_router->getPath('cp_mod_internalapi_edit', [], [
			'query' => [
				'id' => $id
			]
		]));

		return true;
	}
}

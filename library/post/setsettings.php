<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\setsettings.php
//
// ======================================


class post_setsettings extends post_abstract
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

		$newSettings = $request->get('settings', request::METHOD_POST);

    if(!is_array($newSettings)) {
      // generic redirect.
      $response->redirect($ff_router->getPath('cp_landing'));
      return true;
    }

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			return false;
		}

    $userSettings = settings::getByUserId($user->getId());

		foreach($newSettings as $key => $value) {
			$userSettings->setOption($key, $value);
		}

		// Success is dictated by the http status. So no additioanl content needs to
		// be sent.

		return true;
	}
}

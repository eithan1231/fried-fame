<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\ffrpcnew.php
//
// ======================================


class post_ffrpcnew extends post_abstract
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
			$response->redirect($return);
			return true;
		}

		$type = $request->get('type');
		$endpoint = $request->get('endpoint');
		$port = $request->get('port');

		if(!$port || !is_numeric($port) || !$type || !$endpoint) {
			// parameters required, and they're not found, so let's do a page not
			// found error.
			return false;
		}

		$res = ffrpc::createRpcNode($user, $type, $endpoint, $port);
		if($res->success) {
			$response->redirect($ff_router->getPath('cp_mod_ffrpc_landing', [], [
				'hash' => "rpc_{$res->data['id']}"
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_ffrpc_new', [], [
				'query' => [
					'phrase' => $res->message
				]
			]));
		}

		return true;
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\setphrase.php
//
// ======================================


class post_setphrase extends post_abstract
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

		$phrase_name = $request->get('phrase_name', request::METHOD_POST);
		$phrase_language = $request->get('phrase_language', request::METHOD_POST);
		$phrase_revision = $request->get('phrase_revision', request::METHOD_POST);
		$phrase_body = $request->get('phrase_body', request::METHOD_POST);
		$response_type = $request->get('response_type');
		if(!$response_type) {
			$response_type = 'json';
		}

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			return false;
		}

		$send = function($p) use (&$response, $response_type) {
			global $ff_router;
			switch ($response_type) {
				case 'cp_mod_language_new': {
					$response->redirect($ff_router->getPath('cp_mod_language_new', [], [
						'query' => [
							'phrase' => $p->messageKey
						]
					]));
					break;
				}

				case 'cp_mod_language_edit': {
					$response->redirect($ff_router->getPath('cp_mod_language_list', [], [
						'query' => [
							'phrase' => $p->messageKey
						]
					]));
					break;
				}

				case 'json':
				default: {
					$response->setHttpHeader('Content-type', 'application/json');
					$response->appendBody(json_encode($p));
					break;
				}
			}

			return true;
		};

		if(
			!$phrase_name ||
			!$phrase_language ||
			!is_numeric($phrase_revision) ||
			!$phrase_body
		) {
			return $send(ff_return(false, [], 'misc-parameters-missing'));
		}

		$phrase_revision = intval($phrase_revision);
		return $send(language::setPhrase(
			$user,
			$phrase_name,
			$phrase_language,
			$phrase_revision,
			$phrase_body
		));

		return true;
	}
}

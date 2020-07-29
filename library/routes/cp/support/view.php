<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\cp\support\view.php
//
// ======================================


class routes_cp_support_view extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView($parameters = [])
	{
		ff_renderView(substr(__CLASS__, 7), $parameters);
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/control-panel/support/{string:id-subject}/',
			'/control-panel/support/{string:id-subject}',
      '/usercp/support/{string:id-subject}',
      '/usercp/support/{string:id-subject}/',
    ];
	}

	/**
	* The name of the route.
	*/
	public function getName()
	{
		return substr(__CLASS__, 7);
	}

	/**
	* Whether or not this is a special class.
	*/
	public function isSpecial()
	{
		return false;
	}

	/**
	* Gets the supported http methods.
	*/
	public function getMethods()
	{
		return ['GET', 'HEAD'];
	}

	/**
	* The code to execute the route.
	*
	* @param array $parameters
	*		The parameters of the url.
	* @param request $request
	*		The request object.
	* @param response $response
	*		The response object.
	*/
	public function run(array $parameters, request &$request, response &$response)
	{
		global $ff_context, $ff_router;
    if(!cp::standardProcedure($this->getName(), $request, $response)) {
      // Something went wrong with the standard procedure. This might mean it
      // needs additiona authentication, or something along those lines. Whatever
      // it is, means it has modified the $response object, and will redirect, or
      // print the appropriate page.
      return true;
    }

		// Getting ID from the string...
		$id = ff_getIdFromMergedIdAndSubject($parameters['id-subject']);
		if(!is_numeric($id)) {
			// RIP, never trust user input
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$session = $ff_context->getSession();
		if(!$session) {
			// No session..? weird.
			$response->redirect($ff_router->getPath('landing'));
			return true;
		}

		$user = $session->getActiveLinkUser();
		if(!$user) {
			// Probably not logged in
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		$group = $user->getGroup();
		if(!$group || !$group->can('support')) {
			// Cannot support
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$thread = support_thread::getThreadById($id, $user);
		if($thread->success) {
			if($thread->data->isDeleted()) {
				// deleted
				$response->redirect($ff_router->getPath('cp_landing'));
				return true;
			}

			$this->renderView([
				'thread' => $thread->data
			]);
		}
		else {
			$response->redirect($ff_router->getPath('cp_landing', [], [
				'query' => [
					'phrase' => ($thread->messageKey === 'default' ? '' : $thread->messageKey)
				]
			]));
		}

		return true;
	}
}

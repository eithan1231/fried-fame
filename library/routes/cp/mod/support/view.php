<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\cp\mod\support\view.php
//
// ======================================


class routes_cp_mod_support_view extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView($params = [])
	{
		ff_renderView(substr(__CLASS__, 7), $params);
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/usercp/moderator/support/{int:id}'
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

		$user = $ff_context->getSession()->getActiveLinkUser();

		if(!$user || !$user->getGroup()->can('mod_support')) {
			$response->setHttpStatus(401);
			return true;
		}

		if(!is_numeric($parameters['id'])) {
			return false;
		}

		$id = intval($parameters['id']);

		$thread = support_thread::getThreadById($id, $user);
		if($thread->success) {
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

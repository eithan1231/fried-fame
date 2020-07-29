<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\routes\contact.php
//
// ======================================


/**
* Contact US page
*/
class routes_contact extends route
{
	/**
	* Renders the view linked with this
	*/
	private function renderView()
	{
		ff_renderView(substr(__CLASS__, 7));
	}

	/**
	* Gets the paths we want to register with this route.
	*/
	public function getPaths()
	{
		return [
			'/contact-us',
			'/contact-us/',
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

		if($session = $ff_context->getSession()) {
			if($activeLink = $session->getActiveLinkUser()) {
				$response->redirect($ff_router->getPath('cp_support_new', [], [
					'query' => [
						'ref' => $this->getName()
					]
				]));
				return true;
			}
		}

		$supportPublicId = intval($request->get('id'));
		$supportVerification = $request->get('token');
		if($supportPublicId && $supportVerification) {
			$post = support_public::getById($supportPublicId);
			if($post && $post->verify($supportVerification)) {
				// Verified? We're sending a generic message so this can be ignored.
			}

			// generic message
			return $response->redirect($ff_router->getPath('contact', [], [
				'query' => [
					'phrase' => 'misc-contact-validated'
				]
			]));
		}

		// User is not logged in or session is invalid.
		$this->renderView();

		return true;
	}
}

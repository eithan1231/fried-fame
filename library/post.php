<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post.php
//
// ======================================


class post
{
	private $postActions = [];

	private $action = '';

	/**
	* Creates a new post object.
	*
	* @param string $action
	*		The action of the post instance.
	*/
	public function __construct(string $action)
	{
		// Registering action
		$this->action = strtolower($action);

		// Registering POST actions
		$this->postActions[] = new post_login();
		$this->postActions[] = new post_register();
		$this->postActions[] = new post_recovery();
		$this->postActions[] = new post_additionalauth();
		$this->postActions[] = new post_changeemail();
		$this->postActions[] = new post_newsupport();
		$this->postActions[] = new post_newsupportpost();
		$this->postActions[] = new post_changepassword();
		$this->postActions[] = new post_paymentstate();
		$this->postActions[] = new post_contact();
		$this->postActions[] = new post_sessionswitchuser();
		$this->postActions[] = new post_sessionsignout();
		$this->postActions[] = new post_sessionreauth();
		$this->postActions[] = new post_feedback();
		$this->postActions[] = new post_redeemgiftcode();
		$this->postActions[] = new post_newreview();
		$this->postActions[] = new post_resendemailverification();
		$this->postActions[] = new post_setsettings();

		// Moderator post actions
		$this->postActions[] = new post_setphrase();
		$this->postActions[] = new post_changeusergroup();
		$this->postActions[] = new post_closesupportthread();
		$this->postActions[] = new post_opensupportthread();
		$this->postActions[] = new post_deletesupportthread();
		$this->postActions[] = new post_undeletesupportthread();
		$this->postActions[] = new post_creategiftcodes();
		$this->postActions[] = new post_ffrpcnew();
		$this->postActions[] = new post_announcement();
		$this->postActions[] = new post_sendmail();
		$this->postActions[] = new post_uploadpackage();
		$this->postActions[] = new post_approvereview();
		$this->postActions[] = new post_undeletereview();
		$this->postActions[] = new post_deletereview();
		$this->postActions[] = new post_newnode();
		$this->postActions[] = new post_newmodsupportpost();
		$this->postActions[] = new post_internalapinew();
		$this->postActions[] = new post_internalapiedit();
	}

	/**
	* Runs the post handler linked with the action (param 1 of __construct)
	*
	* @param request $request
	*		The request object
	* @param response $response
	*		The response object
	*/
	public function run(request &$request, response &$response)
	{
		foreach($this->postActions as $postAction) {
			if(strtolower($postAction->getName()) == $this->action) {
				return $postAction->run($request, $response);
			}
		}

		return null;
	}
}

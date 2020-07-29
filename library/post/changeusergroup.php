<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\changeusergroup.php
//
// ======================================


class post_changeusergroup extends post_abstract
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

		$session = $ff_context->getSession();
		$user = $session->getActiveLinkUser();
		if(!$user) {
			// Not Authenticated.
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		$userGroup = $user->getGroup();
		if(!$userGroup || !$userGroup->can('mod_users')) {
			// Not authorized.
			$response->redirect($ff_router->getPath('cp_landing'));
			return true;
		}

		$post_userId = $request->get('user_id', request::METHOD_POST);
		$post_newGroup = $request->get('new_group', request::METHOD_POST);

		if(!$post_userId || !$post_newGroup) {
			// Invalid parameters
			$response->redirect($ff_router->getPath('cp_mod_user_landing'));
			return true;
		}

		$userSubject = user::getUserById($post_userId);
		if(!$userSubject) {
			// User provided an invalid user.
			$response->redirect($ff_router->getPath('cp_mod_user_landing'));
			return true;
		}

		$groupSubject = group::getGroupById($post_newGroup);
		if(!$groupSubject) {
			// User provided a bad usergroup. Cannot assign something that doesnt exist.
			$response->redirect($ff_router->getPath('cp_mod_user_manage', [], [
				'query' => [
					'user' => $post_userId
				]
			]));
			return true;
		}

		$result = $userSubject->updateUsergroup($user, $groupSubject);
		if($result->success) {
			$response->redirect($ff_router->getPath('cp_mod_user_manage', [], [
				'query' => [
					'user' => $post_userId,
					'phrase' => $result->message
				]
			]));
			return true;
		}

		return true;
	}
}

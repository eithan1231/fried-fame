<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\uploadpackage.php
//
// ======================================


class post_uploadpackage extends post_abstract
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
			$response->redirect($ff_router->getPath('login'));
			return true;
		}

		$packageFile = $request->getFile('package_file');
		$platform = $request->get('platform');
		$version = $request->get('version');

		if(!$packageFile || !$platform || !$version) {
			$response->redirect($ff_router->getPath('cp_mod_package_new', [], [
				'query' => [
					'phrase' => 'misc-parameters-missing'
				]
			]));
			return true;
		}

		$res = packages::uploadPackage(
			$user,
			$platform,
			$version,
			$packageFile->name,
			$packageFile->tempName
		);

		if($res->success) {
			$response->redirect($ff_router->getPath('cp_mod_package_landing', [], [
				'hash' => "package_{$res->data['id']}"
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_package_new', [], [
				'query' => [
					'platform' => $platform,
					'version' => $version,
				]
			]));
		}

		return true;
	}
}

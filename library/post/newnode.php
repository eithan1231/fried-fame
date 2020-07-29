<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\post\newnode.php
//
// ======================================


class post_newnode extends post_abstract
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
			$response->redirect($return);
			return true;
		}

		$country = $request->get('country');
		$city = $request->get('city');
		$ip = $request->get('ip');
		$hostname = $request->get('hostname');
		$maximum_load = $request->get('maximum_load');
		$ovpn_enable = $request->get('ovpn_enable');
		$ovpn_protocol = $request->get('ovpn_protocol');
		$ovpn_port = $request->get('ovpn_port');
		$ovpn_auth = $request->get('ovpn_auth');
		$ovpn_cipher = $request->get('ovpn_cipher');
		$ovpn_tls_cipher = $request->get('ovpn_tls_cipher');
		$ovpn_compression = $request->get('ovpn_compression');
		$ovpn_ca = $request->get('ovpn_ca');
		$ovpn_cert = $request->get('ovpn_cert');
		$ovpn_key = $request->get('ovpn_key');
		$ovpn_tls_auth = $request->get('ovpn_tls_auth');
		$ovpn_tls_crypt = $request->get('ovpn_tls_crypt');

		if(
			!$country ||
			!$city ||
			!$ip ||
			!$hostname ||
			!$maximum_load ||
			!$ovpn_enable ||
			!$ovpn_protocol ||
			!$ovpn_port ||
			!$ovpn_auth ||
			!$ovpn_cipher ||
			!$ovpn_tls_cipher ||
			!$ovpn_ca ||
			!$ovpn_cert ||
			!$ovpn_key
		) {
			$response->redirect($ff_router->getPath('cp_mod_nodes_new', [], [
				'query' => [
					'phrase' => 'misc-missing-param'
				]
			]));
			return true;
		}

		$ovpn_enable = ff_stringToBool($ovpn_enable);
		$maximum_load = intval($maximum_load);
		if(!$maximum_load) {
			$maximum_load = 256;
		}

		$res = node::newNode(
			$user,
			$country,
			$city,
			$ip,
			$hostname,
			$maximum_load,
			$ovpn_enable,
			$ovpn_protocol,
			$ovpn_port,
			$ovpn_auth,
			$ovpn_cipher,
			$ovpn_tls_cipher,
			$ovpn_compression,
			$ovpn_ca,
			$ovpn_cert,
			$ovpn_key,
			$ovpn_tls_auth,
			$ovpn_tls_crypt
		);

		if(!$res) {
			$response->redirect($ff_router->getPath('cp_mod_nodes_new', [], [
				'query' => [
					'phrase' => $res->message
				]
			]));
		}
		else {
			$response->redirect($ff_router->getPath('cp_mod_nodes_new', [], [
				'query' => [
					'phrase' => 'misc-success'
				]
			]));
		}

		return true;
	}
}

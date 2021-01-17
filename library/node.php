<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\node.php
//
// ======================================


/**
* Class for interfacing VPN Nodes
*/
class node
{
	private static $cache = [];

	private $id = null;
	private $enabled = null;
	private $country = null;
	private $city = null;
	private $ip = null;
	private $hostname = null;
	private $has_ovpn = null;
	private $has_pptp = null;
	private $maximum_load = null;
	private $ovpn_ca = null;
	private $ovpn_cert = null;
	private $ovpn_key = null;
	private $ovpn_tls_auth = null;
	private $ovpn_tls_crypt = null;
	private $ovpn_auth = null;
	private $ovpn_cipher = null;
	private $ovpn_tls_cipher = null;
	private $ovpn_compression = null;
	private $ovpn_protocol = null;
	private $ovpn_port = null;

	/**
	* Links node by an id
	*
	* @param int $id
	*		The id which we want to link with the class instance
	*/
	public function linkById(int $id)
	{
		global $ff_sql;

		$res = $ff_sql->fetch("
			SELECT *
			FROM `vpn_nodes`
			WHERE `id` = ". $ff_sql->quote($id) ."
		", [
			`id` => 'int',
			`enabled` => 'bool',
			`maximum_load` => 'int'
		]);

		if(!$res) {
			return false;
		}

		foreach($res as $key => $value) {
			$this->$key = $value;
		}

		return true;
	}

	public static function getNodes(bool $enabled = true)
	{
		// TODO: Some sort of cache.
		global $ff_sql;
		return $ff_sql->query_fetch_all("
			SELECT *
			FROM `vpn_nodes`
			WHERE
			`enabled` = ". $ff_sql->quote($enabled) ."
		", [
			`id` => 'int',
			`enabled` => 'bool',
			`maximum_load` => 'int'
		]);
	}

	public static function newNode(
		user $user, string $country, string $city, string $ip, string $hostname,
		int $maximumLoad, bool $enableOpenVPN, string $ovpnProtocol,
		string $ovpnPort, string $ovpnAuth, string $ovpnCipher, string $ovpnTlsCipher,
		string $ovpnCompression, string $ovpnCa, string $ovpnCert, string $ovpnKey,
		string $ovpnTlsAuth, string $ovpnTlsCrypt
	) {
		global $ff_sql;
		if (!$user->getGroup()->can('mod_nodes')) {
			return ff_return(false, 'misc-permission-denied');
		}

		$res = $ff_sql->query("
			INSERT INTO `vpn_nodes`
			(
				`id`,
				`enabled`,
				`country`,
				`city`,
				`ip`,
				`hostname`,
				`has_ovpn`,
				`has_pptp`,
				`maximum_load`,
				`ovpn_ca`,
				`ovpn_cert`,
				`ovpn_key`,
				`ovpn_tls_auth`,
				`ovpn_tls_crypt`,
				`ovpn_auth`,
				`ovpn_cipher`,
				`ovpn_tls_cipher`,
				`ovpn_compression`,
				`ovpn_protocol`,
				`ovpn_port`
			)

			VALUES (
				NULL,
				1,
				". $ff_sql->quote($country) .",
				". $ff_sql->quote($city) .",
				". $ff_sql->quote($ip) .",
				". $ff_sql->quote($hostname) .",
				". $ff_sql->quote($enableOpenVPN) .",
				0,
				". $ff_sql->quote($maximumLoad) .",
				". $ff_sql->quote($ovpnCa) .",
				". $ff_sql->quote($ovpnCert) .",
				". $ff_sql->quote($ovpnKey) .",
				". $ff_sql->quote($ovpnTlsAuth) .",
				". $ff_sql->quote($ovpnTlsCrypt) .",
				". $ff_sql->quote($ovpnAuth) .",
				". $ff_sql->quote($ovpnCipher) .",
				". $ff_sql->quote($ovpnTlsCipher) .",
				". $ff_sql->quote($ovpnCompression) .",
				". $ff_sql->quote($ovpnProtocol) .",
				". $ff_sql->quote($ovpnPort) ."
			)
		");

		return ff_return(true);
	}

	/**
	* Gets a node class instance and linkiung it with an id
	*
	* @param int $id
	*/
	public static function getNodeById(int $id)
	{
		if(isset(self::$cache[__FUNCTION__][$id])) {
			return self::$cache[__FUNCTION__][$id];
		}

		$node = new node();
		if(!$node->linkById($id)) {
			return false;
		}

		return self::$cache[__FUNCTION__][$id] = $node;
	}

	/**
	* Gets the servers id
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Gets the servers state of being enabled
	*/
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	* refer to getEnabled
	*/
	public function isEnabled()
	{
		return $this->getEnabled();
	}

	/**
	* Gets the servers country
	*/
	public function getCountry()
	{
		return $this->country;
	}

	/**
	* Gets the servers city
	*/
	public function getCity()
	{
		return $this->city;
	}

	/**
	* Gets the servers ip
	*/
	public function getIp()
	{
		return $this->ip;
	}

	/**
	* Gets the servers hostname
	*/
	public function getHostname()
	{
		return $this->hostname;
	}

	/**
	* Gets the servers maximum load
	*/
	public function getMaximumLoad()
	{
		return $this->maximum_load;
	}

	public function buildOpenVPNConfig()
	{
		if(!$this->has_ovpn) {
			return false;
		}

		$ret = "client\r\n";
		$append = function($s) use (&$ret) {
			if(strlen($s) == 0) {
				return;
			}
			$ret .= $s . "\r\n";
		};

		$append('proto '. $this->ovpn_protocol);
		$append("remote {$this->ip} {$this->ovpn_port}");
		$append('dev tun');
		$append('resolv-retry infinite');
		$append('nobind');
		$append('persist-key');
		$append('persist-tun');
		$append('sndbuf 0');
		$append('rcvbuf 0');
		$append('remote-cert-tls server');
		$append('auth '. $this->ovpn_auth);
		$append('auth-user-pass');
		$append('cipher '. $this->ovpn_cipher);
		$append(strlen($this->ovpn_compression) > 0 ? $this->ovpn_compression : '');// ew
		$append('setenv opt block-outside-dns');
		$append('key-direction 1');
		$append('verb 3');

		if($this->ovpn_tls_cipher) {
			$append('remote-cert-tls server');
			$append('tls-client');
			$append('tls-version-min 1.2');
			$append('tls-cipher '. $this->ovpn_tls_cipher);
		}

		// Certificate Authority
		$append('<ca>');
		$append($this->ovpn_ca);
		$append('</ca>');

		// Certificate
		$append('<cert>');
		$append($this->ovpn_cert);
		$append('</cert>');

		// Key
		$append('<key>');
		$append($this->ovpn_key);
		$append('</key>');

		// TLS-Auth
		if(strlen($this->ovpn_tls_auth) > 0) {
			$append('<tls-auth>');
			$append($this->ovpn_tls_auth);
			$append('</tls-auth>');
		}

		if(strlen($this->ovpn_tls_crypt) > 0) {
			$append('<tls-crypt>');
			$append($this->ovpn_tls_crypt);
			$append('</tls-crypt>');
		}

		return $ret;
	}

	/**
	* Gets the current load on the server.
	*/
	public function getCurrentLoad()
	{
		if($rpc = ffrpc::getRpcByType(ffrpc::TYPE_BACKEND)) {
			$res = $rpc->do('get-server-conneciton-count', [
				'node' => $this->getId()
			]);

			if(!$res) {
				return false;
			}

			return intval($res);
		}
		else {
			return false;
		}
	}
}

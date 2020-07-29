<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\cryptography.php
//
// ======================================


class cryptography
{
	/**
	* Generates a random string.
	*
	* @param int $length
	*		Length of the random string.
	*/
	public static function randomString($length = 32, bool $alphaNumOnly = false)
	{
		$ret = '';
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'. ($alphaNumOnly ? '' : '_-');
		for($i = 0; $i < $length; $i++) {
			$ret .= $chars[self::randomNumber(0, strlen($chars) - 1)];
		}
		return $ret;
	}

	/**
	* Generates a random number. This is basically a wrapper for mt_rand
	*
	* @param int $min
	*		Minimum number possible
	* @param int $maximum
	*		Maximum number possible
	*/
	public static function randomNumber(int $min = 0, int $maximum = 1000000)
	{
		return mt_rand($min, $maximum);
	}

	/**
	* Hashes a password
	*
	* @param string $password
	*		Password we want to hash
	*/
	public static function hashPassword(string $password)
	{
		global $ff_config;
		return password_hash(
			self::applyPepper($password),
			PASSWORD_BCRYPT, [
				'cost' => intval($ff_config->get('password-hash-cost'))
			]
		);
	}

	/**
	* Verifies password against a hash
	*
	* @param string $password
	*		The password we want to apply against the hash
	* @param string $hash
	*		The hash we want to compare against.
	* @return bool true if it matches, false if it doesn't match.
	*/
	public static function verifyHash(string $password, string $hash)
	{
		global $ff_config;
		return password_verify(
			self::applyPepper($password),
			$hash
		);
	}

	/**
	* Applies the configuration pepper to the password.
	*
	* @param string $password
	*		The password you want to apply pepper to.
	* @return string The password combined with pepper
	*/
	public static function applyPepper(string $password)
	{
		global $ff_config;
		return base64_encode(hash_hmac(
			'sha256',
			$password,
			$ff_config->get('pepper'),
			true
		));
	}
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\packages.php
//
// ======================================


class packages
{
	/**
	* Platform for windows.
	* NOTE: When user uploads update for windows, he could spell it wrong. This is
	* just for internal purposes, and we got no control over the users input.
	*/
	const PLATFORM_WINDOWS = 'Windows';

	/**
	* Uploads a package
	*
	* @param user $user
	*		The user who's uploading the file
	* @param string $platform
	*		Platform that the package is intended for.
	* @param string $version
	*		Version of the package
	* @param string $filename
	*		Filename
	* @param string $tempLocation
	*		Temporary upload location
	*/
	public static function uploadPackage(user $user, string $platform, string $version, string $filename, string $tempLocation)
	{
		global $ff_sql;
		if(!$user->getGroup()->can('mod_packages')) {
			return ff_return(false, 'misc-permission-denied');
		}

		$uploadedPath = uploads::upload(__CLASS__, $filename, $tempLocation, FF_TIME .":{$platform}:{$version}");
		$filesize = filesize($uploadedPath);

		$ff_sql->query("
			INSERT INTO `packages`
			(`id`, `date`, `creator`, `platform`, `version`, `filesize`, `filename`)
			VALUES (
				NULL,
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($user->getId()) .",
				". $ff_sql->quote($platform) .",
				". $ff_sql->quote($version) .",
				". $ff_sql->quote($filesize) .",
				". $ff_sql->quote($filename) ."
			)
		");

		$id = $ff_sql->getLastInsertId();

		audits_admin_uploadpackage::insert($user, $id);

		return ff_return(true, [
			'path' => $uploadedPath,
			'size' => $filesize,
			'id' => $id,
		]);
	}

	/**
	* Gets all the package platforms and some associated data.
	*/
	public static function getPackagePlatforms()
	{
		global $ff_sql;
		return $ff_sql->query_fetch_all("
			SELECT
				sum(`master`.`filesize`) as `space`,
				count(1) as `uploads`,
				`master`.`platform`,
				(
					SELECT `version`
					FROM `packages` AS `sub`
					WHERE `sub`.`platform` = `master`.`platform`
					ORDER BY `sub`.`id` DESC
					LIMIT 1
				) AS `recent_version`
			FROM `packages` as `master`
			GROUP BY `master`.`platform`
		", [
			'space' => 'int',
			'uploads' => 'int',
		]);
	}

	/**
	* Gets all packages starting at an index, limiting the amount to a limit.
	* @param int $index
	* @param int $limit
	*/
	public static function getPackages(int $index, int $limit)
	{
		global $ff_sql;
		return $ff_sql->query_fetch_all("
			SELECT *
			FROM `packages`
			WHERE `id` > ". $ff_sql->quote($index) ."
			LIMIT ". $ff_sql->quote($limit) ."
		", [
			'id' => 'int',
			'date' => 'int',
			'creator' => 'int',
			'filesize' => 'int'
		]);
	}

	/**
	* Gets information associated with an id
	* @param int $id
	*/
	public static function getPackageInformation(int $id)
	{
		global $ff_sql;
		return $ff_sql->fetch("
			SELECT *
			FROM `packages`
			WHERE `id` = ". $ff_sql->quote($id) ."
		", [
			'id' => 'int',
			'date' => 'int',
			'creator' => 'int',
			'filesize' => 'int'
		]);
	}

	/**
	* Gets most recent information associated with a platform
	* @param string $platform
	*/
	public static function getPlatformMostRecent(string $platform)
	{
		global $ff_sql;
		return $ff_sql->fetch("
			SELECT *
			FROM `packages`
			WHERE `platform` = ". $ff_sql->quote($platform) ."
			ORDER BY `id` DESC
			LIMIT 1
		", [
			'id' => 'int',
			'date' => 'int',
			'creator' => 'int',
			'filesize' => 'int'
		]);
	}

	/**
	* Gets all the packages associated with a platform
	* @param string $platform
	*/
	public static function getPlatformPackages(string $platform)
	{
		global $ff_sql;
		return $ff_sql->fetch_all("
			SELECT *
			FROM `packages`
			WHERE `platform` = ". $ff_sql->quote($platform) ."
			ORDER BY `id` DESC
		", [
			'id' => 'int',
			'date' => 'int',
			'creator' => 'int',
			'filesize' => 'int'
		]);
	}
}

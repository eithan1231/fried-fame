<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\uploads.php
//
// ======================================


/**
* This class handles the uploads in the ../library/uploads/ directory.
*/
class uploads
{
	/**
	* computes the subpath for an update. ie: "de/ad/"
	* @param string $filename
	*		Original filename
	* @param string $additionalParamater
	*		Should be used if you want to allow files with the same filename, will
	*		prevent conflicts.
	*/
	private static function computeSubDir(string $seed)
	{
		$ret = '';

		$ret .= substr($seed, 0, 2) .'/';
		$ret .= substr($seed, 2, 4) .'/';
		$ret .= substr($seed, 4, 6);

		return $ret;
	}

	/**
	* uploads a file.
	* @param string $category
	*		Category of file (ie: packages)
	* @param string $filename
	*		Filename
	* @param string $temporaryFile
	*		Temporary file (where we move it from)
	* @param string $additionalParamater
	*		Should be used if you want to allow files with the same filename. Will
	*		prevent conflicts.
	*/
	public static function upload(string $category, string $filename, string $temporaryFile, string $additionalParamater = '')
	{
		$categoryPath = FF_UPLOAD_DIR ."/{$category}";
		if(file_exists($categoryPath) && is_dir($categoryPath)) {
			$internalFilename = hash('md5', "{$filename}::{$additionalParamater}");
			$subDir = self::computeSubDir($internalFilename);
			$realDir = "{$categoryPath}/{$subDir}";
			$realPath = "{$realDir}/{$internalFilename}.bin";

			if(!mkdir($realDir, 0770, true)) {
				throw new Exception('Unable to make a recursive directory');
			}

			if(!rename($temporaryFile, $realPath)) {
				throw new Exception('Unable to upload temporary file');
			}

			return $realPath;
		}
		else {
			throw new Exception('Category doesn\'t exist');
		}
	}

	/**
	* Calcualtes a file path based on the parameters
	* NOTE: There is no assurance that the file exists!
	*
	* @param string $category
	*		Category of path (ie: packages)
	* @param string $filename
	*		Name of file
	* @param string $additionalParamater
	*		If you allow duplicate filenames, something must be placed here to ensure
	*		there are no conflicts.
	*/
	public static function getAbsoluteInternalPath(string $category, string $filename, string $additionalParamater = '')
	{
		$categoryPath = FF_UPLOAD_DIR ."/{$category}";
		if(file_exists($categoryPath) && is_dir($categoryPath)) {
			$internalFilename = hash('md5', "{$filename}::{$additionalParamater}");
			$subDir = self::computeSubDir($internalFilename);
			$realDir = "{$categoryPath}/{$subDir}";
			$realPath = "{$realDir}/{$internalFilename}.bin";

			return $realPath;
		}
		else {
			throw new Exception('Category doesn\'t exist');
		}
	}
}

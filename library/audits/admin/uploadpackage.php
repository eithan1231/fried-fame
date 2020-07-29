<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\uploadpackage.php
//
// ======================================


class audits_admin_uploadpackage extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'uploadpackage';

  public function getName()
  {
    return self::AUDIT_NAME;
  }

  /**
  * Builds HTML snippet for viewing audit.
  */
  public function renderSnippet(array $value)
  {
    global $ff_context;

		$info = packages::getPackageInformation($value['id']);
		if($info) {
			echo $ff_context->getLanguage()->getPhrase('audit-admin-uploadpackage', [
				'platform' => $info['platform'],
				'id' => $info['id'],
				'version' => $info['version'],
				'filesize' => ff_getSizeAsVisual($info['filesize']),
				'filename' => $info['filename'],
			]);
		}
		else {
			echo '<span style="color:red"><strong>Unable to find entry in database. Corruption, or manual deletion detected.</strong></span>';
		}
  }

  public static function insert(user $user, int $id)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'id' => $id
    ]);
  }
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\announcement.php
//
// ======================================


class audits_admin_announcement extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'announcement';

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

		$announcement = announcement::getAnnouncementById($value['announcement_id']);
		if($announcement) {
			echo $ff_context->getLanguage()->getPhrase('audit-admin-announcement', [
				'subject' => $announcement->getSubject(),
      ]);
		}
  }

  public static function insert(user $user, int $announcementId)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'announcement_id' => $announcementId
    ]);
  }
}

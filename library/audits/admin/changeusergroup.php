<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\changeusergroup.php
//
// ======================================


class audits_admin_changeusergroup extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'changeusergroup';

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

		$user = user::getUserById($value['user_id']);
		$newGroup = group::getGroupById($value['new_group_id']);
		$oldGroup = group::getGroupById($value['old_group_id']);
    if(!$user || !$newGroup || !$oldGroup) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-changeusergroup-invalid');
    }
    else {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-changeusergroup', [
				'username' => $user->getUsername(),
				'new_group' => $newGroup->getName(),
				'old_group' => $oldGroup->getName(),
      ]);
    }
  }

  public static function insert(user $user, user $subject, group $oldGroup, group $newGroup)
  {
    parent::__insert($user, self::AUDIT_NAME, [
			'user_id' => $subject->getId(),
			'old_group_id' => $oldGroup->getId(),
			'new_group_id' => $newGroup->getId(),
    ]);
  }
}

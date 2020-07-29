<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\sendemail.php
//
// ======================================


class audits_admin_sendemail extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'sendemail';

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

		$recipient = $value['recipient'];
		$subject = $value['subject'];
		$recipientUser = user::getUserById($recipient);
    if($recipientUser) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-sendmail', [
        'username' => $recipientUser->getUsername(),
				'subject' => $subject
      ]);
    }
  }

  public static function insert(user $user, user $recipient, string $subject)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'recipient' => $recipient->getId(),
			'subject' => $subject
    ]);
  }
}

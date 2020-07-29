<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\undeletesupportticket.php
//
// ======================================


class audits_admin_undeletesupportticket extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'undeletesupportticket';

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

    $thread_id = $value['thread_id'];
    $thread = support_thread::getThreadById($thread_id);
    if($thread->success) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-thread-undeleted', [
				'ticket_id' => $thread_id,
				'thread_subject' => $thread->data->getSubject(),
      ]);
    }
  }

  public static function insert(user $user, support_thread $supportThread)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'thread_id' => $supportThread->getId()
    ]);
  }
}

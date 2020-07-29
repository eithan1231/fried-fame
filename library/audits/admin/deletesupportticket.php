<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\deletesupportticket.php
//
// ======================================


class audits_admin_deletesupportticket extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'deletesupportticket';

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
      echo $ff_context->getLanguage()->getPhrase('audit-admin-thread-deleted', [
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

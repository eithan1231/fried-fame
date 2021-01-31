<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\internalapinew.php
//
// ======================================


class audits_admin_internalapinew extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'internalapinew';

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

    echo $ff_context->getLanguage()->getPhrase('audit-admin-internalapinew', [
      'permit' => $value['permit'],
      'id' => $value['id'],
    ]);
  }

  public static function insert(user $user, internalapi $internalapi)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'permit' => $internalapi->getPermit(),
      'id' => $internalapi->getId(),
      'token' => $internalapi->getToken()
    ]);
  }
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\newplan.php
//
// ======================================


class audits_admin_newplan extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'newplan';

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

    $plan = plan::getPlanById($value['plan_id']);
    if(!$plan) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-newplan-invalid', [
        'id' => $value['plan_id']
      ]);
    }
    else {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-newplan', [
        'name' => $plan->getName()
      ]);
    }
  }

  public static function insert(user $user, int $planId)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'plan_id' => $planId
    ]);
  }
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\abstract.php
//
// ======================================


class audits_admin_abstract
{
  public static function __insert(user $user, string $key, array $value)
  {
    global $ff_sql;

    return $ff_sql->query("
      INSERT INTO `admin_audit_logs`
      (`id`, `user_id`, `date`, `name`, `value`)
      VALUES (
        NULL,
        ". $ff_sql->quote($user->getId()) .",
        ". $ff_sql->quote(FF_TIME) .",
        ". $ff_sql->quote($key) .",
        ". $ff_sql->quote(json_encode($value)) ."
      )
    ") !== false;
  }
}

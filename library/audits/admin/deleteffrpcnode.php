<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\deleteffrpcnode.php
//
// ======================================


class audits_admin_deleteffrpcnode extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'deleteffrpcnode';

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

		echo $ff_context->getLanguage()->getPhrase('audit-admin-deleteffrpcnode', [
			'node_id' => $value['node_id'],
			'type' => $value['type'],
			'endpoint' => $value['endpoint'],
			'port' => $value['port'],
		]);
  }

  public static function insert(user $user, int $nodeId, string $type, string $endpoint, int $port)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'node_id' => $nodeId,
			'type' => $type,
			'endpoint' => $endpoint,
			'port' => $port
    ]);
  }
}

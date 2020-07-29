<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\publicsupportstatus.php
//
// ======================================


class audits_admin_publicsupportstatus extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'publicsupportstatus';

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

    $previousStatus = $value['previous_status'];
    $newStatus = $value['new_status'];
    $supoprtPublicId = $value['support_public_id'];
    $supportPublic = support_public::getById($supoprtPublicId);
		if(!$supportPublic) {
			echo '<strong>ERROR! Manual deletion detected</strong>';
		}
		else {
			echo $ff_context->getLanguage()->getPhrase('audit-admin-publicsupportstatus', [
        'title' => $supportPublic->getSubject(),
        'status' => $newStatus,
        'id' => $supoprtPublicId
			]);
		}
  }

  public static function insert(user $user, support_public $post, int $previousStatus, int $newStatus)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'support_public_id' => $post->getId(),
      'previous_status' => $previousStatus,
			'new_status' => $newStatus
    ]);
  }
}

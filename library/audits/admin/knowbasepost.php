<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\knowbasepost.php
//
// ======================================


class audits_admin_knowbasepost extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'knowbasepost';

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

    $postId = intval($value['id']);
    $post = knowbase_post::getPostById($postId);
    if($post) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-knowbasepost', [
        'name' => $post->getTitle()
      ]);
    }
    else {
      echo "<strong>{$postId} not found</strong>";
    }
  }

  public static function insert(user $user, int $postId)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'id' => $postId
    ]);
  }
}

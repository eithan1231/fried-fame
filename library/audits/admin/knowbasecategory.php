<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\knowbasecategory.php
//
// ======================================


class audits_admin_knowbasecategory extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'knowbasecategory';

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

    $id = intval($value['id']);
    $post = knowbase_category::getCategoryById($id);
    if($post) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-knowbasecategory', [
        'name' => $post->getTitle()
      ]);
    }
    else {
      echo "<strong>{$id} not found</strong>";
    }
  }

  public static function insert(user $user, int $postId)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'id' => $postId
    ]);
  }
}

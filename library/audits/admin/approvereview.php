<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\approvereview.php
//
// ======================================


class audits_admin_approvereview extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'approvereview';

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

    $review_id = $value['review_id'];
    $review = review::getReviewInfoById($review_id);
    if(!$review) {
      echo $ff_context->getLanguage()->getPhrase('audit-admin-review-approved-invalid', [
        'id' => $review_id
      ]);
    }
    else {
      $reviewCreator = user::getUserById($review['user_id']);
      echo $ff_context->getLanguage()->getPhrase('audit-admin-review-approved', [
        'writer' => $reviewCreator->getUsername()
      ]);
    }
  }

  public static function insert(user $user, int $reviewId)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'review_id' => $reviewId
    ]);
  }
}

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\undeletereview.php
//
// ======================================


class audits_admin_undeletereview extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'undeletereview';

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
    if($review) {
      $reviewCreator = user::getUserById($review['user_id']);
      echo $ff_context->getLanguage()->getPhrase('audit-admin-review-undeleted', [
        'writer' => $reviewCreator->getUsername()
      ]);
    }
		else {
			echo "<span style=\"color: red\">Error (log must have been menually altered by system administrator)</span>";
		}
  }

  public static function insert(user $user, int $reviewId)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'review_id' => $reviewId
    ]);
  }
}

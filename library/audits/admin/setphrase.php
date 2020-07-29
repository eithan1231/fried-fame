<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\admin\setphrase.php
//
// ======================================


class audits_admin_setphrase extends audits_admin_abstract implements audits_interface
{
  const AUDIT_NAME = 'setphrase';

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

    $phraseId = $value['phrase_id'];
		$revision = $value['revision'];
		$phraseInfo = language::getPhraseInformation($phraseId);
		if(!$phraseInfo) {
			echo $ff_context->getLanguage()->getPhrase('audit-admin-setphrase-invalid', [
        'id' => $phraseId,
      ]);
		}
		else {
			echo $ff_context->getLanguage()->getPhrase('audit-admin-setphrase', [
				'phrase_name' => $phraseInfo['phrase_name'],
				'revision' => $revision,
			]);
		}
  }

  public static function insert(user $user, int $phraseId, int $rev)
  {
    parent::__insert($user, self::AUDIT_NAME, [
      'phrase_id' => $phraseId,
			'revision' => $rev
    ]);
  }
}

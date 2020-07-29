<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\email\supportthreadundeleted.php
//
// ======================================


class email_supportthreadundeleted extends email_builder
{
  private $parameters = [];
  private $email_x = '';


  public function __construct(support_thread $thread)
  {
    global $ff_context, $ff_config, $ff_router;
    $language = $ff_context->getLanguage();
    $this->email_x = substr(__CLASS__, 6);

    $this->parameters = [
      'project' => $ff_config->get('project-name'),
      'username' => $thread->getUser()->getUsername(),
			'thread_subject' => $thread->getSubject(),
			'ticket_id' => $thread->getId(),
			'new_ticket_url' => $ff_router->getPath('cp_support_new', [], ['mode' => 'host']),
			'ticket_url' => $ff_router->getPath('cp_support_view', ['id-subject' => $thread->getId()], ['mode' => 'host']),
    ];

    // Subject
    parent::setSubject(
      $language->getPhrase('email-'. $this->email_x .'-subject', $this->parameters)
    );

    // Setting text body
    parent::setBody(
      $language->getPhrase('email-'. $this->email_x .'-message-text', $this->parameters)
    );

    // Setting html body
    parent::setHtml(
      $language->getPhrase('email-'. $this->email_x .'-message-html', $this->parameters)
    );
  }
}

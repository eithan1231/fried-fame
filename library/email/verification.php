<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\email\verification.php
//
// ======================================


class email_verification extends email_builder
{
  private $parameters = [];
  private $email_x = '';


  public function __construct(string $username, string $verify_url)
  {
    global $ff_context, $ff_config;
    $language = $ff_context->getLanguage();
    $this->email_x = substr(__CLASS__, 6);

    $this->parameters = [
      'project' => $ff_config->get('project-name'),
      'username' => $username,
      'verify_url' => $verify_url
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

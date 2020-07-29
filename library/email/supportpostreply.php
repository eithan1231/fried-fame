<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\email\supportpostreply.php
//
// ======================================


class email_supportpostreply extends email_builder
{
  private $parameters = [];
  private $email_x = '';

  /**
  * @param string $username
  *   Who this is intended for.
  * @param string $ticketId
  *   Thread identifier.
  * @param string $accessUrl
  *   URL user can visit to view thread..
  * @param string $subject
  *   Subject.
  * @param string $replier
  *   Who is responding to support thread.
  * @param string replier_group
  *   The name of the group $replier belongs to.
  * @param string message_quote
  *   Preview of message (HTML)
  */
  public function __construct(
    string $username,
    string $ticketId,
    string $accessUrl,
    string $subject,
    string $replier,
    string $replierGroup,
    string $replierGroupColor,
    string $messageQuote
  ) {
    global $ff_context, $ff_config;
    $language = $ff_context->getLanguage();
    $this->email_x = substr(__CLASS__, 6);

    $this->parameters = [
      'project' => $ff_config->get('project-name'),
      'username' => $username,
      'subject' => $subject,
      'ticket_id' => $ticketId,
      'thread_url' => $accessUrl,
      'replier' => $replier,
      'replier_group' => $replierGroup,
      'replier_group_color' => $replierGroupColor,
      'message_quote' => $messageQuote,
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

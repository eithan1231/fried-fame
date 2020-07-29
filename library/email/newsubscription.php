<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\email\newsubscription.php
//
// ======================================


class email_newsubscription extends email_builder
{
  private $parameters = [];
  private $email_x = '';


  public function __construct(user $user, plan $plan)
  {
    global $ff_context, $ff_config;
    $language = $ff_context->getLanguage();
    $this->email_x = substr(__CLASS__, 6);

    $this->parameters = [
      'project' => $ff_config->get('project-name'),
      'username' => $user->getUsername(),
			'expiry' => $user->date($user->dateFormat(), FF_TIME + $plan->getDuration()),
			'time' => $user->date($user->dateFormat(), FF_TIME),
			'plan_currency' => $plan->getCurrency(),
			'plan_price' => $plan->getPrice(),
			'plan_name' => $plan->getName(),
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

<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\additionalauth\hotp.php
//
// ======================================


class additionalauth_hotp implements additionalauth_interface
{
  /**
  * Checks whether the implementation of this interface is enabled.
  */
  public function enabled()
  {
    return false;
  }

  /**
  * Gets the name of the implementee object. This is used  for internal use.
  * @return string
  */
  public function getName()
  {
    return 'hotp';
  }

  /**
  * This is triggered when a user tries logs in. If for example, he has email
  * verification enabled, this would create and queue a task to send an email.
  *
  * @param user $user
  *   The person who is logging in.
  */
  public function onLogonEvent(user $user)
  {
    // Not needed
  }

  /**
  * Whether or not this additionalauth interface requires a form (submission form)
  * @return bool
  */
  public function requiresForm()
  {
    return true;
  }

  /**
  * Builds the form for the user.
  * @return array
  */
  public function buildForm()
  {
    return [
      'input' => [
        'code' => [
          'type' => 'text',
          'label' => 'Code',
        ]
      ],
      'description' => 'Two Factor Autehtnication: HMAC one time password.',
      'title' => 'HMAC One Time Password',
      'requireCaptcha' => false,
    ];
  }

  /**
  * Handles the response to the buildForm
  *
  * @param user $user
  *   The authanticated user handling the form
  * @param array $parameters
  *   Parameters for the user.
  */
  public function handleForm(user $user, array $parameters = [])
  {
    return ff_return(false);
  }

  /**
  * Will enable the parents additionalauth method with a user.
  *
  * NOTE: This will not disable previous authentications!
  *
  * @param user $user
  *   The user enabling the auth
  */
  public function enable(user $user)
  {

  }
}

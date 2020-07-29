<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\additionalauth\email.php
//
// ======================================


class additionalauth_email implements additionalauth_interface
{
  // The duration the code stays active.
  const CODE_DURATION = 60*60*24*29;//month

  /**
  * Checks whether the implementation of this interface is enabled.
  */
  public function enabled()
  {
    return true;
  }

  /**
  * Gets the name of the implementee object. This is used  for internal use.
  * @return string
  */
  public function getName()
  {
    return 'email';
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
    global $ff_context;

    // Getting auth-context, and updating the recentmost code.
    $context = additionalauth::getUserAuthContext($user);
    $context['token'] = [
      'expiry' => FF_TIME + self::CODE_DURATION,
      'code' => strval(cryptography::randomNumber(1000000, 9999999))
    ];
    additionalauth::setUserAuthContext($user, $context);

    $emailSender = new email_twofactorverification(
      $user->getUsername(),
      $context['token']['code']
    );
    $emailSender->setRecipient($user->getEmail());
    $emailSender->send();

    return true;
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
          'label' => 'misc-additionalauth-email-input-code-label',
        ]
      ],
      'description' => 'misc-additionalauth-email-description',
      'title' => 'misc-additionalauth-email-title',
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
    global $ff_context;

    $context = additionalauth::getUserAuthContext($user);
    if(!$context) {
      return ff_return(false);
    }

    $validationCode = $parameters['code'];

    if(isset($context['token'])) {
      $token = $context['token'];
      if($token['expiry'] < FF_TIME) {
        // Expired.
        return ff_return(false, [], 'misc-additionalauth-email-expired-code');
      }

      if($token['code'] != $validationCode) {
        // Invalid token.
        return ff_return(false, [], 'misc-additionalauth-email-invalid-code');
      }

      return ff_return(true);
    }
    else {
      throw new Exception('No code/token found for additional authentication');
    }

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
    if(!$user->hasValidEmail()) {
      // Invalid email address.
      return ff_return(false);
    }
  }
}

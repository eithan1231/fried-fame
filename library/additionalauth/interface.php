<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\additionalauth\interface.php
//
// ======================================


interface additionalauth_interface
{
  /**
  * Checks whether the implementation of this interface is enabled.
  */
  public function enabled();

  /**
  * Gets the name of the implementee object. This is used  for internal use.
  * @return string
  */
  public function getName();

  /**
  * This is triggered when a user tries logs in. If for example, he has email
  * verification enabled, this would create and queue a task to send an email.
  *
  * @param user $user
  *   The person who is logging in.
  */
  public function onLogonEvent(user $user);

  /**
  * Whether or not this additionalauth interface requires a form (submission form)
  * @return bool
  */
  public function requiresForm();

  /**
  * Builds the form for the user.
  * @return array
  */
  public function buildForm();

  /**
  * Handles the response to the buildForm
  *
  * @param user $user
  *   The authanticated user handling the form
  * @param array $parameters
  *   Parameters for the user.
  */
  public function handleForm(user $user, array $parameters = []);

  /**
  * Will enable the parents additionalauth method with a user.
  *
  * NOTE: This will not disable previous authentications!
  *
  * @param user $user
  *   The user enabling the auth
  */
  public function enable(user $user);
}

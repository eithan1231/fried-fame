<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\audits\interface.php
//
// ======================================


interface audits_interface
{
  /**
  * Gets name of audit implementor
  * @return string
  */
  public function getName();

  /**
  * Builds HTML snippet for viewing audit.
  */
  public function renderSnippet(array $context);
}

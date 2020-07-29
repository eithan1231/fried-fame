<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\task.php
//
// ======================================


class task
{
  const TASK_EMAIL = 'email';

  /**
  * Runs a task.
  *
  * @param string $task
  *   Name of the task we want to run
  * @param mixed @parameters
  *   Parameters for the task. This can be anything serializable by json.
  */
  public static function run(string $task, $parameters)
  {
    $rpc = ffrpc::getRpc(ffrpc::TYPE_TASK);
    return $rpc->do($task, $parameters);
  }
}

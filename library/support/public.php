<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\support\public.php
//
// ======================================


/**
* Handles all public support posts and requests (etc.)
*/
class support_public
{
  /**
  * Statuses for the database.
  */
  const STATUS_PENDING_VERIFICATION = 0;
  const STATUS_PENDING_ACTION = 1;
  const STATUS_COMPLETE = 2;

  /**
  * Used for validation.
  */
  const STATUS_POOL = [
    self::STATUS_PENDING_VERIFICATION,
    self::STATUS_PENDING_ACTION,
    self::STATUS_COMPLETE
  ];

  /**
  * all columns returned by the database.
  */
  private $id = 0;
  private $status = 0;
  private $date = 0;
  private $name = "";
  private $email = "";
  private $email_verif = "";
  private $subject = "";
  private $body = "";

  /**
  * Creates a new enquiry.
  * @param string $name Name of the person creating the enquiry
  * @param string $email Email address of the creator.
  * @param string $subject Subject of enquiry
  * @param string $body Body of the message. HTML is disabled.
  */
  public static function createEnquiry(string $name, string $email, string $subject, string $body)
  {
    global $ff_sql, $ff_router;

    if(strlen($name) > 64) {
      return ff_return(false, 'misc-name-too-long');
    }

    if(strlen($subject) > 256) {
      return ff_return(false, 'misc-subject-too-long');
    }

    if(strlen($email) > 256) {
      return ff_return(false, '	misc-address-too-long');
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ff_return(false, 'misc-invalid-email');
		}

    if(strlen($body) > 65535) {
      return ff_return(false, '	misc-body-too-short');
    }

    $emailVerifToken = cryptography::randomString(32);

    $ff_sql->query("
      INSERT INTO support_public
      (id, date, status, name, email, email_verif, subject, body)
      VALUES (
        NULL,
        ". FF_TIME .",
        ". self::STATUS_PENDING_VERIFICATION .",
        ". $ff_sql->quote($name) .",
        ". $ff_sql->quote($email) .",
        ". $ff_sql->quote($emailVerifToken) .",
        ". $ff_sql->quote($subject) .",
        ". $ff_sql->quote($body) ."
      )
    ");

    $lastId = $ff_sql->getLastInsertId();

    $emailDelivery = new email_publicsupportverification(
      $name,
      $ff_router->getPath('contact', [], [
        'mode' => 'host',
        'query' => [
          'id' => $lastId,
          'token' => $emailVerifToken
        ]
      ])
    );

    $emailDelivery->setRecipient($email);
    $emailDelivery->send();

    return ff_return(true, 'misc-success');
  }

  /**
  * Links class instance with an id.
  */
  public function linkById(int $id)
  {
    global $ff_sql;

    $ret = $ff_sql->fetch("
      SELECT *
      FROM support_public
      WHERE id = ". $ff_sql->quote($id) ."
    ", [
      'id' => 'int',
      'status' => 'int',
      'date' => 'int'
    ]);

    if($ret) {
      foreach($ret as $key => $value) {
        $this->$key = $value;
      }
      return true;
    }

    return false;
  }

  /**
  * Gets instance of this class by id.
  */
  public static function getById(int $id)
  {
    $support = new support_public();
    if(!$support->linkById($id)) {
      return false;
    }

    return $support;
  }

  /**
  * To prevent people abusing this system, we will send them a confirmation email.
  * This is the validation token.
  */
  public function verify(string $token)
  {
    global $ff_sql;

    if($this->status !== self::STATUS_PENDING_VERIFICATION) {
      // Already verified or has been marked as complete.
      return false;
    }

    if($this->email_verif !== $token) {
      // Bad email verification token. Failed to verify.
      return false;
    }

    $this->status = self::STATUS_PENDING_ACTION;

    $ff_sql->query("
      UPDATE support_public
      SET status = ". $ff_sql->quote($this->status) ."
      WHERE id = ". $ff_sql->quote($this->id) ."
    ");

    return true;
  }

  /**
  * Updates the status
  * @param user $user the person manipulating the status
  * @param int $status the new status.
  */
  public function setStatus(user $user, int $status)
  {
    global $ff_sql;

    if(!$user->getGroup()->can('mod_support')) {
      return ff_return(false, 'misc-permission-denied');
    }

    // Inserting admin audit log
    audits_admin_publicsupportstatus::insert(
      $user,
      $this,
      $this->status,
      $status
    );

    $ff_sql->query("
      UPDATE support_public
      SET status = ". $ff_sql->quote($status) ."
      WHERE id = ". $ff_sql->quote($this->id) ."
    ");

    return ff_return(true);
  }

  /**
  * get id of current instance
  */
  public function getId()
  {
    return $this->id;
  }

  /**
  * Gets status of support public (STATUS_XXXX)
  */
  public function getStatus()
  {
    return $this->status;
  }

  /*
  * Gets the name of the person creating this ticket.
  */
  public function getName()
  {
    return $this->name;
  }

  /**
  * Gets the email address of the person creating this ticket.
  */
  public function getEmail()
  {
    return $this->email;
  }

  /**
  * Gets subject of ticket.
  */
  public function getSubject()
  {
    return $this->subject;
  }

  /**
  * Gets body of this ticket.
  */
  public function getBody()
  {
    return $this->body;
  }
}

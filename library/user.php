<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\user.php
//
// ======================================


class user
{
	private static $userCache = [];
	private $username = '';
	private $email = '';
	private $validEmail = true;
	private $id = 0;
	private $groupId = 0;
	private $nodeAuth = '';


	public function __construct($user = null)
	{
		if($user !== null) {
			// Might be an old bit of come somewhere that still implements this... lets
			// throw and log exception so this never happens again.
			throw new Exception('Update to link via the setUser methods.');
		}
	}

	/**
	* Links the instance of this class by username.
	*
	* @param string $username
	*		The username we want to link with.
	*/
	public function linkByUsername(string $username)
	{
		global $ff_sql;

		if($this->id > 0) {
			throw new Exception('Cannot re-link.');
		}

		$res = $ff_sql->query_fetch("
			SELECT `id`
			FROM `users`
			WHERE `username_lower` = ". $ff_sql->quote(strtolower($username)) ."
			LIMIT 1
		", ['id' => 'int']);

		if(!$res) {
			return ff_return(false, [], 'misc-username-not-found');
		}

		return $this->linkById($res['id']);
	}

	/**
	* Returns the object the user linked with $username
	*
	* @param string $username
	*		The username of the users' object you want
	* @return user|bool On success this return user object, otherwise false.
	*/
	public static function getUserByUsername(string $username)
	{
		$user = new user();
		$link = $user->linkByUsername($username);
		if(!$link->success) {
			return false;
		}
		return $user;
	}

	/**
	* Links the instance of this class by email.
	*
	* @param string string $email
	*		The email we want to link with.
	*/
	public function linkByEmail(string $email)
	{
		global $ff_sql;

		if($this->id > 0) {
			throw new Exception('Cannot re-link.');
		}

		ff_cleanEmail($email);

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ff_return(false, [], 'misc-invalid-email');
		}

		$res = $ff_sql->query_fetch("
			SELECT `id`
			FROM `users`
			WHERE `email` = ". $ff_sql->quote($email) ."
			LIMIT 1
		", [
			'id' => 'int'
		]);

		if(!$res) {
			return ff_return(false, [], 'misc-email-not-found');
		}

		return $this->linkById($res['id']);
	}

	/**
	* Returns the object the user linked with $email
	*
	* @param string $email
	*		The email of the users' object you want
	* @return user|bool On success this return user object, otherwise false.
	*/
	public static function getUserByEmail(string $email)
	{
		$user = new user();
		$link = $user->linkByEmail($email);
		if(!$link->success) {
			return false;
		}
		return $user;
	}

	/**
	* Links the current user object with a user id
	*
	* @param int $id
	*		The user who you want to link this user object with.
	*/
	public function linkById(int $id)
	{
		global $ff_sql;

		if($this->id > 0) {
			throw new Exception('Cannot re-link.');
		}

		$res = $ff_sql->query_fetch("
			SELECT
				`id`,
				`username`,
				`email`,
				`email_valid`,
				`group_id`,
				`password`,
				`node_auth`
			FROM `users`
			WHERE
				`id` = ". $ff_sql->quote($id) ."
		", [
			'id' => 'int',
			'group_id' => 'int',
			'email_valid' => 'bool'
		]);

		if(!$res) {
			return ff_return(false);
		}

		$this->username = $res['username'];
		$this->email = $res['email'];
		$this->validEmail = $res['email_valid'];
		$this->id = $res['id'];
		$this->groupId = $res['group_id'];
		$this->nodeAuth = $res['node_auth'];

		return ff_return(true);
	}

	/**
	* Returns the object the user linked with $id
	*
	* @param int $id
	*		The userid of the users' object you want
	* @return user|bool On success this return user object, otherwise false.
	*/
	public static function getUserById(int $id)
	{
		if(isset(self::$userCache[$id])) {
			return self::$userCache[$id];
		}

		$user = new user();
		$link = $user->linkById($id);
		if(!$link->success) {
			return false;
		}
		return self::$userCache[$id] = $user;
	}

	/**
	* Finds Information on users with keywords.
	*
	* @param string $phrase
	*		The phrase we want to search. Will be exploded, and treated.
	*/
	public static function queryUsers(string $phrase)
	{
		global $ff_sql;

		$keywords = array_unique(explode(
			' ',
			str_replace([
					',',
					'!',
					'@',
					'#',
					'$',
					'%',
					'^',
					'&',
					'*',
					'(',
					')',
					'-',
					'+',
					'=',
					'{',
					'}',
					'[',
					']',
					'\\',
					'|',
					'\'',
					'"',
					'.',
					'/',
					'?',
				],
				' ',
				$phrase
			)
		));

		$buildConditions = function($key) use (&$keywords) {
			global $ff_sql;
			$ret = [];

			foreach ($keywords as $keyword) {
				if(strlen($keyword) < 4) {
					// too short
					continue;
				}

				$ret[] = $key .' LIKE '. $ff_sql->quote($ff_sql->escapeWildcard($keyword) . '%');
			}

			return $ret;
		};

		$where = $buildConditions('`users`.`username`');
		$where = array_merge($where, $buildConditions('`users`.`email`'));
		$where = array_merge($where, $buildConditions('`groups`.`name`'));

		if(count($where) == 0) {
			// Nada found
			return false;
		}

		$where = implode(' OR ', $where);
		$where = 'WHERE '. $where;

		return $ff_sql->query_fetch_all("
			SELECT
				`users`.`id` AS user_id,
				`users`.`username` AS user_username,
				`users`.`email` AS user_email,
				`groups`.`id` AS group_id,
				`groups`.`name` AS group_name,
				`groups`.`color` AS group_color,
				`groups`.`id` AS group_id
			FROM
				`users`
			INNER JOIN
				`groups`
			ON
				`users`.`group_id` = `groups`.`id`
			$where
		");
	}

	/**
	* Procedure for creating a new user
	*
	* @param string $username
	*		Username of account creator.
	* @param string $email
	*		Email of user creating account.
	* @param string $password
	*		Password of the user creating the account
	* @param string $password2
	*		Verification password (compares it against the first password.)
	* @param bool $mailingList
	*		Whether or not we are to sign user up to mailing list
	*/
	public static function newUser(
		string $username,
		string $email,
		string $password,
		string $password2,
		bool $mailingList = true
	) {
		global $ff_config, $ff_sql, $ff_context, $ff_router;

		if(!self::isValidUsername($username)) {
			return ff_return(false, [], 'misc-invalid-username');
		}

		if(self::usernameExists($username)) {
			return ff_return(false, [], 'misc-taken-username');
		}

		if($password !== $password2) {
			return ff_return(false, [], 'misc-password-mismatch');
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ff_return(false, [], 'misc-invalid-email');
		}

		if(self::emailExists($email)) {
			return ff_return(false, [], 'misc-email-taken');
		}

		if(!($pw = self::isValidPassword($password))->success) {
			return ff_return(false, [], $pw->messageKey);
		}

		ff_cleanEmail($email);

		$groupId = intval($ff_config->get('group-pre-email-verification'));
		$usernameLower = strtolower($username);
		$passwordHashed = cryptography::hashPassword($password);
		$nodeAuth = cryptography::randomString(16);

		$res = $ff_sql->query("
			INSERT INTO `users`
			(`id`, `username`, `username_lower`, `email`, `email_valid`, `group_id`, `password`, `node_auth`)
			VALUES (
				NULL,
				". $ff_sql->quote($username) .",
				". $ff_sql->quote($usernameLower) .",
				". $ff_sql->quote($email) .",
				1,
				". $ff_sql->quote($groupId) .",
				". $ff_sql->quote($passwordHashed) .",
				". $ff_sql->quote($nodeAuth) ."
			)
		");

		if(!$res) {
			throw new Exception('Failed to insert new user');
		}

		// Getting user id. NOTE: Be sure to do this RIGHT AFTER inserting!
		$userId = intval($ff_sql->getLastInsertId());

		// Initialize user settings.
		settings::initializeUserSettings($userId);

		// Mailing list stuff.
		if($mailingList) {
			maillist::subscribe($email, $userId);
		}

		// Getting user object.
		$user = user::getUserById($userId);

		// Email verification...
		$awaitingEmailVerification = ff_stringToBool($ff_config->get('email-verification-enabled'));
		if($awaitingEmailVerification) {
			$user->sendEmailVerification();
		}

		return ff_return(true, (object)[
			'awaitingEmailVerification' => $awaitingEmailVerification,
			'id' => $userId,
			'getUser' => function() use($userId) {
				return user::getUserById($userId);
			}
		], 'misc-created-user-success');
	}

	/**
	* Sends email verification to user, assuming shim needs it.
	*/
	public function sendEmailVerification()
	{
		global $ff_sql, $ff_router;

		if(!$this->isPendingEmailVerification()) {
			return ff_return(false, [], 'misc-not-pending-verification');
		}

		// Creating token for email verification
		$verificationToken = cryptography::randomString(255);
		$mailVarifInsert = $ff_sql->query("
			INSERT INTO `email_verification`
			(`id`, `user_id`, `date`, `expiry`, `token`, `used`)
			VALUES (
				NULL,
				". $ff_sql->quote($this->id) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote(FF_TIME + FF_MONTH) .",
				". $ff_sql->quote($verificationToken) .",
				0
			)
		");

		if(!$mailVarifInsert) {
			// Failed to insert email verification data
			throw new Exception('Failed to insert email verification token');
		}

		$emailSender = new email_verification($this->username, $ff_router->getPath('emailverif', [
			'token' => $verificationToken,
			'user_id' => $this->id
		], [
			'mode' => 'host'
		]));
		$emailSender->setRecipient($this->email);
		$emailSender->send();

		return ff_return(true);
	}

	/**
	* Checks if a username exists
	*
	* @param string $username
	*		Username we want to see exists
	*/
	public static function usernameExists(string $username)
	{
		global $ff_sql;
		if(!self::isValidUsername($username)) {
			throw new Exception('Invalid username');
		}

		$res = $ff_sql->query_fetch("
			SELECT count(1) as cnt
			FROM `users`
			WHERE `username_lower` = ". $ff_sql->quote(strtolower($username)) ."
			LIMIT 1
		", ['cnt' => 'int']);

		if(!$res) {
			throw new Exception('query fail');
		}

		return $res['cnt'] > 0;
	}

	/**
	* Checks if a username is valid
	*
	* @param string $username
	*		Username we want to see is valid
	*/
	public static function isValidUsername($username)
	{
		return strlen($username) >= 4 && strlen($username) < 32;
	}

	/**
	* Checks whether a name is considered valid
	*
	* @param string $name
	*		The name we want to check is vlaid.
	*/
	public static function isValidName(string $name)
	{
		throw new Exception('Use \'isValidUsername\' as this is no longer used.');
	}

	/**
	* Checks if any given password is considered sound.
	*
	* @param string $password
	*		The password we want to check.
	*/
	public static function isValidPassword(string $password)
	{
		$passwordLength = strlen($password);

		if($passwordLength < 6) {
			return ff_return(false, [], 'misc-password-short');
		}

		if($passwordLength > 256) {
			return ff_return(false, [], 'misc-password-long');
		}

		return ff_return(true);
	}

	/**
	* checks if a email is in use
	*
	* @param string $email
	*		The email we want to see is in use.
	*/
	public static function emailExists(string $email)
	{
		global $ff_sql;

		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		ff_cleanEmail($email);

		$res = $ff_sql->query_fetch("
			SELECT count(1) as cnt
			FROM `users`
			WHERE `email` = ". $ff_sql->quote($email) ."
			LIMIT 1
		", ['cnt' => 'int']);

		if(!$res) {
			throw new Exception('query fail');
		}

		return $res['cnt'] > 0;
	}

	/**
	* returns array of users
	* @param int $index
	*		The index we start the query at
	* @param int $length
	*		how many we want to return.
	*/
	public static function getUsers(int $index, int $length)
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch_all("
			SELECT
				`users`.`id` AS id,
				`users`.`username` AS username,
				`users`.`username_lower` AS username_lower,
				`users`.`email` AS email,
				`users`.`email_valid` AS email_valid,
				`users`.`node_auth` AS node_auth,
				`groups`.`id` AS group_id,
				`groups`.`name` AS group_name,
				`groups`.`color` AS group_color,
				`groups`.`id` AS group_id
			FROM
				`users`
			INNER JOIN
				`groups`
			ON
				`users`.`group_id` = `groups`.`id`
			WHERE
				`users`.`id` > ". $ff_sql->quote($index) ."
			ORDER BY `users`.`id` ASC
			LIMIT ". $ff_sql->quote($length) ."
		");

		return $res;
	}

	/**
	* returns amount of users in the database
	*/
	public static function getUsersCount()
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT COUNT(*) as num
			FROM `users`
		");

		return intval($res['num']);
	}



	/**
	* Verifies an email
	*
	* @param string $token
	*		Token to verify the user with.
	*/
	public function verifyEmail(string $token)
	{
		global $ff_sql, $ff_config;

		if (!$this->isPendingEmailVerification()) {
			return ff_return(false, [], 'misc-not-pending-email-verification');
		}

		$res = $ff_sql->query_fetch("
			SELECT `id`, `date`, `expiry`, `used`
			FROM `email_verification`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ." AND
				`token` = ". $ff_sql->quote($token) ."
		", ['id' => 'int', 'date' => 'int', 'expiry' => 'int']);

		if(!$res) {
			return ff_return(false, [], 'misc-emailverif-token-missing');
		}

		if($res['used']) {
			return ff_return(false, [], 'misc-emailverif-token-used');
		}

		if($res['expiry'] < FF_TIME) {
			return ff_return(false, [], 'misc-emailverif-token-expired');
		}

		$ff_sql->query("
			UPDATE `email_verification`
			SET `used` = 1
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ." AND
				`token` = ". $ff_sql->quote($token) ."
		");

		$ff_sql->query("
			UPDATE `users`
			SET
				`group_id` = ". $ff_sql->quote($ff_config->get('group-post-email-verification')) ."
			WHERE `id` = ". $ff_sql->quote($this->id) ."
		");

		// Updating group id for cache.
		$this->groupId = intval($ff_config->get('group-post-email-verification'));

		// Fixing cache.
		$this->selfCache();

		return ff_return(true, [], 'misc-emailverif-success');
	}

	/**
	* Pushes a recovery email to the client.
	*/
	public function pushRecoveryEmail()
	{
		global $ff_sql, $ff_request, $ff_router, $ff_context;

		// Doesnt NEED to be unique....
		$token = cryptography::randomString(64);

		$userAgent = $ff_request->getHeader('user-agent');
		if(!$userAgent) {
			$userAgent = 'Unknown';
		}

		$ff_sql->query("
			INSERT INTO `password_reset`
			(`id`, `user_id`, `enabled`, `token`, `date`, `expiry`, `ip`)
			VALUES (
				NULL,
				". $ff_sql->quote($this->id) .",
				1,
				". $ff_sql->quote($token) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote(FF_TIME + FF_DAY) .",
				". $ff_sql->quote($ff_request->getIp()) ."
			)
		");

		// Sending email, if email is valid.
		if($this->validEmail) {
			$emaiSender = new email_passwordresetlink(
				$this->username,
				$ff_router->getPath('recovery', [], [
					'query' => [
						'token' => $token,
						'user_id' => $this->id
					],
					'mode' => 'host',
					'allowForceParam' => false
				]),
				$ff_request->getIp(),
				$userAgent
			);
			$emaiSender->setRecipient($this->email);
			$emaiSender->send();
		}

		// Fixing cache.
		$this->selfCache();

		return ff_return(true);
	}

	/**
	* This will reset a users password with a token that was sent to his email.
	*
	* @param string $token
	*		The token that was sent to his email.
	* @param string $ip
	*		The IP of the user who sent the email (ignoring this will get the request ip)
	*/
	public function resetPasswordViaRecovery(string $token, string $ip = '')
	{
		global $ff_sql, $ff_request, $ff_context;
		if(empty($ip)) {
			$ip = $ff_request->getIp();
		}

		$passwordResetEntry = $ff_sql->query_fetch("
			SELECT `id`, `enabled`, `token`, `date`, `expiry`, `ip`
			FROM `password_reset`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ." AND
				`token` = ". $ff_sql->quote($token) ."
		", [
			'id' => 'int',
			'enabled' => 'bool',
			'date' => 'int',
			'expiry' => 'int',
		]);

		if(!$passwordResetEntry) {
			return ff_return(false, [], 'misc-password-reset-token-invalid');
		}

		if(!$passwordResetEntry['enabled']) {
			return ff_return(false, [], 'misc-password-reset-disabled');
		}

		if($passwordResetEntry['expiry'] < FF_TIME) {
			return ff_return(false, [], 'misc-password-reset-expired');
		}

		if($passwordResetEntry['ip'] != $ip) {
			return ff_return(false, [], 'misc-password-reset-ip-mismatch');
		}

		// Generating and checking password
		$password = cryptography::randomString(8);
		$passwordValid = $this->isValidPassword($password);
		if(!$passwordValid->success) {
			return $passwordValid;
		}

		$passwordChange = $this->changePassword($password, false);
		if(!$passwordChange->success) {
			return $passwordChange;
		}

		// Sending emails
		$emailSender = new email_temporarypassword(
			$this->username,
			$password
		);
		$emailSender->setRecipient($this->email);
		$emailSender->send();

		// Fixing cache.
		$this->selfCache();

		return ff_return(true);
	}

	/**
	* Public function to change user password. This is what the user will use when
	* he normally changes his password.
	*
	* @param string $currentPassword
	*		His current password, this is for verification.
	* @param string $newPassword
	*		The new password.
	*/
	public function updatePassword(string $currentPassword, string $newPassword)
	{
		global $ff_sql;

		// Chceking password, this will help towards csrf.
		if(!$this->comparePassword($currentPassword)) {
			return ff_return(false, [], 'misc-wrong-password');
		}

		return $this->changePassword($newPassword, true, false);
	}

	/**
	* This function requires the group permission can_mod_users, and user who's
	* changing the group will need to be provided in parameter one.
	*
	* @param user $user
	*		User who's changing the group.
	* @param group $newGroup
	*		The group the user is about to be assigned.
	* @return ff_return
	*/
	public function updateUsergroup(user $user, group $newGroup)
	{
		global $ff_sql;
		if(!$user->getGroup()->can('mod_users')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		$res = $ff_sql->query("
			UPDATE `users`
			SET
				`group_id` = ". $ff_sql->quote($newGroup->getId()) ."
			WHERE
				id = ". $ff_sql->quote($this->id) ."
		");


		if(!$res) {
			return ff_return(false);
		}

		// Inserting audit log
		audits_admin_changeusergroup::insert(
			$user,
			$this,
			$this->getGroup(),
			$newGroup
		);

		// Updating cache and stuff.
		$this->groupId = $newGroup->getId();
		$this->selfCache();

		return ff_return(true);
	}

	/**
	* The function that actually changes a users password. It does automated things,
	* link sending email notification, pushing old password to his password history,
	* and possibly more in future.
	*
	* NOTE: This is kept private, so as front-end developers cannot change users
	* passwords.
	*
	* @param string $newPassword
	*		The new password.
	* @param bool $sendNotification
	*		True if we want to send email notificaiton, if we dont, leave false.
	* @param bool $protectSensitive
	*		If this is true, it will set user agent and ip address to blank.
	* @param session $session
	*		The session that is changing this password.
	*/
	private function changePassword(
		string $newPassword,
		bool $sendNotification = true,
		bool $protectSensitive = false
	) {
		global $ff_sql, $ff_context, $ff_router, $ff_request;

		if(!$this->isValidPassword($newPassword)) {
			// Invalid password..
			return ff_return(false);
		}

		// Getting current password for history purposes.
		$currentHashedPassword = $this->getHashedPassword();

		// IP and Useragent
		$ip = $ff_request->getIp();
		$useragent = $ff_request->getHeader('user-agent');
		if($useragent) {
			// maximum length
			$useragent = substr($useragent, 0, 256);
		}
		if(!$useragent || $protectSensitive) {
			// Invalid user agent, or we are protecting sensitive information.
			$useragent = '.';
		}
		if($protectSensitive) {
			// Protecting IP address.
			$ip = '';
		}

		// Logging into password history
		$ff_sql->query("
			INSERT INTO `password_history`
			(`id`, `user_id`, `date`, `password`, `ip`, `useragent`)
			VALUES (
				NULL,
				". $ff_sql->quote($this->id) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($currentHashedPassword) .",
				". $ff_sql->quote($ip) .",
				". $ff_sql->quote($useragent) ."
			)
		");

		// Hashing new password
		$newPasswordHashed = cryptography::hashPassword($newPassword);

		// Updating password.
		$ff_sql->query("
			UPDATE `users`
			SET
				`password` = ". $ff_sql->quote($newPasswordHashed) ."
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		if($sendNotification) {
			$emailSender = new email_passwordchange(
				$this->username,
				$ff_router->getPath('recovery', [], ['mode' => 'host', 'allowForceParam' => false]),
				$ff_router->getPath('cp_settings', [], ['mode' => 'host', 'allowForceParam' => false])
			);
			$emailSender->setRecipient($this->email);
			$emailSender->send();
		}

		// Fixing cache. I dont think this function modifies any variables, but w/e.
		$this->selfCache();

		return ff_return(true);
	}

	/**
	* Compares a password, against to the one in the database.
	*
	* @param string $password
	*		Password we want to see is that of the one in the database.
	*/
	public function comparePassword(string $password)
	{
		global $ff_sql;
		$hashedPassword = $this->getHashedPassword();
		return cryptography::verifyHash($password, $hashedPassword);
	}

	/**
	* Gets the users full name
	*/
	public function getFullName()
	{
		throw new Exception('Fullname support has been removed.');
	}

	/**
	* Checks if user is pending email verification
	* @return bool true if pending verification, otherwise false.
	*/
	public function isPendingEmailVerification()
	{
		global $ff_config;
		return (
			ff_stringToBool($ff_config->get('email-verification-enabled')) &&
			$this->groupId == intval($ff_config->get('group-pre-email-verification'))
		);
	}

	/**
	* Updates the users email address.
	*
	* @param string $password
	*		The users current password, this is used to validage the password change.
	* @param string $newEmail
	*		The email-address-to-be.
	* @param bool $pushNotification
	*		Whether or not we want to push email verification.
	*/
	public function changeEmail(
		string $password,
		string $newEmail,
		bool $pushNotification = true
	) {
		global $ff_sql, $ff_context, $ff_router;

		ff_cleanEmail($newEmail);

		if(!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
			return ff_return(false, [], 'misc-invalid-email');
		}

		// Chceking password, this will help towards csrf.
		if(!$this->comparePassword($password)) {
			return ff_return(false, [], 'misc-wrong-password');
		}

		if($newEmail === $this->email) {
			// same
			return ff_return(false, [], 'misc-invalid-email');
		}

		if(self::emailExists($newEmail)) {
			// already exists
			return ff_return(false, [], 'misc-invalid-email');
		}

		// Old email information
		$oldEmail = $this->email;
		$oldEmailValid = $this->validEmail;

		// Pushing to history.
		$ff_sql->query("
			INSERT INTO `email_history`
			(`id`, `user_id`, `date`, `email`, `was_valid`)
			VALUES (
				NULL,
				". $ff_sql->quote($this->id) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($this->email) .",
				". $ff_sql->quote($this->validEmail) ."
			)
		");

		// Updating email
		$ff_sql->query("
			UPDATE `users`
			SET
				`email` = ". $ff_sql->quote($newEmail) .",
				`email_valid` = 1
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		// Updating this object.
		$this->email = $newEmail;
		$this->validEmail = true;// Assume valid, until proven otherwise.

		// Pushing notification, if enabled.
		if($pushNotification) {
			$emailSender = new email_emailchange(
				$this->username,
				$oldEmail,
				$newEmail
			);

			// Add old email to recipient list, only if it's considered valid.
			if($oldEmailValid) {
				$emailSender->setRecipient($oldEmail);
			}
			$emailSender->setRecipient($newEmail);
			$emailSender->send();
		}

		// User is pending email verification, and email is not verfied. So resend
		// verification email.
		if($this->isPendingEmailVerification()) {
			// NOTE: Potential bug/edge case: User can technically verify this email
			// from a recovery email sent to his previous address. As of writing this,
			// it's a non-issue as we assume the set address is valid.
			$this->sendEmailVerification();
		}

		// Fixing cache.
		$this->selfCache();

		return ff_return(true, [], 'misc-success');
	}

	/**
	* Marks the users email as invalid. This might be because it doesn't exist, or
	* something else?
	*/
	public function markEmailAsInvalid()
	{
		global $ff_sql;

		// Marking local variable as false... As it is.
		$this->validEmail = false;

		// Updating database.
		$ff_sql->query("
			UPDATE `users`
			SET `email_valid` = 0
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		// Fixing cache.
		$this->selfCache();
	}

	/**
	* Sends email to the current user object.
	* @param user $sender
	*		The person who's sending the email
	* @param string $subject
	*		Subject of email
	* @param string $htmlBody
	*		HTML body of email
	* @param string $textBody (optional)
	*		Raw text email
	* @return array (ff_return)
	*/
	public function sendEmail(user $sender, string $subject, string $htmlBody, string $textBody = '')
	{
		if(!$sender->getGroup()->can('mod_users')) {
			return ff_return(false, 'misc-permission-denied');
		}

		audits_admin_sendemail::insert($sender, $this, $subject);

		$mailBuilder = new email_builder();
		$mailBuilder->setSubject($subject);
		if(strlen($htmlBody) > 0) {
			$mailBuilder->setHtml($htmlBody);
		}

		if(strlen($textBody) > 0) {
			$mailBuilder->setBody($textBody);
		}

		$mailBuilder->setRecipient($this->getEmail());

		if($mailBuilder->send()) {
			return ff_return(true);
		}
		else {
			return ff_return(false);
		}
	}

	/**
	* Wrapper function for subscriptions::giveSubscription
	*/
	public function giveSubscription(plan $plan, bool $sendMail = true)
	{
		return subscriptions::giveSubscription($this, $plan, $sendMail);
	}

	/**
	* Wrapper function for subscriptions::disableSubscription
	*/
	public function disableSubscription()
	{
		return subscriptions::disableSubscription($this);
	}

	/**
	* Rather than actually disabling it, it will clear a plan from his existing
	* subscription.
	*/
	public function removeSubscription(plan $plan)
	{
		return subscriptions::removeSubscription($this, $plan);
	}

	/**
	* Wrapper function for subscriptions::getSubscription
	*/
	public function getSubscription()
	{
		return subscriptions::getSubscription($this);
	}

	/**
	* Checks if user has valid email. (Meaning it exists on remote server.)
	*/
	public function hasValidEmail()
	{
		return $this->validEmail;
	}

	/**
	* Whether or not user can write a review
	* @return bool
	*/
	public function canReview()
	{
		return review::canUserReview($this);
	}

	/**
	* Gets the users hashed password.
	* NOTE: This is for internal calls only
	*/
	private function getHashedPassword()
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT `password`
			FROM `users`
			WHERE
				`id` = ". $ff_sql->quote($this->id) ."
		");

		if(!$res) {
			// This should never happen....
			throw new Exception('Unable to get password');
		}

		return $res['password'];
	}

	/**
	* Gets the users password history.
	* @return bool|array on success, returns array, on failure, returns false.
	*/
	public function getPasswordHistory()
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch_all("
			SELECT `id`, `user_id`, `date`, `password`, `ip`, `useragent`
			FROM `password_history`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
		]);

		if(!$res) {
			return false;
		}

		return $res;
	}

	/**
	* Checks whether user has a password history
	*/
	public function hasPasswordHistory()
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT count(1) as cnt
			FROM `password_history`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ."
			LIMIT 1
		", [
			'cnt' => 'int',
		]);

		return $res['cnt'] > 0;
	}

	/**
	* Gets email history
	* @return bool|array on success, returns array, on failure, returns false.
	*/
	public function getEmailHistory()
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch_all("
			SELECT `id`, `user_id`, `date`, `email`
			FROM `email_history`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ."
		", [
			'id' => 'int',
			'user_id' => 'int',
			'date' => 'int',
		]);

		if(!$res) {
			return false;
		}

		return $res;
	}

	/**
	* Checks if a user has an email history
	* @return bool
	*/
	public function hasEmailHistory()
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT count(1) as cnt
			FROM `email_history`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ."
			LIMIT 1
		", ['cnt' => 'int']);

		return $res['cnt'] > 0;
	}

	public function getNotifications(bool $showHidden = false)
	{
		return notification::getUserNotifcations($this, $showHidden);
	}

	public function getConnectionStatistics(int $beginning = -1, int $duration = FF_MONTH, int $precision = FF_DAY)
	{
		global $ff_sql, $ff_context;

		if($beginning == -1) {
			$beginning = FF_TIME  - $duration;
		}

		// round beginning to nearest whole percission. This fix's cache bugs, and
		// is just good practice.
		$beginning = intval(ceil($beginning / $precision) * $precision);

		$cache = $ff_context->getCache();
		$cacheKey = ff_cacheKey(__CLASS__ . __FUNCTION__, [$beginning, $duration, $precision, $this->id]);
		if($obj = $cache->get($cacheKey)) {
			return $obj;
		}

		$queryResult = $ff_sql->query_fetch_all("
			SELECT
				id,
				SUM(`data_sent`) as data_sent,
				SUM(`data_received`) as data_received,
				(ROUND(`connect_date` DIV ". $ff_sql->quote($precision) .", 0) * ". $ff_sql->quote($precision) .") AS day
			FROM
				`connections`
			WHERE
				`user_id` = ". $ff_sql->quote($this->id) ." AND
				`connect_date` > ". $ff_sql->quote($beginning) ." AND
				`connect_date` < ". $ff_sql->quote($beginning + $duration) ."
			GROUP BY `id`
		", [
			'data_sent' => 'int',
			'data_received' => 'int',
			'day' => 'int'
		]);

		$dayData = [];
		if($queryResult) {
			array_map(function($row) use(&$dayData) {
				if(isset($dayData[$row['day']])) {
					$dayData[$row['day']]['data_sent'] += $row['data_sent'];
					$dayData[$row['day']]['data_received'] += $row['data_received'];
				}
				else {
					$dayData[$row['day']] = $row;
				}
			}, $queryResult);
		}

		$returnValue = [];
		for($i = $beginning; $i < $beginning + $duration && $i < FF_TIME; $i += $precision) {
			if(isset($dayData[$i])) {
				$returnValue[] = [
					'data_sent' => $dayData[$i]['data_sent'],
					'data_received' => $dayData[$i]['data_received'],
					'date' => $i,
				];
			}
			else {
				$returnValue[] = [
					'data_sent' => 0,
					'data_received' => 0,
					'date' => $i,
				];
			}
		}

		// Store it for a day
		$cache->store($cacheKey, $returnValue, FF_TIME + (FF_MINUTE * 10));

		return $returnValue;
	}

	/**
	* Gets the users active connection count
	*/
	public function getConnectionCount()
	{
		if($rpc = ffrpc::getRpcByType(ffrpc::TYPE_BACKEND)) {
			return intval($rpc->do('get-user-connection-count', [
				'user' => $this->id
			]));
		}
		else {
			return false;
		}
	}

	/**
	* Wrapper for payments::getUserPayments, but autofilling the first parameter.
	*/
	public function getPayments(int $page, int $limit = 32)
	{
		return payment::getUserPayments($this, $page, $limit);
	}

	/**
	* Gets the users id. (user_id)
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Gets the users username
	*/
	public function getUsername()
	{
		return $this->username;
	}

	/**
	* Gets censored username
	*/
	public function getCensoredUsername()
	{
		return ff_censorName($this->username);
	}

	/**
	* Gets the users email address.
	*/
	public function getEmail()
	{
		return $this->email;
	}

	/**
	* Gets instance of settings class.
	*/
	public function getSettings()
	{
		return settings::getByUserId($this->id);
	}

	/**
	* Gets the users active group id
	* @return int
	*/
	public function getGroupId()
	{
		return $this->groupId;
	}

	/**
	* Gets the users node authentication token
	* @return string
	*/
	public function getNodeAuth()
	{
		return $this->nodeAuth;
	}

	/**
	* Gets the users current group object.
	* @return group
	*/
	public function getGroup()
	{
		return group::getGroupById($this->groupId);
	}

	/**
	* Same as the date() function, but auto adjusts to the users timezone.
	*
	* @param string $format
	*		Format for the return vale.
	* @param int $timestamp
	*		The time at which this occused. This is assumed to be the servers time.
	*/
	public function date(string $format, int $timestamp = -1)
	{
		if($timestamp == -1) {
			$timestamp = time();
		}

		$settings = $this->getSettings();
		$offset = $settings->getOption('time_difference');
		$timestamp += $offset;

		return date($format, $timestamp);
	}

	/**
	* Gets date format linked with user.
	*/
	public function dateFormat()
	{
		// TODO: Make this a user configurable thing.
		return 'F j, Y, g:i a';
	}

	/**
	* Updates the current cached object. This should be called whenever a local
	* variable has been changed.
	*/
	private function selfCache()
	{

	}
}

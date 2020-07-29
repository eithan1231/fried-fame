<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\session.php
//
// ======================================


class session
{
	/**
	* Users linked with session
	* @var array|null
	*/
	private $links = null;

	/**
	* The current session id.
	* @var int|null
	*/
	private $sessionId = null;

	/**
	* The session token
	* @var string|null
	*/
	private $sessionToken = null;

	/**
	* The selected language code
	* @var string
	*/
	private $languageCode = '..';

	/**
	* The unix time of when the session becomes void
	* @var int
	*/
	private $sessionExpiry = 0;

	/**
	* The active link id (so the user who is currently getting used with the session)
	*
	* default value is 0, which means nobody is linked.
	*
	* @var int
	*/
	private $activeLinkId = 0;

	/**
	* Cached security token object
	*
	* @var security_token
	*/
	private $securityToken = null;

	/**
	* Links this session object with a specific token (assuming it exists and stuff)
	*
	* @param string $token
	*		token which we want to link with this object
	*/
	public function linkByToken(string $token)
	{
		global $ff_sql;
		$result = $ff_sql->query_fetch("
			SELECT
				`id`,
				`date`,
				`expiry`,
				`active_link_id`,
				`language_code`
			FROM
				`sessions`
			WHERE
				`token` = ". $ff_sql->quote($token) ."
		", [
			'id' => 'int',
			'date' => 'int',
			'expiry' => 'int',
			'active_link_id' => 'int'
		]);

		if($result !== false) {
			$this->sessionId = $result['id'];
			$this->sessionToken = $token;
			$this->languageCode = $result['language_code'];
			$this->activeLinkId = $result['active_link_id'];
			$this->sessionExpiry = $result['expiry'];

			return true;
		}
		else {
			return false;
		}
	}

	/**
	* Gets a session by it's token.
	*
	* @param string $token
	*		The token which we will be querying
	*/
	public static function getSessionByToken(string $token)
	{
		global $ff_context;

		$session = new session();
		if(
			$session->linkByToken($token) &&
			$session->getExpiry() > FF_TIME
		) {
			return $session;
		}
		return false;
	}

	/**
	* Creates a new session.
	*/
	public static function createSession()
	{
		$session = new self();
		$session->_createSession();
		return $session;
	}

	/**
	* Checks if token exists.
	* @return bool
	*/
	private function tokenExists(string $t)
	{
		global $ff_sql;

		$res = $ff_sql->query_fetch("
			SELECT
				count(1) AS cnt
			FROM `sessions`
			WHERE
				`token` = ". $ff_sql->quote($t) ."
		", ['cnt' => 'int']);

		if(!$res) {
			throw new Exception('query err');
		}

		return $res['cnt'] > 0;
	}

	/**
	* Basically configures this ssession object as a new session.
	* NOTE: It's name is as it is, as there is a public function called createSession what is static.
	*/
	private function _createSession()
	{
		global $ff_sql, $ff_config, $ff_request, $ff_context;

		// Generating a token, and making sure it doesnt exist.
		while($this->tokenExists($token = cryptography::randomString(256)));

		$expiry = FF_TIME + intval($ff_config->get('session-valid-duration'));

		// Getting the language... hahhahhahahha. goddamn multi-lingual support.
		$languages = (new language())->getLanguages();
		$acceptLanguages = $ff_request->getAcceptLanguage();
		$languageCode = $ff_config->get('session-default-language');
		if($acceptLanguages) {
			foreach($acceptLanguages as $acceptLang) {
				if($acceptLang['language_code'] === '*') {
					// Wildcard, set default.
					$languageCode = $ff_config->get('session-default-language');
					break;
				}

				// Whether or not the accept-lang index matches a registered language.
				$found = false;

				// Checking if matches a registered language.
				foreach($languages as $lang) {
					if($acceptLang['language_code'] === $lang->languageCode()) {
						$languageCode = $lang->languageCode();
						$found = true;
						break;
					}
				}

				if($found) {
					// matched, break.
					break;
				}
			}
		}

		$insertRes = $ff_sql->query("
			INSERT INTO `sessions`
			(`id`, `token`, `date`, `expiry`, `active_link_id`, `language_code`)
			VALUES (
				NULL,
				". $ff_sql->quote($token) .",
				". $ff_sql->quote(FF_TIME) .",
				". $ff_sql->quote($expiry) .",
				0,
				". $ff_sql->quote($languageCode) ."
			)
		");

		if(!$insertRes) {
			throw new Exception('failed to insert query');
		}

		// Getting and setting session id to this object.
		$this->sessionId = $ff_sql->getLastInsertId('sessions');
		$this->sessionToken = $token;
		$this->languageCode = $languageCode;
		$this->activeLinkId = 0;
		$this->sessionExpiry = $expiry;

		$ff_context->getLogger()->log('Created new session', [
			'id' => $this->sessionId,
			'token' => $token,
			'language' => $languageCode
		]);
	}

	/**
	* Gets the users linked with this session
	*
	* @return array|null
	*		If user(s) are linked, it will return them in an array. If no links are
	*		found, it will return null.
	*/
	public function getLinks($purge = false)
	{
		global $ff_sql;

		if($purge) {
			$this->resetLocalLinks();
		}

		if($this->links === null) {
			$this->links = $ff_sql->query_fetch_all("
				SELECT
					`id`,
					`user_id`,
					`pending_auth`,
					`require_reauth`
				FROM `session_links`
				WHERE
					`session_id` = ". $ff_sql->quote($this->sessionId) ."
			", [
				'id' => 'int',
				'user_id' => 'int',
				'require_reauth' => 'bool'
			]);
		}

		return $this->links;
	}

	/**
	* Resets the locally stored session links.
	*/
	public function resetLocalLinks()
	{
		$this->links = null;
	}

	/**
	* Gets the currently set link.
	* @return bool|array on success, returns link of user, on failure, returns false.
	*/
	public function getActiveLink()
	{
		// Getting the list of links... This function depends on that local variable
		// being set.
		$this->getLinks(false);

		if(!$this->links) {
			return false;
		}

		foreach ($this->links as $link) {
			if($link['id'] == $this->activeLinkId) {
				return $link;
			}
		}

		return false;
	}

	/**
	* Gets the user object linked with the active link
	* @return false|user
	*/
	public function getActiveLinkUser()
	{
		$acitvelink = $this->getActiveLink();
		if(!$acitvelink) {
			return false;
		}
		return user::getUserById($acitvelink['user_id']);
	}

	/**
	* Links a user with this session.
	*
	* @param string|int|user $user
	*		The user who we are now linking with this session. If this is numeric, it
	*		will assume it's the user_id, otherwise we assume it's the username.
	* @param string $password
	*		The password of the user we want to link.
	*/
	public function linkUser($user, string $password)
	{
		global $ff_sql, $ff_context;

		if(gettype($user) == 'user') {
			$userIdMode = false;
			$userObject = $user;
		}
		else {
			$userIdMode = is_int($user);
			if($userIdMode) {
				$userObject = user::getUserById($user);
			}
			else {
				$userObject = user::getUserByUsername($user);
			}
		}

		if(!$userObject) {
			return ff_return(false, [
				'additionalAuth' => false
			], ($userIdMode ? 'misc-userid-not-found' : 'misc-username-not-found'));
		}

		$passwordCompare = $userObject->comparePassword($password);
		if($passwordCompare) {
			// Getting additiona auth crap
			$additionalAuth = additionalauth::getUserAuth($userObject);
			$additionalAuthName = '';
			if($additionalAuth) {
				if($additionalAuth->enabled()) {
					$additionalAuthName = $additionalAuth->getName();
				}
			}

			// Existing link stuff...
			$links = $this->getLinks();
			if($links) {
				foreach($links as $link) {
					if($link['user_id'] == $userObject->getId()) {
						// User is already linked to session

						if($link['require_reauth']) {
							if($additionalAuth) {
								// Triggering additional authentication onlogin event.
								$additionalAuth->onLogonEvent($userObject);
							}

							// Requires re-authentication
							$ff_sql->query("
								UPDATE `session_links`
								SET
									`require_reauth` = 0,
									`pending_auth` = ". $ff_sql->quote($additionalAuthName) ."
								WHERE
									`id` = ". $ff_sql->quote($link['id']) ."
							");
						}

						// Updating active id.
						$this->updateActiveId($link['id']);

						// Purging getlinks cache
						$this->resetLocalLinks();

						$ff_context->getLogger()->log('Re-activated link on session.', [
							'id' => $this->sessionId,
							'token' => $this->sessionToken,
							'link' => $link['id'],
							'user_id' => $link['user_id'],
							'username' => $userObject->getUsername(),
						]);

						return ff_return(true, [
							'additionalAuth' => strlen($additionalAuthName) > 0
						], 'misc-login-successful');
					}
				}
			}

			// Inserting new link (we checked, and user isnt in exsiting links.)
			$ff_sql->query("
				INSERT INTO `session_links`
				(`id`, `session_id`, `user_id`, `pending_auth`, `require_reauth`)
				VALUES (
					NULL,
					". $ff_sql->quote($this->sessionId) .",
					". $ff_sql->quote($userObject->getId()) .",
					". $ff_sql->quote($additionalAuthName) .",
					0
				)
			");

			// Updating active id
			$linkId = $ff_sql->getLastInsertId('session_links');
			$this->updateActiveId($linkId);

			// Triggering additional authentication onlogin event.
			if($additionalAuth) {
				$additionalAuth->onLogonEvent($userObject);
			}

			$ff_context->getLogger()->log('Added new link to session.', [
				'id' => $this->sessionId,
				'token' => $this->sessionToken,
				'link' => $linkId,
				'user_id' => $userObject->getId(),
				'username' => $userObject->getUsername(),
			]);

			return ff_return(true, [
				'additionalAuth' => strlen($additionalAuthName) > 0
			], 'misc-login-successful');
		}
		else {
			return ff_return(false, [
				'additionalAuth' => false
			], 'misc-wrong-password');
		}
	}

	/**
	* Removes pending additional authentication from the active link id.. This
	* should only be called after validation of the additioanl auth has been done.
	*/
	public function removePendingAuthOnActiveLink()
	{
		global $ff_sql;
		$this->getLinks(false);

		if(!$this->links) {
			return false;
		}

		foreach ($this->links as $k => $link) {
			if($link['id'] == $this->activeLinkId) {

				$this->links[$k]['pending_auth'] = '';

				return $ff_sql->query("
					UPDATE `session_links`
					SET `pending_auth` = ''
					WHERE
						`id` = ". $ff_sql->quote($this->activeLinkId) ."
				") !== false;
			}
		}

		return false;
	}

	/**
	* Checks if a user is linked with the session (if it exists in links regardless
	* of additional auth)
	*
	* @param user $user
	*		The user who we are checking is on the list.
	*/
	public function getLinkIfLinked(user $user)
	{
		$links = $this->getLinks();
		if($links) {
			foreach ($links as $link) {
				if($link['user_id'] == $user->getId()) {
					return $link;
				}
			}
		}
		return false;
	}

	/**
	* Updates the active id.
	*
	* @param int $id
	*		The new active ID.
	*/
	private function updateActiveId(int $id)
	{
		global $ff_sql;

		// Getting active link
		$this->activeLinkId = $id;

		// Updating active link
		$ff_sql->query("
			UPDATE `sessions`
			SET `active_link_id` = ". $ff_sql->quote($this->activeLinkId) ."
			WHERE
				`id` = ". $ff_sql->quote($this->sessionId) ."
		");

		// Refetching linked users (purging cache)
		$this->resetLocalLinks();
	}

	/**
	* Switches the active user linked with the session.
	*
	* @param user $user
	*		Changing the current active user link
	* @param string $password
	*		This parameter is optional (depends if the link needs re-auth). If it's
	*		required, you must enter the password.
	*/
	public function switchLink(user $user, string $password = '')
	{
		global $ff_sql, $ff_context;

		// Getting additiona auth crap for in the event reauth is required
		$additionalAuth = additionalauth::getUserAuth($user);
		$additionalAuthName = '';
		if($additionalAuth && $additionalAuth->enabled()) {
			$additionalAuthName = $additionalAuth->getName();
		}

		if($persistentLink = $this->getLinkIfLinked($user)) {
			// NOTE: reauthentication is only required when user clicks logout button,
			// otherwise user can change without any further steps.

			// Setting reauthentication stuff.
			$requireReauth = false;
			if($persistentLink['require_reauth']) {
				if(!$password) {
					$requireReauth = !$user->comparePassword($password);
				}
			}
			else {
				$additionalAuthName = '';
			}

			$ff_sql->query("
				UPDATE `session_links`
				SET
					`require_reauth` = ". $ff_sql->quote($requireReauth) .",
					`pending_auth` = ". $ff_sql->quote($additionalAuthName) ."
				WHERE
					`id` = ". $ff_sql->quote($persistentLink['id']) ."
			");

			// Updating active id.
			$this->updateActiveId($persistentLink['id']);

			$this->resetLocalLinks();

			$ff_context->getLogger()->log('Updated the active link on session.', [
				'id' => $this->sessionId,
				'token' => $this->sessionToken,
				'link' => $persistentLink['id'],
				'user_id' => $user->getId(),
				'username' => $user->getUsername(),
				'require_reauth' => $requireReauth,
				'additional_auth' => $additionalAuthName
			]);

			return ff_return(true, [
				'additionalAuth' => strlen($additionalAuthName) > 0
			], 'misc-switch-link-successful');
		}
		else {
			// Trying to switch to an account not linked with this session.
			return ff_return(false, [
				'additionalAuth' => false
			], 'misc-permission-denied');
		}
	}

	/**
	* Logs out the current user link (and forces reauth)
	*/
	public function logoutLink()
	{
		global $ff_sql, $ff_context;

		$ff_sql->query("
			UPDATE `session_links`
			SET
				`require_reauth` = 1
			WHERE
				`id` = ". $ff_sql->quote($this->activeLinkId) ." AND
				`session_id` = ". $ff_sql->quote($this->sessionId) ."
		");

		$ff_context->getLogger()->log('Logged active link out.', [
			'id' => $this->sessionId,
			'token' => $this->sessionToken,
			'link' => $this->activeLinkId
		]);

		// Getting links, to try set to a valid one. This will also purge links.
		$links = $this->getLinks(true);
		$newActiveId = 0;
		foreach ($links as $link) {
			if(
				$link['require_reauth'] ||
				$this->activeLinkId == $link['id']
			) {
				continue;
			}
			$newActiveId = $link['id'];
		}
		$this->updateActiveId($newActiveId);

		return ff_return(true);
	}

	/**
	* When you switch session links, you may require to reauthenticate the user.
	* This function is called once user has switched link, and is then
	* reauthenticating
	*/
	public function reauth(string $password)
	{
		global $ff_sql, $ff_context;

		$activeLink = $this->getActiveLink();
		$user = user::getUserById($activeLink['user_id']);
		if(!$user) {
			return ff_return(false, [], 'misc-try-again');
		}

		if($user->comparePassword($password)) {
			$ff_sql->query("
				UPDATE `session_links`
				SET
					`require_reauth` = 0
				WHERE
					`id` = ". $ff_sql->quote($activeLink['id']) ." AND
					`session_id` = ". $ff_sql->quote($this->sessionId) ."
			");

			$ff_context->getLogger()->log('Re-authenticated session link.', [
				'id' => $this->sessionId,
				'token' => $this->sessionToken,
				'link' => $activeLink['id'],
				'user_id' => $user->getId(),
				'username' => $user->getUsername()
			]);

			$this->resetLocalLinks();
			return ff_return(true);
		}
		else {
			return ff_return(false, [], 'misc-try-again');
		}
	}

	/**
	* Gets a security token object linked with this session.
	*
	* @param bool
	*		Optional: default is false. If this is true, it will return security_token
	*		object, it being false will return a string security token.
	*/
	public function getSecurityToken(bool $returnObject = false)
	{
		if(!$this->securityToken) {
			$this->securityToken = new security_token($this);
		}

		return ($returnObject
			? $this->securityToken
			: $this->securityToken->getToken()
		);
	}

	/**
	* Gets the ID linked with session
	*/
	public function getId()
	{
		return $this->sessionId;
	}

	/**
	* Gets the token linked with the session. The token that will be set in a cookie.
	*/
	public function getToken()
	{
		return $this->sessionToken;
	}

	/**
	* Gets expiration of session
	*/
	public function getExpiry()
	{
		return $this->sessionExpiry;
	}

	/**
	* Gets the language code linked with session.
	*/
	public function getLanguageCode()
	{
		return strtolower($this->languageCode);
	}
}

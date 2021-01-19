<?php

/**
* Fried-Fame Remote Procedure Call. This is the class we use for interfacing
* with other in-house applications in a secure manner.
*/
class ffrpc
{
  /**
  * TYPE_EMAIL - responsible for all email delivery
  */
  public const TYPE_EMAIL = "ff-email";

  /**
  * TYPE_BACKEND - Responsible for
  */
  public const TYPE_BACKEND = "ff-backend";

  /**
  * Arrat of all FF-RPC types. Should be alphanumeric with "-_"
  */
  public const TYPES = [
    self::TYPE_EMAIL,
    self::TYPE_BACKEND
  ];

  private $id = 0;
  private $type = '';
  private $auth_token = '';
  private $endpoint = '';
  private $port = 0;

  /**
  * Fills current instance with FFRPC-data
  */
  private function linkByData($data)
  {
    $this->id = $data['id'];
    $this->type = $data['type'];
    $this->auth_token = $data['auth_token'];
    $this->endpoint = $data['endpoint'];
    $this->port = $data['port'];
  }

  /**
  * Gets an FFRPC object by its ID.
  * @return null|ffrpc
  */
  public static function getRpcById(int $id)
  {
    global $ff_sql;

    $res = $ff_sql->fetch("
      SELECT *
      FROM `ff_rpc`
      WHERE `id` = ". $ff_sql->quote($id) ."
    ");

    if(!$res) {
      return null;
    }

    // creating ffrpc object and returning it with found data
    $ffrpc = new self();
    $ffrpc->linkByData($res);
    return $ffrpc;
  }

  /**
  * Gets a random FFRPC obect from its specified type
  * @param string $type
  */
  public static function getRpcByType(string $type)
  {
    global $ff_sql;

    if(!in_array($type, self::TYPES)) {
      return null;
    }

    $rpcs = $ff_sql->query_fetch_all("
      SELECT *
      FROM `ff_rpc`
      WHERE `type` = ". $ff_sql->quote($type) ."
    ", [
      'id' => 'int',
      'port' => 'int'
    ]);

    if(!$rpcs) {
      return null;
    }

    // random index
    $rpc = ff_randomArrayIndex($rpcs);

    // creating ffrpc object and returning it with found data
    $ffrpc = new self();
    $ffrpc->linkByData($rpc);
    return $ffrpc;
  }

  /**
  * Returns complete RPC list
  */
  public static function getRpcList()
  {
    // TODO: Make this with page support in the future.
    global $ff_sql;

		$result = $ff_sql->query_fetch_all("
			SELECT *
			FROM `ff_rpc`
		", [
			'id' => 'int',
			'port' => 'int'
		]);

		return $result;
  }

  /**
	* Creates a new RPC node.
	*
	* @param user $user
	*		The person who's creating the node.
	* @param string $type
	*		The type of node: IE: ff-rpc-notif
	* @param string $endpoint
	*		The endpoint used for connecting to (hostname or IP)
	* @param int $port
	*		The port which we use for communicatioms
	*/
  public static function createRpc(user $user, string $type, string $endpoint, int $port)
  {
    global $ff_sql;

    // Authorization check
    if(!$user->getGroup()->can('mod_ffrpc')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

    // Generating authorization token for the RPC
    $token = cryptography::randomString(128);

    // Inserting record
    $result =  $ff_sql->query("
			INSERT INTO `ff_rpc`
			(`id`, `type`, `auth_token`, `endpoint`, `port`)
			VALUES (
				NULL,
				". $ff_sql->quote($type) .",
				". $ff_sql->quote($token) .",
				". $ff_sql->quote($endpoint) .",
				". $ff_sql->quote($port) ."
			)
		");

    if(!$result) {
      throw new Exception('Failed to create new FFRPC');
    }

    // Making audit for Administrator
    $lastInsertId = $ff_sql->getLastInsertId();
    audits_admin_newffrpcnode::insert($user, $lastInsertId, $type, $endpoint, $port);

    // success
    return ff_return(true, [
      'id' => $lastInsertId,
      'token' => $token
    ]);
  }

  /**
	* Deletes a RPC node
	*
	* @param user $user
	*		The person who be deleting
	* @param int|ffrpc $ffrpc
	*		The ID or FFRPC instance we want to delete.
	*/
  public static function deleteRpc(user $user, $ffrpc)
  {
    global $ff_sql;

    // Authorization check
    if(!$user->getGroup()->can('mod_ffrpc')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

    // getting rpc, depending on the type of $ffrpc
    $rpc = null;
    if(get_class($ffrpc) === 'ffrpc') {
      $rpc = $ffrpc;
    }
    else if(is_int($ffrpc)) {
      $rpc = self::getRpcById($id);
    }
    else {
      throw new Exception('Unexpected type');
    }

    // ensuring rpc is valid.
    if(!$rpc) {
      return ff_return(false, [], 'misc-not-found');
    }

    // Audit log.
    audits_admin_newffrpcnode::insert(
      $user,
      $id,
      $rpc->getType(),
      $rpc->getEndpoint(),
      $rpc->getPort()
    );

    // Deleting the RPC
    $ff_sql->query("
      DELETE FROM `ff_rpc`
      WHERE `id` = ". $ff_sql->quote($rpc->getId()) ."
      LIMIT 1
    ");

    return ff_return(true);
  }

  /**
  * Sends a do request to the FF-RPC service.
  */
  public function sendDo(string $name, $parameter)
  {
    if(!is_array($parameter)) {
      throw new Exception('Unsupported type');
    }

    if(!ff_isAlphanumeric($name)) {
      throw new Exception('Invalid parameter - $name - expected alphanumeric');
    }

    // Generate HTTP Post data
    $data = json_encode($parameter);

    // creating and submitting request
    $ch = curl_init("http://{$this->endpoint}:{$this->port}/$name");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Content-Length: '. strlen($data),
      // NOTE: Authorization used in this way goes against the specification.
      // not important, but should be noted. There is nothing to spec that is
      // accepting of tokens. (that i could be bothered researching)
      'authorization: '. $this->auth_token,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Getting respones
    $result = curl_exec($ch);
    $resultStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $resultContentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if($resultStatusCode !== 200) {
      throw new Exception('FF-RPC Node returned uncessful status '. $resultStatusCode);
    }

    if($resultContentType === 'application/json') {
      return json_decode($result, true);
    }
    else if($resultContentType === 'application/x-www-form-urlencoded') {
      // why do you do this php. We hate you.
      $parsedString = [];
      parse_str($result, $parsedString);
      return $parsedString;
    }
    else {
      // no known content type? assume plain text.
      return $result;
    }
  }

  /**
  * Alias of ffrpc::sendDo.
  */
  public function do(string $name, $parameter)
  {
    return $this->sendDo($name, $parameter);
  }

  /**
  * Returns ID associated with object
  */
  public function getId()
  {
    return $this->id;
  }

  /**
  * Returns type associated with object
  */
  public function getType()
  {
    return $this->type;
  }

  /**
  * Returns authentication token associated with object
  */
  public function getAuthToken()
  {
    return $this->auth_token;
  }

  /**
  * Returns endpoint associated with object
  */
  public function getEndpoint()
  {
    return $this->endpoint;
  }

  /**
  * Returns port associated with object
  */
  public function getPort()
  {
    return $this->port;
  }

}

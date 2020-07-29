<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\ffrpc.php
//
// ======================================


class ffrpc
{
  const TYPE_NOTIF = 'ff-rpc-notification';
  const TYPE_TASK = 'ff-rpc-task';


  const CRLF = "\r\n";
  const CHUNK_SIZE = 1024;
  private $socket = null;

  /**
  * Creates a new FF-Rpc client instance
  *
  * @param string $hostname
  *   The hostname (or ip address) of the server we want to connect to.
  * @param int $port
  *   The port we will use to connect
  * @param string $token
  *   Authentication token that we will use.
  */
  public function __construct(
    string $hostname,
    int $port,
    string $token
  ) {
    $this->socket = fsockopen($hostname, $port);
    if(!$this->socket) {
      throw new Exception("Failed to open socket with {$hostname} on port {$port}");
    }

    if(!$this->sendData(
      self::buildRequest('AUTH', $token)
    )) {
      throw new Exception('Failed to send AUTH request');
    }

    $authResponse = $this->readToBreak();
    $nameEnd = strpos($authResponse, ' ');
    if(!$nameEnd) {
      throw new Exception('Invalid response');
    }

    $name = substr($authResponse, 0, $nameEnd);
    $value = substr($authResponse, $nameEnd + 1);
    if(strtolower($name) !== 'auth') {
      throw new Exception('Unexpected response');
    }

    $response = json_decode($value, true);
    if($response === false) {
      throw new Exception('Unauthorized');
    }
  }

	/**
	* Gets all of the FF-RPC nodes.
	*/
	public static function getRpcList()
	{
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
	public static function createRpcNode(user $user, string $type, string $endpoint, int $port)
	{
		global $ff_sql;
		if(!$user->getGroup()->can('mod_ffrpc')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		$token = cryptography::randomString(128);
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

		if($result) {
			$lastInsertId = $ff_sql->getLastInsertId();
			audits_admin_newffrpcnode::insert($user, $lastInsertId, $type, $endpoint, $port);

			return ff_return(true, [
				'id' => $lastInsertId,
				'token' => $token
			]);
		}
		else {
			throw new Exception("Failed to create FF-RPC node");
		}
	}

	/**
	* Deletes a RPC node
	*
	* @param user $user
	*		The person who be deleting
	* @param int $id
	*		The ID of the RPC node we want to delete.
	*/
	public static function deleteRpcNode(user $user, int $id)
	{
		global $ff_sql;

		if(!$user->getGroup()->can('mod_ffrpc')) {
			return ff_return(false, [], 'misc-permission-denied');
		}

		$rpcNodeInfo = self::getRpcInformation($id);
		if($rpcNodeInfo) {
			audits_admin_newffrpcnode::insert($user, $id, $rpcNodeInfo['type'], $rpcNodeInfo['endpoint'], $rpcNodeInfo['port']);

			$ff_sql->query("
				DELETE FROM `ff_rpc`
				WHERE `id` = ". $ff_sql->quote($id) ."
				LIMIT 1
			");

			return ff_return(true);
		}
		else {
			return ff_return(false, [], 'misc-not-found');
		}
	}

	/**
	* Gets information about a FF-RPC node.
	*
	* @param int $id
	*		The ID of the node we want to fetch information on
	* @return array|false
	*/
	public static function getRpcInformation(int $id)
	{
		global $ff_sql;
		return $ff_sql->query_fetch("
			SELECT *
			FROM `ff_rpc`
			WHERE `id` = ". $ff_sql->quote($id) ."
		", [
			'id' => 'int',
			'port' => 'int'
		]);
	}

  /**
  * Creates a ffrpc object, and auto-fills credentials (port, token, hostname)
  * with a random index whose type matches $type.
  *
  * @param string $type
  *   The type of RPC connection you want to make.
  */
  public static function getRpc(string $type)
  {
    global $ff_sql;

    $RPCs = $ff_sql->query_fetch_all("
      SELECT `id`, `auth_token`, `endpoint`, `port`
      FROM `ff_rpc`
      WHERE `type` = ". $ff_sql->quote($type) ."
      LIMIT 128
    ", [
      'id' => 'int',
      'port' => 'int'
    ]);

    if(!$RPCs) {
      throw new Exception('No RPCs linked with the "'. $type .'" type');
    }

    // Selecting random RPC
    $rndRPC = ff_randomArrayIndex($RPCs);

    // Creating FFRpc instance, and returning it.
    return new ffrpc(
      $rndRPC['endpoint'],
      $rndRPC['port'],
      $rndRPC['auth_token']
    );
  }

  /**
  * Sends a DO request to the remote server.
  *
  * @param string $name
  * @param mixed $parameter
  *   Parameter, can be anything (except objects.)
  */
  public function sendDo(string $name, $parameter)
  {
    if(is_object($parameter)) {
      throw new Exception('Unsupported type');
    }

    if(strpos($name, ' ') !== false) {
      throw new Exception('Name cannot contain spaces.');
    }

    if(!$this->sendData(
      self::buildRequest('DO', $name .' '. json_encode($parameter))
    )) {
      throw new Exception('Failed to send AUTH request');
    }

    $response = $this->readToBreak();
    $commandPos = strpos($response, ' ');
    if(!$commandPos) {
      throw new Exception('unexpectted response');
    }

    $command = substr($response, 0, $commandPos);
    $parameter = substr($response, $commandPos + 1);
    $parameterParsed = json_decode($parameter, true);

    if(strtolower($command) !== 'do') {
      if($command === '-') {
        // Error command
        throw new Exception($parameterParsed);
      }
      throw new Exception('unexpectted response');
    }

    return $parameterParsed;
  }

  public function do(string $name, $parameter)
  {
    return $this->sendDo($name, $parameter);
  }

	/**
	* Closes socket
	*/
	public function close()
	{
		try {
			socket_close($this->socket);
			return true;
		}
		catch (Exception $ex) {
			return false;
		}
	}

  private function sendData($data)
  {
    // Appending "<CRLF><CRLF>" to data.
    $data .= self::CRLF . self::CRLF;

    // Building packets
    $packets = [];
    if(mb_strlen($data) > self::CHUNK_SIZE) {
      $packets = str_split($data, self::CHUNK_SIZE);
    }
    else {
      $packets = [$data];
    }

    foreach ($packets as $packet) {
      if(!fwrite($this->socket, $packet, self::CHUNK_SIZE)) {
        return false;
      }
    }

    return true;
  }

  /**
  * Reads as much content as it can (until server says to stop)
  */
  private function readToBreak()
  {
    $ret = '';

    while($ret .= fgets($this->socket, self::CHUNK_SIZE)) {
      if(strpos($ret, self::CRLF . self::CRLF) !== false) {
        break;
      }
    }

    // Returning the received response, but removing the end of message
    // indication.
    return substr($ret, 0, -4);
  }

  private static function buildRequest($name, string $data = '')
  {
    if(strlen($data) === 0) {
      // "$name"
      return strtoupper($name);
    }
    else if(is_string($data)) {
      // "$name $data"
      return strtoupper($name) .' '. $data;
    }
    throw new Exception('Data parameter must be string');
  }
}

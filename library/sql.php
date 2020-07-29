<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\sql.php
//
// ======================================


/**
* Basically a wrapper for the MySQLI plugin.
*/

class sql
{
	private $instance = null;
	private $hostname = null;
	private $username = null;
	private $password = null;
	private $database = null;
	private $queryCount = 0;

	/**
	* Creates a SQL object, and prepares it for connectiong
	*/
	public function __construct(
		string $username,
		string $password,
		string $hostname,
		string $database
	) {
		mysqli_report(MYSQLI_REPORT_STRICT);

		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;
		$this->database = $database;
	}

	public function connect()
	{
		if($this->instance === null) {
			try {
				$this->instance = mysqli_connect(
					$this->hostname,
					$this->username,
					$this->password,
					$this->database
				);
			}
			catch(Exception $ex) {
				handlers_exception::handle($ex);
				$this->instance = null;
			}
		}

		return $this->instance !== null;
	}

	/**
	* Prepares a MySQL query
	*
	* @param string $query
	*		The query to be prepared.
	*/
	public function prepare($query)
	{
		$this->connect();

		if(!$this->instance) {
			throw new Exception('DB connection failed');
		}

		return $this->instance->prepare($query);
	}

	/**
	* Runs a MySQL query
	*
	* @param string $query
	*		Query to be run.
	*/
	public function query($query)
	{
		$this->connect();

		if(!$this->instance) {
			throw new Exception('DB connection failed');
		}

		$res = $this->instance->query($query);
		if(!$res) {
			throw new Exception('Error with query.'. base64_encode($query));
		}

		$this->queryCount++;

		return $res;
	}

	/**
	* Runs a query, and returns the first result.
	*
	* @param string $query
	*		The query whose results we want to return.
	* @param array $types
	*		Array of types matching columns. So Key is the column name, and value is
	*		the type. Makes use of http://php.net/manual/en/function.settype.php
	*		NOTE: It's recommended to leave large strings with no type casting
	*/
	public function query_fetch(string $query, array $types = [])
	{
		if(!$this->instance) {
			throw new Exception('DB connection failed');
		}

		if($queryResults = $this->query($query)) {
			if($queryResults->num_rows === 0) {
				return false;
			}

			$assoc = $queryResults->fetch_assoc();

			// Type casting.
			foreach($assoc as $assocName => $assocValue) {
				if(isset($types[$assocName])) {
					if(!settype($assoc[$assocName], $types[$assocName])) {
						throw new Exception('Failed to cast '. gettype($assoc[$assocName]) .' to '. $types[$assocName]);
					}
				}
			}

			return $assoc;
		}
		else {
			return false;
		}
	}

	/**
	* Refer to query_fetch.
	*/
	public function fetch(string $query, array $types = [])
	{
		return $this->query_fetch($query, $types);
	}

	/**
	* Runs a query, but returns all results.
	*
	* @param string $query
	*		The query whose results we want to return.
	* @param array $types
	*		Array of types matching columns. So Key is the column name, and value is
	*		the type. Makes use of http://php.net/manual/en/function.settype.php
	*		NOTE: It's recommended to leave large strings with no type casting
	* @return array|bool
	*		On success we return an array with all the content, and on failure we
	*		return false.
	*/
	public function query_fetch_all(string $query, array $types = [])
	{
		if(!$this->instance) {
			throw new Exception('DB connection failed');
		}

		if($queryResults = $this->query($query)) {
			if($queryResults->num_rows === 0) {
				return false;
			}

			$rows = [];
			$index = 0;
			while($row = $queryResults->fetch_assoc()) {

				// Type casting.
				foreach($row as $assocName => $assocValue) {
					if(isset($types[$assocName])) {
						if(!settype($row[$assocName], $types[$assocName])) {
							throw new Exception('Failed to cast '. gettype($row[$assocName]) .' to '. $types[$assocName]);
						}
					}
				}

				$rows[$index] = $row;
				$rows[$index]['__index__'] = $index;
				$index++;
			}

			return $rows;
		}
		else {
			return false;
		}
	}

	public function fetch_all(string $query, array $types = [])
	{
		return $this->query_fetch_all($query, $types);
	}

	/**
	* Gets the most recent primary key inserted.
	*/
	public function getLastInsertId($table = null)
	{
		if(!$this->instance) {
			throw new Exception('DB connection failed');
		}

		$query = ($table === null
			? "SELECT LAST_INSERT_ID() as id"
			: "SELECT LAST_INSERT_ID() as id from `". $this->escape($table) ."`"
		);

		$res = $this->query_fetch($query, ['id' => 'int']);

		if($res) {
			return $res['id'];
		}
		throw new Exception('No last insert id found!');
	}

	/**
	* Alias for getLastInsertId
	*/
	public function lastInsertId($table = null)
	{
		return $this->getLastInsertId($table);
	}

	/**
	* Quotes, and escapes a variable.
	*
	* @param mixed $var
	*		Variable to be escaped
	*/
	public function quote($var)
	{
		$this->connect();

		if(
			is_null($var) ||
			is_resource($var) ||
			is_object($var) ||
			is_array($var)
		) {
			throw new Exception(gettype($var) .' is a disallowed type for $var');
		}

		if(!$this->instance) {
			throw new Exception('DB connection failed');
		}

		if(is_float($var) || is_double($var)) {
			return floatval($var);
		}
		else if(is_int($var)) {
			return intval($var);
		}
		else if(is_bool($var)) {
			return ($var === true ? '1' : '0');
		}
		else if(is_string($var)) {
			$escaped = $this->instance->real_escape_string($var);

			if($escaped === false) {
				throw new Exception('Failed to escape string');
			}

			return "\"{$escaped}\"";
		}
		else {
			throw new Exception('Unknown variable type');
		}
	}

	public function escape(string $string)
	{
		return $this->instance->real_escape_string($string);
	}

	/**
	* Wrapper for the MySQLI function, 'ping'
	*/
	public function ping()
	{
		try {
			if(!$this->instance) {
				return false;
			}

			$this->connect();

			return $this->instance->ping();
		}
		catch (mysqli_sql_exception $ex) {
			return false;
		}
	}

	/**
	* Closes the MySQLI instance
	*/
	public function close()
	{
		if(!$this->instance) {
			return true;
		}
		return $this->instance->close();
	}

	/**
	* Escapes a wildcard. This is good for LIKE conditions.
	*
	* @param string $str
	*		String to be escaped.
	*/
	public function escapeWildcard(string $str)
	{
		return str_replace(
			['%', '_', '*'],
			['\\%', '\\_', '\\*'],
			$str
		);
	}

	/**
	* Gets the amount of queries executed
	* @return int
	*/
	public function queryCount()
	{
		return $this->queryCount;
	}
}

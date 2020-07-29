<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\group.php
//
// ======================================


class group
{
  public static $groupCache = [];

  /**
  * ID of this group instance
  * @var int
  */
  private $id = 0;

  private $name = '';
  private $color = '';

  /**
  * Key/Value of user can permissions
  * @var array
  */
  private $canPermissions = [];

  /**
  * Links the current goup object to an id.
  *
  * @param int $id
  *   Id we want to link.
  */
  public function linkById(int $id)
  {
    global $ff_sql;

    // I generally dislike using wildcards, when selecting columns, but it's okay...
    $res = $ff_sql->query_fetch("
      SELECT *
      FROM `groups`
      WHERE
        `id` = ". $ff_sql->quote($id) ."
    ", ['id' => 'int']);

    if(!$res) {
      return false;
    }

    $this->name = $res['name'];
    $this->id = $res['id'];
    $this->color = $res['color'];

    foreach($res as $key => $value) {
      if(substr($key, 0, 4) === 'can_') {
        if($perm = substr($key, 4)) {
          settype($value, 'bool');
          $this->canPermissions[strtolower($perm)] = $value;
        }
      }
    }

    return true;
  }

  /**
  * Gets group object by id
  *
  * @param int $id
  *   Group you want to get.
  */
  public static function getGroupById(int $id)
  {
    if(isset(self::$groupCache[$id])) {
      return self::$groupCache[$id];
    }

    $group = new group();
    if($group->linkById($id)) {
      return self::$groupCache[$id] = $group;
    }
    return false;
  }

	/**
	* Gets a list of all user groups
	* @return array|false on success, returns array of group objects, otherwise false.
	*/
	public static function getAllGroups()
	{
		global $ff_sql;

		$groupRows = $ff_sql->query_fetch_all("
			SELECT
				*
			FROM
				`groups`
		");

		$return = [];
		foreach ($groupRows as $group) {
			$return[] = self::getGroupById($group['id']);
		}

		return (count($return) > 0
			? $return
			: false
		);
	}

  /**
  * Checks a user can* permission
  *
  * @param string $permission
  *   Permission we want to check. Do not prefix with can_. If you want to use
  *   a wildcard, you can do mod_*, and it will return true if any of the matched
  *   can values are true, and otherwise false.
  * @return bool
  */
  public function can(string $permission)
  {
    $permission = strtolower($permission);

    if(($wcPos = strpos($permission, '*')) !== false) {
      $wildcard = substr($permission, 0, $wcPos);
      foreach ($this->canPermissions as $permissionName => $permissionValue) {
        if(substr($permissionName, 0, $wcPos) === $wildcard) {
          if($permissionValue) {
            return true;
          }
        }
      }
      return false;
    }
    else {
      if(isset($this->canPermissions[$permission])) {
        return $this->canPermissions[$permission];
      }
    }

    throw new Exception('Permission not found');
  }

  public function getName()
  {
    return $this->name;
  }

  public function getColor()
  {
    return $this->color;
  }

  public function getId()
  {
    return $this->id;
  }
}

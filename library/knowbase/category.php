<?php

// ======================================
//
// Fried Fame VPN Engine
// Created by Eithan
//
// https://github.com/eithan1231/fried-fame
// https://eithan.me/
//
// \library\knowbase\category.php
//
// ======================================


class knowbase_category
{
  private $id = 0;
  private $name = '';
  private $description = '';

  /**
  * Creates a new category
  * @param string $name Name of the category
  * @param string $description Description of the category.
  */
  public static function createCategory(string $name, string $description)
  {
    global $ff_sql;

    if(strlen($name) > 64 || strlen($name) < 1) {
      return ff_return(false, 'misc-invalid-name');
    }

    if(strlen($description) < 1) {
      return ff_return(false, 'misc-invalid-description');
    }

    $ff_sql->query("
      INSERT INTO `knowbase_category`
      (id, description, name, name_lower)
      VALUES (
        NULL,
        ". $ff_sql->quote($description) .",
        ". $ff_sql->quote($name) .",
        ". $ff_sql->quote(strtolower($name)) .",
      )
    ");

    $id = $ff_sql->getLastInsertId();

    return ff_return(true, [
      'id' => $id
    ], 'misc-success');
  }

  public function linkById(int $id)
  {
    global $ff_sql;

    $res = $ff_sql->fetch("
      SELECT `id`, `description`, `name`
      FROM `knowbase_category`
      WHERE `id` = ". $ff_sql->quote($id) ."
    ", ['id' => 'int']);

    if(!$res) {
      return false;
    }

    $this->linkByData($res);

    return true;
  }

  private function linkByData($sqlres)
  {
    $this->id = $sqlres['id'];
    $this->description = $sqlres['description'];
    $this->name = $sqlres['name'];
  }

  public static function getCategoryByNane(string $name)
  {
    global $ff_sql;

    $res = $ff_sql->fetch("
      SELECT id
      FROM knowbase_category
      WHERE name = ". $ff_sql->quote($name) ."
      LIMIT 1
    ");

    return (!!$res
      ? self::getCategoryById($res['id'])
      : null
    );
  }

  public static function getCategoryById(int $id)
  {
    $category = new self();
    if($category->linkById($id)) {
      return $category;
    }

    return null;
  }

  public function setDescription(user $user, string $description)
  {
    throw new Exception('Not Implemented');
  }

  public function setName(user $user, string $name)
  {
    throw new Exception('Not Implemented');
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function getPosts(int $offset = 0, int $count = 20)
  {

  }
}

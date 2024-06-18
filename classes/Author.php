<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

class Author extends ObjectModel
{
  public $id_author;
  public $first_name;
  public $last_name;
  public $biography;

  public static $definition = array(
    'table' => 'author',
    'primary' => 'id_author',
    'fields' => array(
      'first_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
      'last_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
      'biography' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true),
    ),
  );
}

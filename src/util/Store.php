<?php

namespace Hero\Util;

class Store {

  private static $instance;
  private $data = [];

  public static function getInstance(){

    if (null === static::$instance)
      static::$instance = new static();

    return static::$instance;
  }

  private function __construct() {

  }

  public function _set($key, $value) {
    $this->data[$key] = $value;
  }

  public function _get($key) {
    return $this->data[$key];
  }

  public function _push($key, $value){
    array_push($this->data[$key], $value);
  }

  public static function set($key, $value) {
    self::getInstance()->_set($key, $value);
  }

  public static function get($key) {
    return self::getInstance()->_get($key);
  }

  public static function push($key, $value){
    self::getInstance()->_push($key, $value);
  }

}

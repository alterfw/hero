<?php

class Store {

  public static $instance;
  private $data = [];

  private function __construct() {
    //
  }

  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new Store();
    }

    return self::$instance;
  }

  public function get($key){
    return $this->data[$key];
  }

  public function set($key, $value){
    $this->data[$key] = $value;
  }

  public function reset(){
    $this->data = [];
  }

}

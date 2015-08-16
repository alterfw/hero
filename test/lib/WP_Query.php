<?php

class WP_Query {

  public function __construct($args){
    Store::getInstance()->set('args', $args);
  }

  public function have_posts() {
    return false;
  }

}

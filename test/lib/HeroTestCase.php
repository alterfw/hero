<?php

class HeroTestCase extends PHPUnit_Framework_TestCase {

  function setUP() {

    parent::setUP();
    $hero = new Hero();
    $this->app = $hero->get();

  }

}

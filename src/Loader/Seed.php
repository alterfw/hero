<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 9/7/15
 * Time: 9:18 PM
 */

namespace Hero\Loader;
use Alter\Common\Loader;


class Seed extends Loader {

  protected $handlers = [];
  protected $instances = [];

  function __construct($folder) {

    $self = $this;
    parent::__construct(null, [$folder]);

    parent::handle('\Hero\Core\Seeder', function($_app, $instance) use ($self) {
        array_push($self->instances, $instance);
    });

    $this->load();

  }

  public function getInstances() {
    return $this->instances;
  }

}

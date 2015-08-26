<?php

use Hero\Core\Model;

class User extends Model {

  public $fields = [

    'title' => true,

    'name' => [
      'type' => 'text',
      'label' => 'Name'
    ]

  ];

}

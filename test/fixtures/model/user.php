<?php

class User extends AppModel {

  public $fields = [

    'title' => true,

    'name' => [
      'type' => 'text',
      'label' => 'Name'
    ]

  ];

}

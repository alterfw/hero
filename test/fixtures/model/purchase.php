<?php

use Hero\Core\Model;

class Purchase extends Model {

  public $belongs_to = 'user';
  public $has_many = 'product';

  public $fields = [

    'date' => [
      'type' => 'date',
      'label' => 'date'
    ]

  ];

}

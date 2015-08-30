<?php

use Hero\Core\Model;

class Product extends Model {

  public $fields = [

    'title' => true,
    'thumbnail' => true,

    'price' => [
      'type' => 'number',
      'label' => 'Price'
    ]

  ];

}

<?php

class Product extends AppModel {

  public $fields = [

    'title' => true,
    'thumbnail' => true,

    'price' => [
      'type' => 'number',
      'label' => 'Price'
    ]

  ];

}

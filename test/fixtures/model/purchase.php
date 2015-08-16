<?php

class Purchase extends AppModel {

  public $belongs_to = 'user';
  public $has_many = 'product';

  public $fields = [

    'date' => [
      'type' => 'date',
      'label' => 'date'
    ]

  ];

}

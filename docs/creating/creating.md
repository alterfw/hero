---
title: Creating models
---

To create Hero models you need to create a directory named `model` in your theme's root directory.
Inside the `model` folder you can create your model classes extending `Hero\Core\Model`.

**Note:** Hero needs that the file name match the class name to work properly.

## Generating Models

To generate a model you can run the `create:model` command in your terminal:

    ./vendor/bin/hero create:model car
    
The above command will create this file in the `/model/car.php` location:

```php
<?php

use Hero\Core\Model;

class Car extends Model {

  public $fields = [
    'title' => true
  ];

}
```

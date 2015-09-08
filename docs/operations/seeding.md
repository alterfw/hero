---
title: Seeding
---

Hero includes a simple method for seeding your database using Seed classes. All seed classes must be stored at `/seed` directory.
Seed classes may have any name you wish, but probably should follow some sensible convention, such as `CarSeeder`, etc.
All seed classes must implement `Hero\Core\Seeder` interface, the `run()` method will be called during the seed process.

## Writing Seeders

To generate a seed you can run the `create:seed` command in your terminal:

    ./vendor/bin/hero create:seed car
    
The above command will create this file in the `/seed/CarSeeder.php` location:

```php
<?php

use Hero\Core\Seeder;

class CarSeeder implements Seeder {

  public function run() {

    $item = new Car();
    $item->save();

  }

}
```

## Running Seeders

Once you have written your seeder classes, you may use the `db:seed` command in your terminal to seed your database. The `db:seed` command will seed all classes in the `/seed` directory.

    ./vendor/bin/hero db:seed
    
---
title: Retrieving data
---

Hero offers a simple and powerful API to retrieve data. Let's supose that you have this `Car` model:

```php
use Hero\Core\Model;
class Car extends Model {

  public $fields = [

    'title' => true
    'thumbnail' => true,


    'gallery' => [
      'type' => 'image',
      'label' => 'Gallery',
      'multiple' => true
    ],

    'year' => [
      'type' => 'int',
      'label' => 'Year'
    ]

  ];

}
```

You can just call:

```php
$cars = Car::all(); // Array of Cars
```

And access the model fields:

```php
foreach(Car:all() as $car){

  echo $car->title;
  echo $car->year;

  foreach($car->gallery as $photo){
    echo $photo->thumbnail;
  }

}
```

## Model methods

### all()

Retrieves an array with all items

### find($options)

* $options: Array of [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query) arguments

Retrieves an array of items

### findBySlug($slug)

* $slug: String

Retrives an item matching the slug passed as argument

### findByTaxonomy($taxonomy, $term, $limit)

* $taxonomy: String
* $term: String
* $limit: Integer (optional)

Retrieves an array of items matching the passed taxonomy and term

### paginate($limit, $offset)

* $limit: Integer (optional)
* $offset: Integer (optional)

Returns an paginated array of items

```php
$cars = Car::paginate();
```

### paginateWithOptions($options, $limit, $offset)

* $options: Array of [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query) arguments
* $limit: Integer (optional)
* $offset: Integer (optional)

Returns an paginated array of items

## Automagic find() methods

Hero allows you to use automagic *find()* methods according to you custom fields.

```php
$cars = Car::findByYear(2015);
```

This method will search all the cars that have the custom fields `year` with the *2015* value.

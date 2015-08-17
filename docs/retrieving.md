# Retrieving data

Hero offers a simple and powerful API to retrieve data. Let's supose that you have this `Car` model:

```php
<?php
// car.php
class Car extends AppModel {

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

```
<?php
$cars = $app->car->find(); // Array of Cars
```

And access the model fields:

```
<?php
foreach($app->car->find() as $car){

  echo $car->title;
  echo $car->year;

  foreach($car->gallery as $photo){
    echo $photo->thumbnail;
  }

}
```

## Model methods

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

Returns an instance of the model class so you can chain any of the *find* methods:

```php
$cars = $app->car->paginate()->find();
```

### pagination($type)

* $type: Boolean (optional)

Returns an array of pages to show pagination links in the template, if `$type` is false only return the previous and next pages.

## Automagic find() methods

Hero allows you to use automagic *find()* methods according to you custom fields.

```
$cars = $app->car->findByYear(2015)
```

This method will search all the cars that have the custom fields `year` with the *2015* value.

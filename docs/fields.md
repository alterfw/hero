# Setting model fields

Hero has built in supprt to WordPress default fields such as title and thumbnail, you just need to set to true using the `$fields` attribute.

```php
<?php
// car.php
class Car extends AppModel {

  public $fields = [

    'title' => true,
    'thumbnail' => true

  ];

}
```

## Custom fields

Hero allows you to use custom fields in your model:

```php
public $fields = [
  'name' => [
    'type' => 'text',
    'label' => 'Name' // Name showed on WordPress admin
  ]
];
```

The built in field types on Hero are:

**text:**
A simple text field

**long_text:**
A textarea that allows more text than `text`

**int:**
A field of type `number`.

**boolean:**
A checkbox field

**image:**
An image field, you can enable multiple images via `multiple` parameter:

```php
'gallery' => [
  'type' => 'image',
  'label' => 'Gallery',
  'multiple' => true // Enables multiple images
]
```

**list:** A `select` field, you can pass the options throught the `options` parameter:
```php
'fruits' => [
  'type' => 'list',
  'label' => 'Fruits',
  'options' => ['Apple', 'Orange', 'Banana']
]
```

Using the `list` field type you can also pass a function name or an *Closure* in the `options` parameter:

```php
function get_fruits(){
  return ['Apple', 'Orange', 'Banana']
}
```

```php
'fruits' => [
  'type' => 'list',
  'label' => 'Fruits',
  'options' => 'get_fruits'
]
```

**file:**
A `file` field

### Meta Box fields

Hero uses Meta Box to create the custom fields, you can also use any of the [Meta Box fields](https://metabox.io/docs/define-fields/).

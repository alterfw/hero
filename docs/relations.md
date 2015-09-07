---
title: Relationships
---

In Hero you can specify relations between models such as `belongs_to` and `has_many`.


Creates the `Car` model:
```php
use Hero\Core\Model;
class Car extends Model {

}
```

Attachs a field of type `checkbox_list` with a list of cars into the `User` model:
```php
use Hero\Core\Model;
class User extends Model {

  public $has_many = 'car';

}
```

Attachs a field of type `list` with a list of users into the `Apartment` model:
```php
use Hero\Core\Model;
class Apartment extends Model {

  public $belongs_to = 'user';

}
```

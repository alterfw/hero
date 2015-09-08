---
title: Taxonomies
---

You can also register taxonomies using Hero:

```php
use use Hero\Core\Taxonomy;

Taxonomy::register('city', 'City', 'Cities');
Taxonomy::register('province', 'Province', 'Provinces');
```

## Linking to models

And you can easily link taxonomies to models:

```php
use use Hero\Core\Model;
class CarModel extends Model {

  public $taxonomies = ['car_type', 'car_color'];

}
```

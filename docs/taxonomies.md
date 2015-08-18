# Taxonomies

You can also register taxonomies using Hero:

```
<?php

$app->registerTaxonomy('city', 'City', 'Cities');
$app->registerTaxonomy('province', 'Province', 'Provinces');
$app->registerTaxonomies();
```

## Linking to models

And you can easily link taxonomies to models:

```
<?php
class CarModel extends AppModel{

  public $taxonomies = ['car_type', 'car_color'];

}
```

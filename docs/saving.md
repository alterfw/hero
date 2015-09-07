---
title: Saving data
---

With Hero you can also save data.

Just intantiate your model:

```php
$car = new Car();
```

Add some data:

```php
$car->color = "blue";
$car->title = "Cobra";
```

And save!

```php
$car->save();
```

With the object saved, you can now access the ID:

```php
echo $car->id;
```
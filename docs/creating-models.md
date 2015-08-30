# Creating models

To create Hero models you need to create a directory named `model` in your theme's root directory.
Inside the `model` folder you can create your model classes extending `Hero\Core\Model`.

**Note:** Hero needs that the file name match the class name to work properly.

*E.g.*

```php
<?php
// car.php
use Hero\Core\Model;
class Car extends Model {

}
```

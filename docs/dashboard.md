# Dashboard customization

Using Hero you can customize the menu labels on WordPress dashboard:

```
<?php
class Car extends AppModel {

  public $singular = "Car";
  public $plural = "Cars";

}
```

You can also customize the icon that will appear on WordPress dashboard:

```
<?php
class Car extends AppModel {

  public $icon = "dashicons-admin-home";

}
```

Hero uses the [WordPress Dashicons](https://developer.wordpress.org/resource/dashicons/#images-alt).

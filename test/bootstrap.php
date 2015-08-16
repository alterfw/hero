<?php

require __DIR__."/lib/wp_functions.php";
require __DIR__."/../vendor/autoload.php";
require __DIR__."/lib/HeroTestCase.php";
require __DIR__."/lib/Store.php";
require __DIR__."/lib/WP_Query.php";

// Define constants for testing purposes
define('ABSPATH', __DIR__);
define('ALTER', __DIR__. '/../');
define('ALTER_VENDOR', __DIR__. '/../vendor/alterfw');
define('APPLICATION_PATH', __DIR__. '/fixtures');
define('RWMB_URL', '');

$hero = new Hero();
$app = $hero->get();

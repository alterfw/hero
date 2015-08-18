Hero
=========

[![Build Status](https://travis-ci.org/alterfw/hero.svg?branch=master)](https://travis-ci.org/alterfw/hero)

Hero is the Alter's main module, responsible for all the interaction with post types and taxonomies throught models.

## Instalation

    composer require alterfw/hero

## Getting started

Require the composer autoload file into your `functions.php`:

```php
require "vendor/autoload.php";
```

And Hero will be ready:

```php
$hero = new Hero();
$app = $hero->get();
$books = $app->books->find();
```

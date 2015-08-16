Hero
=========

[![Build Status](https://travis-ci.org/alterfw/hero.svg?branch=master)](https://travis-ci.org/alterfw/hero)

Hero is the Alter's main module, responsible for all the interaction with post types and taxonomies throught models.

## Instalation

    composer require alterfw/hero

## Usage

Using Hero out of the box.

```php
<?php

$hero = new Hero();
$app = $hero->get();

$books = $app->books->find();

```


## Documentation

Please read the documentation on the [Alter Documentation Website](http://alter-framework.readthedocs.org/en/latest/models.html).

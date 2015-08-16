<?php

class ModelSpec extends HeroTestCase {

  function test_if_hero_has_loaded() {
    $this->assertTrue(class_exists('Hero'), 'Verify if Hero main class has been loaded');
    $this->assertInstanceOf('\Hero\Core\App', $this->app, 'Checks if Hero is loaded in the Test case');
  }

  function test_if_default_models_has_been_loaded(){
    $this->assertObjectHasAttribute('post', $this->app, 'Verify if default post model has been loaded');
    $this->assertObjectHasAttribute('page', $this->app, 'Verify if default page model has been loaded');
  }

  function test_if_custom_models_has_been_loaded() {
    $this->assertObjectHasAttribute('user', $this->app, 'Verify if user model has been loaded');
    $this->assertObjectHasAttribute('purchase', $this->app, 'Verify if purchase model has been loaded');
    $this->assertObjectHasAttribute('product', $this->app, 'Verify if purchase model has been loaded');
  }

  function test_if_user_model_has_custom_fields() {
    $fields = $this->app->user->getModel()->getFields();
    $this->assertCount(2, $fields);
    $this->assertArrayHasKey('title', $fields);
    $this->assertArrayHasKey('name', $fields);
  }

  function test_purchase_relation_with_user() {

    $fields = $this->app->purchase->getModel()->getFields();
    $this->assertCount(3, $fields);
    $this->assertArrayHasKey('date', $fields);
    $this->assertArrayHasKey('user', $fields);
    $this->assertEquals($fields['user']['type'], 'list');
    $this->assertInstanceOf('Closure', $fields['user']['options']);

  }

  function test_purchase_relation_with_product() {

    $fields = $this->app->purchase->getModel()->getFields();
    $this->assertArrayHasKey('date', $fields);
    $this->assertArrayHasKey('product', $fields);
    $this->assertEquals($fields['product']['type'], 'checkbox_list');
    $this->assertInstanceOf('Closure', $fields['product']['options']);

  }

  function test_if_models_return_an_array() {
    $this->assertTrue(is_array($this->app->user->find()), 'Check if the model->find() method returns an array');
    $this->assertTrue((count($this->app->user->find()) == 0), 'Check if the model->find() method returns 0 items');
  }

  function test_if_wp_query_is_done_right() {

    $this->app->user->find();
    $args = Store::getInstance()->get('args');

    $this->assertEquals($args['post_type'], 'user', 'Check if search for the right post type');
    $this->assertEquals($args['post_status'], 'publish', 'Check if search for the right post status');
    $this->assertEquals($args['paged'], 1, 'Check if search for the right post page');
    $this->assertEquals($args['limit'], -1, 'Check if search without limit');

  }

}

<?php

class ModelSpec extends PHPUnit_Framework_TestCase {

  function test_if_custom_models_has_been_loaded() {
    $this->assertTrue(class_exists('User'), 'Asserts if the User model has been loaded');
    $this->assertTrue(class_exists('Purchase'), 'Asserts if the Purchase model has been loaded');
    $this->assertTrue(class_exists('Product'), 'Asserts if the Product model has been loaded');
  }

  function test_if_user_model_has_custom_fields() {
    $fields = User::getFields();
    $this->assertCount(2, $fields);
    $this->assertArrayHasKey('title', $fields);
    $this->assertArrayHasKey('name', $fields);
  }

  function test_purchase_relation_with_user() {

    $fields = Purchase::getFields();
    $this->assertCount(3, $fields);
    $this->assertArrayHasKey('date', $fields);
    $this->assertArrayHasKey('user', $fields);
    $this->assertEquals($fields['user']['type'], 'list');
    $this->assertInstanceOf('Closure', $fields['user']['options']);

  }

  function test_purchase_relation_with_product() {

    $fields = Purchase::getFields();
    $this->assertArrayHasKey('date', $fields);
    $this->assertArrayHasKey('product', $fields);
    $this->assertEquals($fields['product']['type'], 'checkbox_list');
    $this->assertInstanceOf('Closure', $fields['product']['options']);

  }

  function test_if_models_return_an_array() {
    $this->assertTrue(is_array(User::find()), 'Check if the model->find() method returns an array');
    $this->assertTrue((count(User::find()) == 0), 'Check if the model->find() method returns 0 items');
  }

  function test_if_wp_query_is_done_right() {

    User::find();
    $args = Store::getInstance()->get('args');

    $this->assertEquals($args['post_type'], 'user', 'Check if search for the right post type');
    $this->assertEquals($args['post_status'], 'publish', 'Check if search for the right post status');
    $this->assertEquals($args['paged'], 1, 'Check if search for the right post page');
    $this->assertEquals($args['limit'], -1, 'Check if search without limit');

  }

}

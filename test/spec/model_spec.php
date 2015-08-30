<?php

class ModelSpec extends PHPUnit_Framework_TestCase {

  function setUP() {
    \Hero\Util\Register::post_types();
    parent::setUP();
  }

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

  function test_model_pagination() {
    $this->assertTrue(is_array(User::paginate()), 'Check if the model->paginate() method returns an array');
    $this->assertTrue(is_array(User::paginateWithOptions([])), 'Check if the model->paginateWithOptions() method returns an array');
  }

  function test_if_models_has_automagic_relation_methods() {
    $this->assertTrue(is_array(Purchase::findByUser(1)), 'Check if automagic belongs_to method works');
    $this->assertTrue(is_array(Purchase::findByProduct(1)), 'Check if automagic has_many method works');
  }

  function test_if_models_has_automagic_field_methods() {
    $this->assertTrue(is_array(Purchase::findByDate(1)), 'Check if automagic field method works on Purchase');
    $this->assertTrue(is_array(Product::findByPrice(1)), 'Check if automagic field method works on Product');
  }

  function test_if_an_invalid_automagic_method_throws_exception() {
    $this->setExpectedException('Exception', "Trying to access a method that doesn't exists");
    $this->assertTrue(is_array(Purchase::findByPrice(1)), 'Check if automagic field method works on Purchase');
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

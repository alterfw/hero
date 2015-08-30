<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/27/15
 * Time: 9:00 PM
 */

namespace Hero\Core;

use Hero\Util\Store;

class Taxonomy {

  private static $instance;
  private $taxonomies = [];

  private function __construct(){
    add_action( 'init', array($this, 'register_tax'), 0 );
  }

  private static function getInstance() {
    if(empty(self::$instance)) self::$instance = new Taxonomy();
    return self::$instance;
  }

  public function register_tax() {
    foreach($this->taxonomies as $tax) $this->findPostType($tax);
  }

  private function findPostType($tax) {

    $models = Store::get('models');
    foreach($models as $serialized) {
      $model = unserialize($serialized);
      if(in_array($tax->slug, $model['taxonomies'])) $this->do_register($tax, $model['post_type']);
    }

  }

  private function do_register($tax, $post_type) {

    $labels = array(
      'name'                => __($tax->plural),
      'singular_name'       => __($tax->singular),
      'menu_name'           => __($tax->plural),
      'parent_item_colon'   => __( 'Parent Item:'),
      'all_items'           => __($tax->plural),
      'view_item'           => __( 'View') . ' '. __($tax->plural),
      'add_new_item'        => __( 'Add' ) . ' '. __($tax->singular),
      'add_new'             => __( 'Add') .' '. __($tax->singular),
      'edit_item'           => __( 'Edit') . ' '. __($tax->singular),
      'update_item'         => __( 'Update'). ' '. __($tax->singular),
      'search_items'        => __( 'Search'). ' '. __($tax->singular),
      'not_found'           => __( 'Not found'),
      'not_found_in_trash'  => __( 'Not found in Trash'),
    );
    $args = array(
      'labels'                     => $labels,
      'hierarchical'               => $tax->hierarchical,
      'public'                     => true,
      'show_ui'                    => true,
      'show_admin_column'          => true,
      'show_in_nav_menus'          => true,
      'show_tagcloud'              => true,
    );

    register_taxonomy( $tax->slug, $post_type, $args );

  }

  private function add($slug, $singular, $plural, $hierarchical) {
    $data = [
      'slug' => $slug,
      'singular' => $singular,
      'plural' => $plural,
      'hierarchical' => $hierarchical
    ];
    $data = (object) $data;
    array_push($this->taxonomies, $data);
  }

  public static function register($slug, $singular, $plural = '', $hierarchical = true) {
    if($plural == '') $plural = $singular.'s';
    self::getInstance()->add($slug, $singular, $plural, $hierarchical);
  }


}
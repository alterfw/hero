<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/24/15
 * Time: 11:40 PM
 */

namespace Hero\Util;


class Register {

  private $instance;
  private $meta;
  private $models = [];

  private function getInstance() {
    if(empty(self::$instance)) self::$instance = new Register();
    return self::$instance;
  }

  private function getMeta() {
    if(empty(self::$meta)) self::$meta = new RegisterMetabox();
    return self::$meta;
  }

  public static function models() {
    add_action( 'init', array('\Hero\Util\Register', 'post_types'), 0 );
    add_filter( 'rwmb_meta_boxes', array('\Hero\Util\Register', 'meta_boxes') );
    foreach(get_declared_classes() as $class){
      if($class instanceof Hero\Core\Model) self::model($class);
    }
  }

  private function addModel($model) {
    array_push($this->models, $model);
  }

  private static function model($model) {
    self::getInstance()->addModel($model);
    self::getMeta()->add(self::getType($model), $model->getFields());
  }

  private static function getType($model) {
    return strtolower(get_class($model));
  }

  private static function post_type($model) {

    $labels = $model->getLabels();

    $singular = (empty($labels[0])) ? ucfirst(get_class($model)) : $labels[0];
    $plural = (empty($labels[1])) ? ucfirst(get_class($model).'s') : $labels[1];
    $icon = (empty($model->getIcon())) ? 'dashicons-admin-post' : $model->getIcon();
    $tax = $model->getTaxonomies();
    $fields = $model->getFields();

    $supports = array();
    $wp_fields = array(
      'title',
      'editor',
      'thumbnail',
      'excerpt',
      'comments',
      'revisions',
      'trackbacks',
      'page-attributes'
    );

    if(!empty($fields))
      foreach($fields as $key => $value){
        if(in_array($key, $wp_fields) && $value){
          array_push($supports, $key);
        }
      }
    if(count($supports) == 0) $supports = false;

    $capability_type = 'page';
    $capabilities = array();

    $labels = array(
      'name'                => __($plural),
      'singular_name'       => __($singular),
      'menu_name'           => __($plural),
      'parent_item_colon'   => __( 'Parent Item:'),
      'all_items'           => __($plural),
      'view_item'           => __( 'View') . ' '. __($plural),
      'add_new_item'        => __( 'Add' ) . ' '. __($singular),
      'add_new'             => __( 'Add') .' '. __($singular),
      'edit_item'           => __( 'Edit') . ' '. __($singular),
      'update_item'         => __( 'Update'). ' '. __($singular),
      'search_items'        => __( 'Search'). ' '. __($singular),
      'not_found'           => __( 'Not found'),
      'not_found_in_trash'  => __( 'Not found in Trash'),
    );

    $args = array(
      'label'               => __( get_class($model) , 'text_domain' ),
      'description'         => __( '', 'text_domain' ),
      'labels'              => $labels,
      'supports'            => $supports,
      'taxonomies'          => $tax,
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'menu_position'       => 5,
      'menu_icon'           => $icon,
      'can_export'          => true,
      'has_archive'         => true,
      'exclude_from_search' => false,
      'publicly_queryable'  => true,
      'capability_type'     => $capability_type,
      'capabilities'        => $capabilities,
      'rewrite'             => array('slug' => 'hero_'.self::getType($model), 'with_front' => true)
    );

    if(self::getType($model) != 'page' && self::getType($model) != 'post'){
      register_post_type( self::getType($model) , $args );
    }

  }

  public static function post_types() {
    foreach(self::getInstance()->models as $model) self::post_type($model);
  }

  public static function meta_boxes() {
    self::getMeta()->doRegister();
  }

}
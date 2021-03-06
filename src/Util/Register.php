<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/24/15
 * Time: 11:40 PM
 */

namespace Hero\Util;
use Hero\Loader\Model;
use Hero\Util\Store;

class Register {

  private static $instance;
  private static $meta;
  private $models = [];

  private static function getInstance() {
    if(empty(self::$instance)) self::$instance = new Register();
    return self::$instance;
  }

  private static function getMeta() {
    if(empty(self::$meta)) self::$meta = new RegisterMetabox();
    return self::$meta;
  }

  private static function callStatic($class, $method) {
    return call_user_func($class.'::'.$method);
  }

  public static function models() {

    add_action( 'init', array('\Hero\Util\Register', 'post_types'), 0 );
    self::getMeta()->register();
    new Model();

    foreach(get_declared_classes() as $class){
      if(get_parent_class($class) == 'Hero\Core\Model') self::model($class);
    }
  }

  private function addModel($model) {
    array_push($this->models, $model);
  }

  private static function model($model) {
    self::getInstance()->addModel($model);
    self::getMeta()->add(self::getType($model), self::callStatic($model, 'getFields'));
  }

  private static function getType($model) {
    return strtolower($model);
  }

  private static function post_type($model) {

    $labels = self::callStatic($model, 'getLabels');
    $icon = self::callStatic($model, 'getIcon');
    $sizes = self::callStatic($model, 'getSizes');
    foreach($sizes as $key => $value)
      add_image_size($key, $value[0], $value[1], (empty($value[2]) ? false : $value[2]));

    Store::push('models', self::callStatic($model, '_serialize'));

    $singular = (empty($labels[0])) ? ucfirst($model) : $labels[0];
    $plural = (empty($labels[1])) ? ucfirst($model.'s') : $labels[1];
    $icon = (empty($icon)) ? 'dashicons-admin-post' : $icon;
    $tax = self::callStatic($model, 'getTaxonomies');
    $fields = self::callStatic($model, 'getFields');
    $capabilities = self::callStatic($model, 'getCapabilities');
    $rewrite = self::callStatic($model, 'getRewrite');
    if(empty($rewrite)) $rewrite = 'hero_'.self::getType($model);

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

    $capability_type = 'page';
    $capabilities = [];

    if($capabilities) {
      $theCapabilities = get_post_type_object('post');
      $capability_type = strtolower($model);
      $capabilities = [];
      foreach ($theCapabilities->cap as $key) {
        $capabilities[$key] = str_replace('post', $capability_type, $key); 

      }
    } 

    $labels = array(
      'name'                => \__($plural),
      'singular_name'       => \__($singular),
      'menu_name'           => \__($plural),
      'parent_item_colon'   => \__( 'Parent Item:'),
      'all_items'           => \__($plural),
      'view_item'           => \__( 'View') . ' '. \__($plural),
      'add_new_item'        => \__( 'Add' ) . ' '. \__($singular),
      'add_new'             => \__( 'Add') .' '. \__($singular),
      'edit_item'           => \__( 'Edit') . ' '. \__($singular),
      'update_item'         => \__( 'Update'). ' '. \__($singular),
      'search_items'        => \__( 'Search'). ' '. \__($singular),
      'not_found'           => \__( 'Not found'),
      'not_found_in_trash'  => \__( 'Not found in Trash'),
    );

    $args = array(
      'label'               => __( $model , 'text_domain' ),
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
      'rewrite'             => array('slug' => $rewrite, 'with_front' => true)
    );

    if(self::getType($model) != 'page' && self::getType($model) != 'post'){
      register_post_type( self::getType($model) , $args );
    }

  }

  public static function post_types() {
    foreach(self::getInstance()->models as $model) self::post_type($model);
  }

}

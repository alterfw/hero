<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/25/15
 * Time: 12:44 AM
 */

namespace Hero\Core;
use Hero\Util\Store;


class Queryable {

  private static function getDefaultQuery(){
    return [
      'post_type'     => self::getType(),
      'post_status'   => 'publish'
    ];
  }

  private static function getModel() {
    $models = Store::get('models');
    foreach($models as $serialized) {
      $model = unserialize($serialized);
      if($model['post_type'] == strtolower(get_called_class())) return $model;
    }
  }

  public static function __callStatic($method, $arguments){

    $model = self::getModel();
    $qr = array();
    $non_custom_allowed = array('id', 'status', 'category', 'author', 'date');

    $findvalue = $arguments[0];

    if(!empty($arguments[1]) && count($arguments[1]) > 0){
      foreach($arguments[1] as $f => $v){
        $qr[$f] = $v;
      }
    }

    $custom_fields = [];
    foreach($model['fields'] as $field => $value){
      if(is_array($value)) $custom_fields[$field] = $value;
    }

    foreach($model['relations']['has_many'] as $many) {
      $custom_fields[$many] = true;
    }

    foreach($model['relations']['belongs_to'] as $bel) {
      $custom_fields[$bel] = true;
    }

    $attribute = str_replace("find_by_", "", self::from_camel_case($method));

    if(in_array($attribute, $non_custom_allowed)){

      $key = null;

      switch($attribute){

        case 'id':
          $key = 'p';
          break;

        case 'status':
          $key = 'post_status';
          break;

        case 'category':
          $key = 'cat';
          break;

        case 'author':
          $key = 'author';
          break;

        case 'date':
          $key = 'date_query';
          break;

      }

      $qr[$key] = $findvalue;

      return self::find($qr);

    }else{

      if(!empty($custom_fields[$attribute])){

        $qr['meta_key'] = $attribute;
        $qr['meta_value'] = $findvalue;

        return self::find($qr);

      }else{

        throw new \Exception("Trying to access a method that doesn't exists");

      }

    }

  }

  private static function getType() {
    return strtolower(get_called_class());
  }

  private static function buildQuery($options = null){

    $attrs = self::getDefaultQuery();

    if(!empty($options)){

      // But if is a array with the 'limit' index, too
      if(!empty($options['limit'])){
        $attrs['posts_per_page'] = $options['limit'];
      } else {
        $attrs['posts_per_page'] = '-1';
      }

      // Check if is arguments for WP_Query
      if(is_array($options)){
        foreach($options as $key => $value){
          $attrs[$key] = $value;
        }
      }

      // Or if is arguments for WP_Query into 'query' index
      if(!empty($options['query'])){
        $arr = explode('&', $options['query']);
        foreach($arr as $item){
          $arr_item = explode('=', $item);
          $attrs[$arr_item[0]] = $arr_item[1];
        }
      }

    }

    return $attrs;

  }

  public static function paginate($limit = null, $paged = null) {
    return self::paginateWithOptions([], $limit, $paged);
  }

  public static function paginateWithOptions($options, $limit = null, $paged = null) {

    if($paged == null){
      $paged = ( \get_query_var('paged') ) ? \get_query_var('paged') : 1;
    }

    if(empty($limit)){
      $limit = \get_option('posts_per_page');
    }

    return self::find($options, [
      'paginate_limit' => $limit,
      'paginate_page' => $paged
    ]);

  }

  public static function all() {
    return self::find();
  }

  public static function findBySlug($slug) {

    $args = array(
      'name' => $slug,
      'post_type' => self::getType(),
      'post_status' => 'publish',
      'numberposts' => 1
    );

    $my_posts = get_posts($args);

    if( $my_posts ) {
      return self::findById($my_posts[0]->ID);
    }else{
      return false;
    }

  }

  public static function findByTaxonomy($taxonomy, $value, $limit){

    $options = array();

    if(empty($limit)){
      $limit = get_option('posts_per_page');
    }

    $options['posts_per_page'] = $limit;

    $options['tax_query'] = array(
      array(
        'taxonomy' => $taxonomy,
        'field' => 'slug',
        'terms' => $value
      )
    );

    return self::find($options);

  }

  public static function find($options = null, $params = []){

    $attrs = self::buildQuery($options);

    if(empty($attrs['paged'])){
      $attrs['paged'] = 1;
    }

    if(empty($attrs['limit'])){
      $attrs['limit'] = -1;
    }

    $klass = ucfirst(self::getType());

    if(!empty($attrs['p'])){
      return new $klass($attrs['p']);
    }

    if(!empty($params['paginate_limit']))
      $attrs['posts_per_page'] = $params['paginate_limit'];

    if(!empty($params['paginate_page']))
      $attrs['paged'] = $params['paginate_page'];

    $qr = new \WP_Query($attrs);

    if(!$qr->have_posts()){
      return [];
    }else{

      $posts = array();

      while($qr->have_posts()){

        $qr->the_post();

        $obj = new $klass(get_the_ID());
        array_push($posts, $obj);

      }

      return $posts;

    }

  }

  private static function from_camel_case($str) {
    $str[0] = strtolower($str[0]);
    $func = create_function('$c', 'return "_" . strtolower($c[1]);');
    return preg_replace_callback('/([A-Z])/', $func, $str);
  }

}

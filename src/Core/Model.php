<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/24/15
 * Time: 11:26 PM
 */

namespace Hero\Core;

use Hero\Util\Store;

class Model extends Queryable implements \Serializable {

  private static $instance;

  /**
   * @param bool|false $id
   * @param array $exclude_relations
   */
  public function __construct($id = false, $exclude_relations = []) {
    if($id) {
      $this->id = $id;
      $this->setup($exclude_relations);
    }
  }

  /**
   * Save the instance using WordPress API
   */
  public function save() {

    $default_wp_fields = [
      'id',
      'content',
      'name',
      'title',
      'status',
      'author',
      'parent',
      'excerpt',
      'date',
      'date_gmt'
    ];

    $default_fields_raw = array_filter($this->_getFields(), function($var){
      return is_array($var);
    });

    $default_fields = [];
    foreach($default_fields_raw as $key => $value) {
      array_push($default_fields, $key);
    }

    $fields = get_object_vars($this);;

    $meta = [];
    $wp_fields = [
      'post_status' => 'publish'
    ];

    foreach($fields as $field => $value) {
      if(in_array($field, $default_fields)) {
        $meta[$field] = $this->{$field};
      } else if(in_array($field, $default_wp_fields)) {
        $wp_fields['post_'.$field] = $this->{$field};
      }
    }

    $wp_fields['post_type'] = strtolower(get_called_class());
    $id = wp_insert_post($wp_fields);

    $this->id = $id;

    foreach($meta as $key => $value) {
      update_post_meta($id, $key, $value);
    }

    $this->setup();

  }

  private function setup($exclude_relations = []) {

    $postObject = get_post($this->id);
    $post_type = strtolower(get_called_class());
    $_class = get_called_class();

    $fields = $this->getFields();
    $taxonomies = $this->getTaxonomies();

    $modelDefaultFields = ['title', 'thumbnail', 'editor'];
    $multipleFields = ['checkbox_list', 'plupload_image', 'checkbox_tree'];

    // Post default properties
    foreach($postObject as $key => $value){
      $chave = str_replace('post_', '', $key);
      $this->{$chave} = $value;
    }

    // Permalink
    $this->permalink = get_permalink($this->ID);

    // Default post taxonomies
    if($post_type == "post" && empty($taxonomies)){
      $taxonomies = array("post_tag", "category");
    }

    // Author
    $author = new \stdClass();
    foreach(array('ID', 'display_name', 'nickname', 'first_name', 'last_name', 'description', 'email') as $field){
      $author->{$field} = get_the_author_meta( $field, $this->author );
    }

    $this->author = $author;
    $this->content = apply_filters('the_content', $this->content);

    // Terms
    if( !empty($taxonomies))

      foreach($taxonomies as $taxonomy){

        $terms = array();
        $obj = get_the_terms( $this->ID, $taxonomy );

        if(is_array($obj))
          foreach($obj as $term){
            $term->link = get_term_link($term->term_id, $taxonomy);
            array_push($terms, $term);
          }

        $this->{$taxonomy} = $terms;
      }

    // Custom fields
    foreach($fields as $key => $value){

      $is_multiple = (!empty($value['multiple'])  && $value['multiple']);

      if(!in_array($key, $modelDefaultFields)){

        if($value['type'] !== 'image' && $value['type'] !== 'file'){

          if($is_multiple || in_array($value['type'], $multipleFields)){
            $this->{$key} = get_post_meta($postObject->ID, $key);

          }else{
            $this->{$key} = get_post_meta($postObject->ID, $key, true);
          }

        }else{

          switch($value['type']){

            case 'image':

              $this->{$key} = $this->getImage($postObject, $key, $value);

              break;

            case 'file':

              $this->{$key} = $this->getFile($postObject, $key, $value);

              break;

          }

        }

      }

    }

    // Relations

    $has_many = Store::get('relation_has_many');
    foreach($has_many as $many){
      if($many['target'] == $post_type && !in_array($many['model'], $exclude_relations)){
        $manyqr = new \WP_Query([
          'post_type'      => $many['model'],
          'meta_key'       => $many['target'],
          'meta_value'     =>$this->ID
        ]);
        if($manyqr->have_posts()){
          $ids = [];
          foreach($manyqr->posts as $_post){
            $klass = $this->getClass($many['model']);
            array_push($ids, new $klass($_post->ID, [$many['target']]));
          }
          $this->{$many['model']} = $ids;
        } else {
          $this->{$many['model']} = [];
        }
      } else if($many['model'] == $post_type){
        if(is_array($this->{$many['target']})){
          $ids = [];
          foreach($this->{$many['target']} as $item) {
            $klass = $this->getClass($many['target']);
            array_push($ids, new $klass($item, [$many['model']]));
          }
          $this->{$many['target']} = $ids;
        } else {
          $klass = $this->getClass($many['target']);
          $this->{$many['target']} = new $klass($this->{$many['target']}, [$many['model']]);
        }

      }
    }

    $belongs_to = Store::get('relation_belongs_to');
    //var_dump($belongs_to);
    foreach($belongs_to as $bel){
      if($bel['target'] == $post_type  && !in_array($bel['model'], $exclude_relations)){
        $belqr = new \WP_Query([
          'post_type'      => $bel['model'],
          'meta_key'       => $bel['target'],
          'meta_value'     =>$this->ID
        ]);
        if($belqr->have_posts()){
          $klass = $this->getClass($bel['model']);
          $this->{$bel['model']} = new $klass($belqr->posts[0], [$bel['target']]);
        } else {
          $this->{$bel['model']} = null;
        }
      } else if($bel['model'] == $post_type){
        $klass = $this->getClass($bel['target']);
        $this->{$bel['target']} = new $klass($this->{$bel['target']}, [$bel['model']]);
      }
    }



    // Include subpages
    if($post_type == 'page'){

      $my_wp_query = new \WP_Query();
      $all_wp_pages = $my_wp_query->query(array('post_type' => 'page'));

      // Filter through all pages and find Portfolio's children
      $children = get_page_children( $this->ID, $all_wp_pages );
      $this->children = array();

      foreach($children as $child){
        array_push($this->children, new Page($child));
      }

    }

    // Set the thumbnail
    $image = get_post_thumbnail_id($postObject->ID);

    $img = new \stdClass();

    foreach( get_intermediate_image_sizes() as $s ){
      $wp_image = wp_get_attachment_image_src( $image, $s);
      $img->{$s} = $wp_image[0];
    }

    $wp_image = wp_get_attachment_image_src( $image, 'full');
    $img->full = $wp_image[0];

    $this->thumbnail = $img;

  }

  // --------------- Utils

  private function getClass($type) {
    return ucfirst($type);
  }

  private function getFile($postObject, $key, $value){

    if(empty($value['multiple']) || !$value['multiple']){

      return wp_get_attachment_url(get_post_meta($postObject->ID, $key, true));

    }else{

      $files = array();
      $wpfiles = get_post_meta($postObject->ID, $key);
      foreach($wpfiles as $file){
        array_push($files, wp_get_attachment_url($file));
      }

      return $files;

    }

  }

  public function date($format){

    if(!empty($format)){
      return date($format, strtotime($this->date));
    }else{
      return $this->date;
    }

  }

  // --------------- Serialize

  public function serialize() {

    $data = [
      'post_type' => strtolower(get_class($this)),
      'fields' => $this->fields,
      'icon' => $this->_getIcon(),
      'labels' => $this->_getLabels(),
      'relations' => $this->_getRelations(),
      'taxonomies' => $this->_getTaxonomies()
    ];

    return serialize($data);

  }

  public function unserialize($str) {
    // do nothing
  }

  public static function _serialize(){
    return self::getInstance()->serialize();
  }

  // --------------- Getters

  static public function getName() {
    return get_called_class();
  }

  private static function getInstance() {
    $name = get_called_class();
    if(empty(self::$instance) || get_class(self::$instance) != $name) {
      $r = new \ReflectionClass($name);
      self::$instance = $r->newInstanceArgs();
    }
    return self::$instance;
  }

  private function registerRelation($model, $relation) {
    Store::pushIfNotPresent('relation_'.$relation, ['model' => strtolower(get_class($this)), 'target'=> $model]);
    $type = ($relation == 'belongs_to') ? 'list' : 'checkbox_list';
    return [
      'type' => $type,
      'label' => ucfirst($model),
      'options' => function() use($model){
        $arr = [];
        $items = call_user_func( ucfirst($model).'::find');
        foreach($items as $item) $arr[$item->ID] = $item->title;
        return $arr;
      }
    ];
  }

  private function registerBelongs($fields){
    if(!empty($this->belongs_to) && is_array($this->belongs_to)){
      foreach($this->belongs_to as $model) $fields[$model] = $this->registerRelation($model, 'belongs_to');
    } else if(!empty($this->belongs_to) && is_string($this->belongs_to)){
      $fields[$this->belongs_to] = $this->registerRelation($this->belongs_to, 'belongs_to');
    }
    return $fields;
  }

  private function registerHasMany($fields) {
    if(!empty($this->has_many) && is_array($this->has_many)){
      foreach($this->has_many as $model) $fields[$model] = $this->registerRelation($model, 'has_many');
    } else if(!empty($this->has_many) && is_string($this->has_many)){
      $fields[$this->has_many] = $this->registerRelation($this->has_many, 'has_many');
    }
    return $fields;
  }

  private function _getFields() {
    $fields = (empty($this->fields)) ? [] : $this->fields;
    $fields = $this->registerBelongs($fields);
    $fields = $this->registerHasMany($fields);
    return $fields;
  }

  public static function getFields() {
    return self::getInstance()->_getFields();
  }

  private function _getRelations() {
    $belongs_to = (!empty($this->belongs_to)) ? $this->belongs_to : [];
    $has_many = (!empty($this->has_many)) ? $this->has_many : [];
    if(is_string($belongs_to)) $belongs_to = [$belongs_to];
    if(is_string($has_many)) $has_many = [$has_many];
    return ['belongs_to'=>$belongs_to, 'has_many' => $has_many];
  }

  public static function getRelations() {
    return self::getInstance()->_getRelations();
  }

  private function _getTaxonomies() {
    if(!empty($this->taxonomies)) return $this->taxonomies;
    return [];
  }

  public static function getTaxonomies() {
    return self::getInstance()->_getTaxonomies();
  }

  private function _getLabels() {
    if(!empty($this->labels)) return $this->labels;
    return [];
  }

  public static function getLabels() {
    return self::getInstance()->_getLabels();
  }

  private function _getIcon() {
    if(!empty($this->icon)) return $this->icon;
  }

  public static function getIcon() {
    return self::getInstance()->_getIcon();
  }

}
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

    foreach($meta as $key => $value) {
      update_post_meta($id, $key, $value);
    }

  }

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

}
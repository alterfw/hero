<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/24/15
 * Time: 11:26 PM
 */

namespace Hero\Core;

use Hero\Util\Store;

abstract class Model extends Queryable {


  private function registerRelation($model, $relation) {
    Store::push('relation_'.$relation, ['model' => strtolower(get_class($this)), 'target'=> $model]);
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
    if(is_array($this->belongs_to)){
      foreach($this->belongs_to as $model) $fields[$model] = $this->registerRelation($model, 'belongs_to');
    } else if(is_string($this->belongs_to)){
      $fields[$this->belongs_to] = $this->registerRelation($this->belongs_to, 'belongs_to');
    }
    return $fields;
  }

  private function registerHasMany($fields) {
    if(is_array($this->has_many)){
      foreach($this->has_many as $model) $fields[$model] = $this->registerRelation($model, 'has_many');
    } else if(is_string($this->has_many)){
      $fields[$this->has_many] = $this->registerRelation($this->has_many, 'has_many');
    }
    return $fields;
  }

  public function getFields() {
    $fields = (empty($this->fields)) ? [] : $this->fields;
    $fields = $this->registerBelongs($fields);
    $fields = $this->registerHasMany($fields);
    return $fields;
  }

  public function getRelations() {
    $belongs_to = (!empty($this->belongs_to)) ? $this->belongs_to : [];
    $has_many = (!empty($this->has_many)) ? $this->has_many : [];
    return compact($belongs_to, $has_many);
  }

  public function getTaxonomies() {
    if(!empty($this->taxonomies)) return $this->taxonomies;
    return [];
  }

  public function getLabels() {
    if(!empty($this->labels)) return $this->labels;
    return [];
  }

  public function getIcon() {
    if(!empty($this->icon)) return $this->icon;
  }

}
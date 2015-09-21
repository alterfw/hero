<?php

namespace Hero\Util;

class RegisterMetabox {

  private $boxes = [];

  /**
  * Add a post type and his fields
  *
  * @param $post_type
  * @param $fields
  */
  public function add($post_type, $fields){
    if($fields){
      $this->boxes[$post_type] = $fields;
    }

  }

  public function register(){

    add_filter( 'rwmb_meta_boxes', array($this, 'doRegister') );

  }

  public function doRegister(){

    $post = false;
    if(!empty($_GET['post'])){
      $post = get_post($_GET['post']);
    }

    $meta_boxes = array();
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

    foreach($this->boxes as $post_type => $fields){

      $box = array(
        'id' => $post_type . '_metabox',
        'title' => __('More') .' '. __('About'),
        'pages' => array($post_type),
        'context' => 'normal',
        'priority' => 'high',
        'autosave' => true,

        // List of meta fields
        'fields' => array()
      );

      foreach($fields as $key => $content){

        if(!empty($content['if']) && $post){
          $condition = $content['if'];

          if(is_array($condition)) {
            eval('$valid = $post->post_'.$condition[0].' '.$condition[1].' "'.$condition[2].'";');
            if(!$valid) continue;
          } else if(is_callable($condition)) {
            $valid = call_user_func_array($condition, [$post]);
            if(!$valid) continue;
          } else {
            throw new \Exception("Invalid Argument for conditional field, please provide an array or closure");
          }

        }

        if(!in_array($key, $wp_fields)){

          if(!empty($content['options'])){

            if(is_array($content['options'])){
              $options = $content['options'];
            } else if(is_callable($content['options'])){
              $function = $content['options'];
              $options = call_user_func($function);
            } else {
              $options = [];
            }

          }

          $field_options = [];

          switch($content['type']){

            case 'int':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'number',
            );

            break;

            case 'text':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'text',
            );

            break;

            case 'long_text':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'textarea',
            );

            break;

            case 'float':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'text',
            );

            break;

            case 'boolean':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'checkbox',
            );

            break;

            case 'list':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'select',
              'options' => $options
            );

            break;

            case 'file':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'file',
            );

            break;

            case 'date':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'date',
            );

            break;

            case 'map':

            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => 'map',
              'style' => 'height: 300px;',
              'std' => '-7.1274404, -34.868966'
            );

            break;

            case 'image':

            if(empty($content['multiple'])){

              $field_options = array(
                'name' => $content['label'],
                'id'   => $key,
                'type' => 'image',
              );

            }else{

              $field_options = array(
                'name' => $content['label'],
                'id'   => $key,
                'type' => 'plupload_image',
              );

            }

            break;

            default:
            $field_options = array(
              'name' => $content['label'],
              'id'   => $key,
              'type' => $content['type'],
              'options' => $options
            );
            break;

          }

          unset($content['label']);
          $merged = array_merge($content, $field_options);
          array_push($box['fields'], $merged);

        }

      }

      if(count($box['fields']) > 0){
        array_push($meta_boxes, $box);
      }

    }

    if(count($meta_boxes) > 0){
      return $meta_boxes;
    }else{
      return array();
    }

  }

}

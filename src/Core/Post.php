<?php

namespace Hero\Core;
use Hero\Util\Store;

class Post {

  public function __construct($this_id, $post_type, $exclude_relations = []){

    $postObject = get_post($this_id);
    $object = array();
    $className = ucfirst($post_type);
    $model = new $className;

    $fields = $model->getFields();
    $taxonomies = $model->getTaxonomies();

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
            array_push($ids, new Post($_post->ID, $many['model'], [$many['target']]));
          }
          $this->{$many['model']} = $ids;
        } else {
          $this->{$many['model']} = [];
        }
      } else if($many['model'] == $post_type){
        if(is_array($this->{$many['target']})){
          $ids = [];
          foreach($this->{$many['target']} as $item) array_push($ids, new Post($item, $many['target'], [$many['model']]));
          $this->{$many['target']} = $ids;
        } else {
          $this->{$many['target']} = new Post($this->{$many['target']}, $many['target'], [$many['model']]);
        }

      }
    }

    $belongs_to = Store::get('relation_belongs_to');
    //var_dump($belongs_to);
    foreach($belongs_to as $bel){
      if($bel['target'] == $model->getPostType()  && !in_array($bel['model'], $exclude_relations)){
        $belqr = new \WP_Query([
          'post_type'      => $bel['model'],
          'meta_key'       => $bel['target'],
          'meta_value'     =>$this->ID
        ]);
        if($belqr->have_posts()){
          $this->{$bel['model']} = new Post($belqr->posts[0], $bel['model'], [$bel['target']]);
        } else {
          $this->{$bel['model']} = null;
        }
      } else if($bel['model'] == $model->getPostType()){
        $this->{$bel['target']} = new Post($this->{$bel['target']}, $bel['target'], [$bel['model']]);
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
        array_push($this->children, new Post($child, $model));
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

  private function getImage($postObject, $key, $value){

    $retorno = array();

    if(empty($value['multiple']) || !$value['multiple']){

      $image = get_post_meta($postObject->ID, $key, true);

      $img = new \stdClass();

      foreach( get_intermediate_image_sizes() as $s ){
        $wp_image = wp_get_attachment_image_src( $image, $s);
        $img->{$s} = $wp_image[0];
      }

      $wp_image = wp_get_attachment_image_src( $image, 'full');
      $img->full = $wp_image[0];
      $img->caption = get_post($image)->post_excerpt;

      $retorno = $img;

    }else{

      $images = get_post_meta($postObject->ID, $key);

      foreach($images as $image){

        $img = new \stdClass();

        foreach( get_intermediate_image_sizes() as $s ){
          $wp_image = wp_get_attachment_image_src( $image, $s);
          $img->{$s} = $wp_image[0];
        }

        $img->caption = get_post($image)->post_excerpt;

        array_push($retorno, $img);

      }

    }

    return $retorno;

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

}

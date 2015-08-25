<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 8/25/15
 * Time: 12:44 AM
 */

namespace Hero\Core;


class Queryable {

  private static function getDefaultQuery(){
    return [
      'post_type'     => self::getType(),
      'post_status'   => 'publish'
    ];
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

  public static function find($options = null, $params = []){

    // Reset the paginated options
    if(!empty($params['paginated'])){
      $params['paginate_page'] = false;
      $params['paginate_limit'] = false;
    }

    $attrs = self::buildQuery($options);

    if(empty($attrs['paged'])){
      $attrs['paged'] = 1;
    }

    if(empty($attrs['limit'])){
      $attrs['limit'] = -1;
    }

    if(!empty($attrs['p'])){
      return new Post($attrs['p'], self::getType());
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

        $obj = new Post(get_the_ID(), self::getType());
        array_push($posts, $obj);

      }

      return $posts;

    }

  }

}
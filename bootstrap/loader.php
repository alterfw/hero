<?php namespace Hero;

use Hero\Util\Register;
use Hero\Util\Store;

class Loader {

  public static function load() {

    // Constants
    $ci = getenv('CI');
    $hero_env = getenv('HERO_ENV');

    if(!empty($ci) && !defined('HERO_ENV')) define('HERO_ENV', 'TEST');
    if(!empty($hero_env) && !defined('HERO_ENV')) define('HERO_ENV', $hero_env);

    if(defined('HERO_ENV') && HERO_ENV == 'TEST') require_once __DIR__.'/../test/bootstrap.php';
    if(defined('HERO_ENV') && HERO_ENV == 'CLI') {
      if(strpos(__DIR__, 'vendor') > -1) {
        if(!defined('APPLICATION_PATH')) define('APPLICATION_PATH', explode('vendor', __DIR__)[0]);
        define('RWMB_VER', false);
      }
    }

    if(!defined('HERO_ENV')) define('HERO_ENV', 'PROD');
    if(!defined('ALTER')) define('ALTER', __DIR__ . "/..");
    if(!defined('ALTER_VENDOR')) define('ALTER_VENDOR', ALTER . "/..");
    if(!defined('COMMOM_VENDOR')) define('COMMOM_VENDOR', ALTER . "/../..");
    if(!defined('APPLICATION_PATH')) define('APPLICATION_PATH', \get_template_directory());

    if(!defined('RWMB_VER') || HERO_ENV != 'CLI') self::loadMetaBox();

    Store::set('relation_belongs_to', []);
    Store::set('relation_has_many', []);
    Store::set('models', []);

    if(HERO_ENV != 'CLI') Register::models();

  }

  private static function loadMetaBox() {

    if(HERO_ENV == 'PROD') {
      $path = explode('wp-content', realpath(COMMOM_VENDOR . "/rilwis/meta-box/"));
      $url = \get_site_url().'/wp-content'.$path[1].'/';
    } else {
      $url = 'http://localhost';
    }

    \RWMB_Loader::load($url, COMMOM_VENDOR . "/rilwis/meta-box/");

    require_once RWMB_INC_DIR . 'common.php';
    require_once RWMB_INC_DIR . 'field.php';
    require_once RWMB_INC_DIR . 'field-multiple-values.php';

    foreach ( glob( RWMB_FIELDS_DIR . '*.php' ) as $file ) {
      require_once $file;
    }

    require_once RWMB_INC_DIR . 'meta-box.php';
    require_once RWMB_INC_DIR . 'helpers.php';
    require_once RWMB_INC_DIR . 'init.php';

  }

}

Loader::load();

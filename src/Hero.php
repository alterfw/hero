<?php

namespace Alter;
use Alter\Hero\Core\App;
use Alter\Hero\Core\ModelLoader;

class Hero {

  private static $app;

  public function __construct() {

    // Constants
    if(!defined('ALTER')) define('ALTER', __DIR__ . "/..");
    if(!defined('ALTER_VENDOR')) define('ALTER_VENDOR', ALTER . "/..");

    if(!defined('ASSETS_PATH')) define('ASSETS_PATH', get_bloginfo('template_url'));
    if(!defined('APPLICATION_PATH')) define('APPLICATION_PATH', get_template_directory());

    $path = explode('wp-content', realpath(ALTER_VENDOR . "/meta-box/"));
    define('RWMB_URL', get_site_url().'/wp-content'.$path[1].'/');
    define('RWMB_DIR', ALTER_VENDOR . "/meta-box/" );

    // Assets constants
    if(!defined('ALTER_IMG')) define('ALTER_IMG', ASSETS_PATH . "/img");
    if(!defined('ALTER_CSS')) define('ALTER_CSS', ASSETS_PATH . "/css");
    if(!defined('ALTER_JS')) define('ALTER_JS', ASSETS_PATH . "/js");

    // ---- Import framework dependencies in the right order (Composer sucks for that!)
    require_once ALTER_VENDOR . "/php-form-generator/fg/load.php";
    require_once ALTER_VENDOR . "/wordpress-for-developers/lib/load.php";
    require_once ALTER_VENDOR . "/meta-box/meta-box.php";

    self::$app = new App();
    new ModelLoader(self::$app);

  }

  public function get(){
    return self::$app;
  }

}

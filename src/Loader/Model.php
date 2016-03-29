<?php

namespace Hero\Loader;
use Alter\Common\Loader;

class Model extends Loader {

	function __construct() {

    $folder = (!defined('HERO_MODELS')) ? 'model' : HERO_MODELS;
		parent::__construct(null, [$folder]);
		$this->load();

	}

}

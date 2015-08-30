<?php

namespace Hero\Core;
use Alter\Common\Loader;
use Hero\Util\RegisterMetabox;

/**
 * Class Loader
 *
 * This class load all the user files
 */
class ModelLoader extends Loader {

	private $folders = array('model');

	function __construct() {
		parent::__construct(null, ['model']);
		$this->load();
	}

}

<?php

namespace Hero\Loader;
use Alter\Common\Loader;

class Model extends Loader {

	function __construct() {
		parent::__construct(null, ['model']);
		$this->load();
	}

}

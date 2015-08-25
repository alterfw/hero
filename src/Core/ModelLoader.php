<?php

namespace Hero\Core;
use Alter\Common\Loader;
use Hero\Util\RegisterMetabox;

/**
* Class Loader
*
* This class load all the user files
*/
class ModelLoader extends Loader
{

	private $app;
	private $folders = array('model');

	function __construct($app)
	{

		$self = $this;
		parent::__construct($app);
		$this->rw = new RegisterMetabox();
		$this->rw->register();

		parent::handle('AppModel', function($_app, $instance) use ($self) {
			$_app->registerModel($instance);
			$self->rw->add($instance->getPostType(), $instance->getFields());
		});

		$this->load();

	}

	protected function load()
	{

		parent::load();

		if(empty($this->app->post)){
			$this->loadFile(ALTER . '/src/core/default/PostModel.php');
		}

		if(empty($this->app->page)){
			$this->loadFile(ALTER . '/src/core/default/PageModel.php');
		}

	}

}

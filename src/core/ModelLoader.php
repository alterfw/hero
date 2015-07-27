<?php

use Alter\Common\Loader;

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

		parent::__construct($app);
		$this->rw = new RegisterMetabox();
		$this->rw->register();

		$this->handle('AppModel', function($_app, $instance){
			$this->app->registerModel($instance);
			$this->rw->add($instance->getPostType(), $instance->getFields());
		});

	}

	private function load()
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

<?php

namespace Hero\Core;

class App {

	private $taxonomies = array();
	private $models = array();
	private $terms = array();
	public $option;

	public function __construct(){
		$this->option = new \stdClass();
	}

	public function registerModel($model){

		$modelName = str_replace('model', '', strtolower(get_class($model)));

		$modelItem = new Model($model);
		$this->{$modelName} = $modelItem;

		array_push($this->models, $modelItem);

	}

	public function registerOption($option){
		$this->option->{strtolower(get_class($option))} = $option;
	}

	/**
	 * Add a taxonomy
	 *
	 * @param $taxonomy
	 * @param $singular
	 * @param $plural
	 */
	public function registerTaxonomy($taxonomy, $singular, $plural, $hierarchical = true){

		$arr = array('key'=> $taxonomy, 'singular' => $singular, 'plural' => $plural, 'hierarchical' => $hierarchical);
		array_push($this->taxonomies, $arr);

	}

	/**
	 * Register all taxonomies
	 */
	public function registerTaxonomies(){

		foreach($this->taxonomies as $tax){

			$post_type = array();

			foreach($this->models as $model){

				if($model->getAppModel()->getTaxonomies() && in_array($tax['key'], $model->getAppModel()->getTaxonomies())){
					array_push($post_type, $model->getAppModel()->getPostType());
				}

			}

			if(count($post_type) > 0){
				new AppTaxonomy($tax, $post_type);
			}

		}

		add_action('init', array($this, 'registerTerms'), 1);

	}

	public function registerTerm($taxonomy, $slug, $term){

		$item = new stdClass();
		$item->taxonomy = $taxonomy;
		$item->slug = $slug;
		$item->term = $term;

		array_push($this->terms, $item);

	}

	public function registerTerms(){

		foreach($this->terms as $item){

			if(!term_exists($item->slug, $item->taxonomy)){

				wp_insert_term($item->term, $item->taxonomy, array('slug'=> $item->slug));

			}

		}

	}

	public function getIdBySlug($page_slug) {
		$page = get_page_by_path($page_slug);
		if ($page) {
			return $page->ID;
		} else {
			return null;
		}
	}

}

<?php

namespace Framework;

/**
 * Framework Parent Model.
 *
 * This is the main model that is extended by user models and extends
 * the App class, that sets up framework specific objects and functions.
 * Helper functions are set up for inheritance and are used for "data" specific things.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Model{

  /**
   * Holds an array of other models that will be used within this model.
   * Important not to cause an infinite loop by reference a model that references this model.
   * @var array
   */
	public $models = array();

  /**
   * Holds the data this model will expose.
   * @var mixed
   */
	public $data;

  /**
   * Pagination information will be stored in this variable.
   * @var mixed
   */
	public $pagination;

  /**
   * Set the page_break e.g. the limit on records to show.
   * @var object
   */
	public $page_break = false;

  /**
   * Implements __construct();
   *
   * The main Model constructor.
   *
   * @return void
   */
	public function __construct(){
    $this->db   = new Database();
    $this->user = new Auth();;

    // Fire init hook.
		$this->init();
	}

  /**
   * Implements init();
   *
   * Fired when a model is ready.
   */
	public function init(){ }
}

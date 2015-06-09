<?php

namespace Model;

use Framework\Model;

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
class ExampleModel extends Model{
  /**
   * Holds the table that this model will use.
   * @var string
   */
	public $table = 'TABLE_NAME';

  /**
   * Implements init();
   *
   * Fired when a model is ready.
   */
	public function init(){
    var_dump($this->db);
    // You can use other models you have defined here by doing the following:
    /*
    $otherModel = new OtherModel();

    $otherModel->get();

    $data = $otherModel->data;
    */
  }

}

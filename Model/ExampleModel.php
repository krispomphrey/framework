<?php

namespace Model;

use Framework\Model;

/**
 * Framework Model.
 *
 * This is an example Model for usage.
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
    // You can use other models you have defined here by doing the following:
    /*
    $otherModel = new OtherModel();

    $otherModel->get();

    $data = $otherModel->data;
    */
  }

}

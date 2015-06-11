<?php

namespace Model;

use Framework\Model;

// Import the Doctrine Entity (i.e. the table) to use with the model.
use Model\Entity\Example;

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
   * Implements init();
   *
   * Fired when a model is ready.
   */
	public function init(){
    $example = new Example();

    $example->setCreated();
  }
}

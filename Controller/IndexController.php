<?php

namespace Controller;

use Framework\Controller;

/**
 * Index Controller.
 *
 * This is the main controller that lives at your site root.
 * If this controller is removed then the site won't function properly.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class IndexController extends Controller{

	public $protected = false;
	public $login = false;

	public function pre_init(){
		//$this->asset('css', 'bootstrap.min.css');

    $this->model('Example');
	}

	public function init(){
		$this->render('home');
	}
}

<?php
class IndexController extends Controller{
  public $protected = true;
  public $login = true;
  public $auth = array('allow' => array(99));
	public function pre_init(){
		$this->asset('css', 'bootstrap.min.css');
	}
	public function init(){
		$this->render('home');
	}
}

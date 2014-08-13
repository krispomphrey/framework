<?php
class IndexController extends Controller{
	public function pre_init(){
    $this->model('Login');
		$this->asset('css', 'bootstrap.min.css');
	}
	public function init(){
		$this->render('home');
	}
}

<?php
class AdminController extends Controller{
	public $protected = 1;
	public $login = 1;
	public function pre_init(){
		$this->asset('css', 'bootstrap.min.css', true);
	}
	public function init(){
		$this->render('admin');
		switch($this->router->action[1]){
			// The switch statement for the logic after /admin/xxx
		}
	}
}
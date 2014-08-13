<?php
class AdminController extends Controller{
	public $models = array('login');
	public $protected = 0;
	public $login = 0;
	public $auth = array(
		'allow' => array(99)
	);
	public function pre_init(){
		$this->asset('css', 'bootstrap.min.css', true);
		$this->asset('css', 'template.css', true);
		if($_POST && $this->user->loggedin == 0){
			$this->model('login');
			if($this->model->check_login_details($_POST)){
				if($this->user->login(array_pop($this->model->data))){
					//$this->router->redirect($_SERVER['REQUEST_URI']);
				}
			} else {
				array_push($this->messages, array(
					'type' => 'danger',
					'notice' => 'Wrong username/password combination'
				));
				$this->layout = 'login';
				$this->asset('css', 'login.css', true);
				$this->render('login', true);
			}
		}
	}
	public function init(){
		switch($this->router->action[1]){
			case 'content': $this->render('content', true);
			case 'pages': $this->render('users', true);
			case 'users': $this->render('users', true);
			case 'settings': $this->render('settings', true);
			default: $this->render('dashboard', true);
		}
	}
}

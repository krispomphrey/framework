<?php
class LoginController extends Controller{
	public function init(){
		if($_POST){
			$this->model('Login');
			if($this->model->check_login_details()){
				if($this->user->login(array_pop($this->model->data))){
					Router::redirect('/');
				}
			} else {
				array_push($this->messages, array(
					'type' => 'danger',
					'notice' => 'Wrong username/password combination'
				));
				if(isset($_GET['admin']) && $_GET['admin'] == 1){
					$this->render('login', true);
				} else {
					$this->render('login');
				}

			}
		} else {
			$this->router->redirect('/');
		}
	}
}

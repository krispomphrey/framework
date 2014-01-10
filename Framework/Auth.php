<?php
class Auth{
	public $loggedin = 0;
	public $id = 0;
    public $name = '';
    public $username = '';
    public $acl = 0;
    public $acllabel = '';
	public function __construct(){
		if(!isset($_SESSION)){
		    session_start();
		}
		if(isset($_SESSION['loggedin'])){
			$this->init_user();
		} else {
			$this->loggedin = 0;
		}
	}
	public function login($user_data){
		$_SESSION['loggedin'] = 1;
		foreach($user_data as $key => $udata){
			$_SESSION[$key] = $udata;
		}
		unset($_SESSION['password']);
		$this->init_user();
		return true;
	}
	public function init_user(){
		foreach($_SESSION as $key => $value){
			$this->$key = $value;
		}
		//foreach($this->access_levels() as $key => $label){
			//if($this->acl == $key){
				//$this->acllabel = $label;
			//}
		//}
	}
	public function access_levels(){
		return array();
	}
	public function logout($url){
		unset($_SESSION['loggedin']);
		$this->loggedin = 0;
		Router::redirect($url);
	}
} ?>

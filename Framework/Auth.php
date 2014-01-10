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
        $_SESSION['id'] = $user_data->id;
        $_SESSION['name'] = $user_data->name;
        $_SESSION['username'] = $user_data->username;
        $_SESSION['level'] = $user_data->level;
		$this->init_user();
		return true;
	}
	public function init_user(){
		$db = new Database();
		$this->db = 0;
		$this->loggedin = 1;
		$this->id = $_SESSION['id'];
		$this->name = $_SESSION['name'];
		$this->username = $_SESSION['username'];
		$this->acl = $_SESSION['level'];
		foreach($this->access_levels() as $key => $label){
			if($this->acl == $key){
				$this->acllabel = $label;
			}
		}
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

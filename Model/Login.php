<?php
class LoginModel extends Model{
	public $table = 'users';
	public function init(){

	}
	public function check_login_details(){
		$this->db->db = 0;
		$return = false;
		$pw = md5($_POST['password']);
		$this->get(array('username' => $_POST['username'], 'password' => $pw), 'AND');
		if($this->data){
			foreach($this->data as $key => $value){
				if($_POST['username'] == $value->username && md5($_POST['password']) == $value->password){
					return true;
				}
			}
		} else {
			return false;
		}
	}
}

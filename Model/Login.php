<?php
class LoginModel extends Model{
	public $table = 'users';
	public function init(){

	}
	public function check_login_details($post){
		$this->db->db = 0;
		$return = false;
		$pw = md5($post['password']);
		$this->get(array('username' => $post['username'], 'active' => 1), 'AND');
		if($this->data){
			foreach($this->data as $key => $value){
				if($post['username'] == $value->username && md5($post['password']) == $value->password){
					return true;
				}
			}
		} else {
			return false;
		}
	}
}

<?php
class LoginModel extends Model{
	public $table = 'users';
	public function init(){
    $this->get();
	}
}

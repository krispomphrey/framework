<?php
class Config{
  public function __construct(){
  	$this->db_host = '127.0.0.1';
  	$this->db_user = 'root';
  	$this->db_password = 's10052';
  	$this->dbs = array('fw_data');
  	$this->debug = true;
    $this->base_url = $_SERVER['HTTP_HOST'];
  }
}
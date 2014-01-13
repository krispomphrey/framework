<?php
class Database{
	public $info;
	public $error;
	public $insert_id;
	public $db;
	public function __construct($db = null){
		if($db){
			$this->db = $db;
		} else{
			$this->db = 0;
		}
		$this->config = new Config();
		$this->init();
	}
    public function init(){
        $con = mysql_connect($this->config->db_host, $this->config->db_user, $this->config->db_password);
		$this->info = mysql_info();
		$this->error = mysql_error();
        if(!$con){
        	if(file_exists(LAYOUT_ROOT.'error.php')){
        		die(include_once(LAYOUT_ROOT.'error.php'));
        	} else{
        		die(include_once(FW_ROOT.'static/error.php'));
        	}
		} else {
	        if(is_array($this->config->dbs)){
	        	foreach($this->config->dbs as $key => $db){
	        		if($this->db == $key) $selected = $db;
	        	}
	        } else $selected = $db;
	        mysql_select_db($selected) or die(mysql_error());
		}
    }
    public function destroy(){
        $con = mysql_connect($this->config->db_host, $this->config->db_user, $this->config->db_password);
        if(!$con){
        	if(file_exists(LAYOUT_ROOT.'error.php')){
        		die(include_once(LAYOUT_ROOT.'error.php'));
        	} else{
        		die(include_once(FW_ROOT.'static/error.php'));
        	}
		} else {
        	mysql_close($con);
		}
    }
	public function select($sql, $object = true){
		$results_objects = array();
		$results = mysql_query($sql);
		if($object == true){
			while($row = mysql_fetch_object($results)){
				$results_objects[] = $row;
			}
		} else {
			while($row = mysql_fetch_assoc($results)){
				$results_objects[] = $row;
			}
		}
		$this->info = mysql_info();
		$this->error = mysql_error();
		return $results_objects;
	}
	public function update($sql){
		$results = mysql_query($sql);
		$this->info = mysql_info();
		$this->error = mysql_error();
		$this->insert_id = mysql_insert_id();
		if(mysql_affected_rows() >= 0){
			return true;
		} else {
			return false;
		}
	}
	public function delete($sql){
		$results = mysql_query($sql);
		$this->info = mysql_info();
		$this->error = mysql_error();
		if(mysql_affected_rows() >= 0){
			return true;
		} else {
			return false;
		}
	}
	public function switchdb($db){
		$this->db = $db;
		$this->init();
	}
} ?>

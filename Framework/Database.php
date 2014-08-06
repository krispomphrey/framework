<?php
/**
 * Framework Database Class.
 * Class defines database and holds useful functions.
 *
 * TODO: Split into different drivers.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Database{
	public $info;
	public $error;
	public $insert_id;
	public $db;

  /**
   * Implements __construct();
   *
   * The main Database constructor.
   */
	public function __construct($db = null){
    // Check if a specific DB is to be created.
		if($db){
			$this->db = $db;
		} else{
			$this->db = 0;
		}

    // Pull conf into context.
		$this->config = new Config();
		$this->init();
	}

  /**
   * Implements init();
   *
   * Initialise the connection using the details provided in conf.
   */
  public function init(){
    $con = mysql_connect($this->config->db_host, $this->config->db_user, $this->config->db_password);
		$this->info = mysql_info();
		$this->error = mysql_error();
    if(!$con){
    	$this->error();
		} else {
      if(is_array($this->config->dbs)){
      	foreach($this->config->dbs as $key => $db){
      		if($this->db == $key) $selected = $db;
      	}
      } else $selected = $db;
      mysql_select_db($selected) or die(mysql_error());
		}
  }

  /**
   * Implements destroy();
   *
   * You can kill a connection.  You can't kill an idea.
   */
  public function destroy(){
    $con = mysql_connect($this->config->db_host, $this->config->db_user, $this->config->db_password);
    if(!$con){
    	$this->error();
  	} else {
    	mysql_close($con);
  	}
  }

  /**
   * Implements select();
   *
   * Knowledge is power.  Have some.
   */
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

  /**
   * Implements update();
   *
   * Level up!
   */
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

  /**
   * Implements delete();
   *
   * Content can no longer be allowed to exist.
   */
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

  /**
   * Implements switchdb();
   *
   * In case there is more than one db we can switch.
   */
	public function switchdb($db){
		$this->db = $db;
		$this->init();
	}

  /**
   * Implements error();
   *
   * What to do when we fail in our quest for content.
   */
  private function error(){
    if(file_exists(LAYOUT_ROOT.'error.php')){
      die(include_once(LAYOUT_ROOT.'error.php'));
    } else{
      die(include_once(FW_ROOT.'static/error.php'));
    }
  }
} ?>

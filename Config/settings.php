<?php
/**
 * Framework Configuration.
 *
 * All the configuration for the framework is held here.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Config{
<<<<<<< HEAD
	public $db_host = '127.0.0.1';
	public $db_user = 'root';
	public $db_password = 'supermassive';
	public $dbs = array('fw_data');
	public $debug = 0;
=======
  public function __construct(){
  	$this->dbs = array(
      'fw_data' => array(
        'type' => 'mysqli',
        'user' => 'root',
        'password' => 's10052',
        'host' => 'localhost',
        'prefix' => ''
      )
    );
  	$this->debug = true;
    $this->base_url = $_SERVER['HTTP_HOST'];
  }
>>>>>>> adb4486762bdfe4142ee08ce3b0ec0619802893e
}

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
  public function __construct(){
  	// An example of how to register a database.
    /* $this->dbs = array(
      'fw_data' => array(
        'type' => 'mysqli',
        'user' => 'DB_USER',
        'password' => 'DB_PASSWORD',
        'host' => 'localhost',
        'prefix' => ''
      )
    ); */
  	$this->debug = true;
    $this->base_url = $_SERVER['HTTP_HOST'];
  }
}

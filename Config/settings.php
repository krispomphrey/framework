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
}

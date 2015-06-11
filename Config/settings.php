<?php
namespace Config;

/**
 * Framework Configuration.
 *
 * All the configuration for the framework is held here.
 *
 * @package   Framework
 * @author    Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Settings{
  public function __construct(){
  	/* An example of how to register a database.
     * For multiple databases, add to the array with 'db_name' => 'options'.  All options are needed, even if blank.
     * Because doctrine is used, you can specify the driver and where the entities will live.
     */
    /*
    $this->dbs = array(
      'fw_data' => array(
        'driver'      => 'pdo_mysql',
        'user'        => 'root',
        'password'    => 'root',
        'host'        => 'localhost',
        'entity_path' => '',
      )
    );
    */

    $this->dbs = array(
      'fw_data' => array(
        'driver'      => 'pdo_mysql',
        'user'        => 'root',
        'password'    => 'root',
        'host'        => 'localhost',
        'entity_path' => '',
      )
    );

    // Whether to output debugging information or not.
  	$this->debug = true;

    // Sets the base url of the site.  Leave as default for multi domain sites.
    $this->base_url = $_SERVER['HTTP_HOST'];

    // Set an array of ignore controllers (i.e. paths), or leave the array empty to default.
    $this->ignore = array();
  }
}

<?php
namespace Framework;

if(file_exists(FW_ROOT.'vendor/autoload.php')) require_once FW_ROOT.'vendor/autoload.php';

use Framework\Config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Framework Database Class.
 *
 * The database abstraction class that sets up the databases based on the configuration.
 * This will pull in the correct SQL driver to use.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Database{
  /**
   * Holds the data from the driver.
   * @var mixed
   */
  public $data;

  /**
   * Holds an array of databased and their clients.
   * @var object
   */
	private $dbs;

  /**
   * Holds the configuration from settings.php.
   * @var object
   */
  private $config;

  /**
   * Implements __construct();
   *
   * The main Database constructor.  This will run through the list of databases
   * (if any) in the config and create instances available at $db->dbs['DB_NAME'].
   *
   * @return void
   */
	public function __construct(){
    // Grab the configuration so we can extract some databases.
		$this->config = new Config();

    // Make sure there are databases to use.
    if(isset($this->config->dbs) && !empty($this->config->dbs)){
      // Count how many databases we have, so we can set the object to be simple or multidimensional.
      // Run through each of the databases defined.
      foreach($this->config->dbs as $key => $db){
        $paths = array(empty($db['entity_path']) ? DIR_ROOT.$db['entity_path'] : ENTITY_ROOT);
        $isDevMode = $this->config->debug ? $this->config->debug : false;

        // The connection configuration
        $dbParams = array(
          'driver'   => $db['driver'],
          'user'     => $db['user'],
          'password' => $db['password'],
          'dbname'   => $key,
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

        // Build the doctrine settings.
        // Assign an entity manager to the db for use!
        $this->dbs[$key] = EntityManager::create($dbParams, $config);
      }
    } else return false;
	}

  /**
   * Implements error();
   *
   * What to do when we fail in our quest for content.
   * @return void
   */
  private function error(){
    if(file_exists(LAYOUT_ROOT.'error.php')){
      die(include_once(LAYOUT_ROOT.'error.php'));
    } else{
      die(include_once(FW_ROOT.'static/error.php'));
    }
  }
}

// Default root for Doctrine Entities
define('ENTITY_ROOT',     MODEL_ROOT.'/Entity/');

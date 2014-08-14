<?php
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
        // Check that the type is set so we can require the correct driver.
        if($db['type'] && !empty($db['type'])){
          $class = ucwords($db['type']).'Driver';
          // Build the driver path.
          $current = FW_ROOT.'Drivers/Database/'.$class.'.php';
          // Only do something if the driver class exists.
          if(file_exists($current)){
            require_once($current);
            // Start the new instance of the database.
            $this->dbs[$key] = new $class($key, $db);
          }
        }
      }
    }
	}

  /**
   * Implements __construct();
   *
   * The main Database constructor.  This will run through the list of databases
   * (if any) in the config and create instances available at $db->dbs['DB_NAME'].
   *
   * @return void
   */
  public function __destruct(){
    if(isset($this->config->dbs) && !empty($this->config->dbs)){
      $this->destroy();
    }
  }

  /**
   * Implements query();
   *
   * A generic query wrapper that can be used to pass a statement to the driver.
   * All drivers will have at LEAST a query function.
   *
   * @param mixed   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return void/mixed
   */
  public function query($statement, $instances = array()){
    if($statement){
      if(!empty($instances)){
        foreach($instances as $instance){
          $this->data[$instance] = $this->dbs[$instance]->query($statement);
        }
      } else {
        foreach($this->dbs as $key => $db){
          $this->data[$key] = $db->query($statement);
        }
      }
      return $this->data;
    } else $this->error();
  }

  /**
   * Implements insert();
   *
   * Create something new.
   *
   * @param array   $data         An array of data to insert in FIELD => VALUE array.
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return void/mixed
   */
  public function insert($data, $statement = array(), $instances = array()){
    if($statement){
      if(!empty($instances)){
        foreach($instances as $instance){
          $this->data[$instance] = $this->dbs[$instance]->insert($data, $statement);
        }
      } else {
        foreach($this->dbs as $key => $db){
          $this->data[$key] = $db->insert($data, $statement);
        }
      }
      return $this->data;
    } else $this->error();
  }

  /**
   * Implements select();
   *
   * Knowledge is power.  Have some.
   *
   * @param mixed   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return void/mixed
   */
  public function select($statement, $instances = array()){
    if($statement){
      if(!empty($instances)){
        foreach($instances as $instance){
          $this->data[$instance] = $this->dbs[$instance]->select($statement);
        }
      } else {
        foreach($this->dbs as $key => $db){
          $this->data[$key] = $db->select($statement);
        }
      }
      return $this->data;
    } else $this->error();
  }

  /**
   * Implements update();
   *
   * Level up!
   *
   * @param array   $data         An array of data to update in FIELD => VALUE array.
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return void
   */
  public function update($data, $statement, $instances = array()){
    if($statement){
      if(!empty($instances)){
        foreach($instances as $instance){
          $this->data[$instance] = $this->dbs[$instance]->update($data, $statement);
        }
      } else {
        foreach($this->dbs as $key => $db){
          $this->data[$key] = $db->update($data, $statement);
        }
      }
    } else $this->error();
  }

  /**
   * Implements delete();
   *
   * Content can no longer be allowed to exist.
   *
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return void/mixed
   */
  public function delete($statement, $instances = array()){
    if($statement){
      if(!empty($instances)){
        foreach($instances as $instance){
          $this->data[$instance] = $this->dbs[$instance]->delete($statement);
        }
      } else {
        foreach($this->dbs as $key => $db){
          $this->data[$key] = $db->delete($statement);
        }
      }
      return $this->data;
    } else $this->error();
  }

  /**
   * Implements destroy();
   *
   * You can kill a connection.  You can't kill an idea.
   *
   * @param array   $instances    Used to kill one or more instances of a database, rather than all.
   * @return void/mixed
   */
  public function destroy($instances = array()){
    if(!empty($instances)){
      foreach($instances as $instance){
        $this->dbs[$instance]->destroy();
      }
    } else {
      foreach($this->dbs as $key => $db){
        $db->destroy();
      }
    }
    return $this->data;
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
} ?>

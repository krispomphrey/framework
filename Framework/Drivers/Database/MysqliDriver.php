<?php
/**
 * The MySQLi driver for the framework.
 * Class defines database and holds useful functions.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class MysqliDriver{
  private $config;
  private $client;
  public $db;

  /**
   * Implements __construct();
   *
   * The main Database constructor which is passed what database and credentials to use.
   *
   * @param string    $db_name        The database name that will be selected for this instance of the database object.
   * @param array     $credentials    The database details that are needed to run the database.
   */
  public function __construct($db_name, $credentials){
    $this->db = $db_name;
    $this->config = $credentials;
    $this->init();
  }

  /**
   * Implements init();
   *
   * Initialise the connection using the details provided in conf.
   */
  public function init(){
    $this->client = mysqli_connect($this->config['host'], $this->config['user'], $this->config['password']);
    if($this->client && !$this->client->connect_error){
      $this->client->select_db($this->db);
    } else {
      $this->client->error = "Could not connect to database.";
      $this->error();
    }
  }

  /**
   * Implements query();
   *
   * Knowledge is power.  Have some.
   *
   * @param string   $statement    The string that will have the SQL query in it.
   */
  public function query($statement){
    $results = $this->client->query($statement);
    var_dump($statement);
    var_dump($results);
    return $results;
  }

  /**
   * Implements insert();
   *
   * Level up!
   */
  public function insert($data, $statement){
    $sql = "INSERT INTO {{database}}.{{table}} ({{columns}}) VALUES ({{data}})";

    // Check to see if specified columns.
    if($data){
      $cols = '';
      $vals = '';
      $c = 0;

      foreach($data as $field => $value){
        $cols .= $this->client->escape_string($field);
        $vals .= "'".$this->client->escape_string($value)."'";
        if($c < count($data)-1){
          $cols .= ', ';
          $vals .= ', ';
        }
        $c++;
      }
    }

    // Replace the SQL with what has been generated above.
    $sql = str_replace(
      array('{{database}}', '{{table}}', '{{columns}}', '{{data}}'),
      array($this->db, $this->config['prefix'].$statement['table'], $cols, $vals),
      $sql
    );
    $this->query($sql);
    if($this->client->affected_rows >= 0){
      return true;
    } else {
      return false;
    }
  }

  /**
   * Implements select();
   *
   * Knowledge is power.  Have some.
   *
   * @param mixed     $statement    Either an array of conditions (array('table' => 'TABLE_NAME', 'cols' => array('col1', 'col2'), 'args' => array(array('column', 'condition', 'value', 'AND/OR')))) or a string with a valid SQL query.
   */
  public function select($statement){
    $results_objects = array();
    $cols = '';
    $where = '';

    // Build the query string with replacement tokens.
    $sql = "SELECT {{columns}} FROM {{database}}.{{table}} {{where}}";

    // See if we have used an array statement.
    if(is_array($statement)){
      // Grab the elements in the array.
      $skeys = array_keys($statement);

      // Check to see if specified columns.
      if(in_array('cols', $skeys)){
        // Check that there are multiple columsn in an array.
        if(is_array($statement['cols'])){
          $cols .= '(';
          // Loop through all the arguments and build the sql string.
            $c = 0;
          foreach($statement['cols'] as $col){
            $cols .= $this->client->escape_string($col);
            if($c < count($statement['cols'])-1){
              $cols .= ', ';
            }
            $c++;
          }
          $cols .= ')';
        } else {
          // A string of columns.
          $cols .= $statement['cols'];
        }
      } else {
        // No columns, use a wildcard.
        $cols .= '*';
      }

      // Check to see if specified arguments.
      if(in_array('args', $skeys)){
        $where .= 'WHERE ';
        // Check that there are multiple columsn in an array.
        if(is_array($statement['args'])){
          // Loop through all the arguments and build the sql string.
          foreach($statement['args'] as $arg){
            if(isset($arg[3])){
              $where .= $arg[3];
            }
            $where .= $this->client->escape_string($arg[0]).' '.$this->client->escape_string($arg[1])." '".$this->client->escape_string($arg[2])."'";
          }
        } else {
          // A string of conditions.
          $where .= $statement['args'];
        }
      }
      if(in_array('limit', $skeys)){
        $where .= ' LIMIT ';
        // Check that there are multiple columsn in an array.
        if(is_array($statement['limit'])){
          $where .= $this->client->escape_string($statement['limit'][0]).','.$this->client->escape_string($statement['limit'][1]);
        } else {
          // A string of conditions.
          $where .= $statement['limit'];
        }
      }
    } else {
      $where = $statement;
    }

    // Replace the SQL with what has been generated above.
    $sql = str_replace(
      array('{{columns}}', '{{database}}', '{{table}}', '{{where}}'),
      array($cols, $this->db, $this->config['prefix'].$statement['table'], $where),
      $sql
    );

    $results = $this->query($sql);

    if(is_object($results)){
      while($row = $results->fetch_object()){
        $results_objects[] = $row;
      }
    } else {
      $results_objects = false;
    }

    return $results_objects;
  }

  /**
   * Implements update();
   *
   * Level up!
   */
  public function update($data, $statement){
    $where = '';
    $sql = "UPDATE {{database}}.{{table}} SET {{data}} {{where}}";

    // See if we have used an array statement.
    if(is_array($statement)){
      // Grab the elements in the array.
      $skeys = array_keys($statement);

      // Check to see if specified columns.
      if($data){
        $c = 0;
        $data_string = '';
        foreach($data as $field => $value){
          $data_string .= $this->client->escape_string($field) ." = '". $this->client->escape_string($value)."'";
          if($c < count($data)-1){
            $data_string .= ', ';
          }
          $c++;
        }
      }

      // Check to see if specified arguments.
      if(in_array('args', $skeys)){
        $where .= 'WHERE ';
        // Check that there are multiple columns in an array.
        if(is_array($statement['args'])){
          // Loop through all the arguments and build the sql string.
          foreach($statement['args'] as $arg){
            $where .= (($arg[3] && count($statement['args']) > 1) ? $arg[3] : null);
            $where .= $this->client->escape_string($arg[0]).' '.$this->client->escape_string($arg[1])." '".$this->client->escape_string($arg[2])."'";
          }
        } else {
          // A string of conditions.
          $where .= $statement['args'];
        }
      }
    } else {
      $where = $statement;
    }

    // Replace the SQL with what has been generated above.
    $sql = str_replace(
      array('{{database}}', '{{table}}', '{{data}}', '{{where}}'),
      array($this->db, $this->config['prefix'].$statement['table'], $data_string, $where),
      $sql
    );

    $this->query($sql);
    if($this->client->affected_rows >= 0){
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
  public function delete($statement){
    $where = '';
    $sql = "DELETE FROM {{database}}.{{table}} {{where}}";
    if(is_array($statement)){
      // Grab the elements in the array.
      $skeys = array_keys($statement);

      // Check to see if specified arguments.
      if(in_array('args', $skeys)){
        $where .= 'WHERE ';
        // Check that there are multiple columns in an array.
        if(is_array($statement['args'])){
          // Loop through all the arguments and build the sql string.
          foreach($statement['args'] as $arg){
            $where .= (($arg[3] && count($statement['args']) > 1) ? $arg[3] : null);
            $where .= $this->client->escape_string($arg[0]).' '.$this->client->escape_string($arg[1])." '".$this->client->escape_string($arg[2])."'";
          }
        } else {
          // A string of conditions.
          $where .= $statement['args'];
        }
      }
    } else {
      $where = $statement;
    }

    // Replace the SQL with what has been generated above.
    $sql = str_replace(
      array('{{database}}', '{{table}}', '{{where}}'),
      array($this->db, $this->config['prefix'].$statement['table'], $where),
      $sql
    );

    $this->query($sql);

    if($this->client->affected_rows >= 0){
      return true;
    } else {
      return false;
    }
  }

  /**
   * Implements close();
   *
   * Close the DB.
   */
  public function close(){
    $this->client->close();
  }

  /**
   * Implements destroy();
   *
   * You can kill a connection.  You can't kill an idea.
   */
  public function destroy(){
    $this->client->close();
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

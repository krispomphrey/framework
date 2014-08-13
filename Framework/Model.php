<?php
/**
 * Framework Parent Model.
 *
 * This is the main model that is extended by user models and extends
 * the WebApp class, that sets up framework specific objects and functions.
 * Helper functions are set up for inheritance and are used for "data" specific things.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Model extends WebApp{
  /**
   * Holds the table that this model will use.
   * @var string
   */
	public $table;

  /**
   * Holds an array of other models that will be used within this model.
   * Important not to cause an infinite loop by reference a model that references this model.
   * @var array
   */
	public $models = array();

  /**
   * Holds the data this model will expose.
   * @var mixed
   */
	public $data;

  /**
   * Pagination information will be stored in this variable.
   * @var mixed
   */
	public $pagination;

  /**
   * Set the page_break e.g. the limit on records to show.
   * @var object
   */
	public $page_break = 50;

  /**
   * Implements __construct();
   *
   * The main Model constructor.
   *
   * This will invoke WebApp::__construct() so we can get access to the database,
   * current user and router from within the controller.
   *
   * @return void
   */
	public function __construct(){
    // Call the parent construct to get access to user, databases etc.
		parent::__construct();

    // Setup the other models so they are accessable inside this model.
		$this->other_models();

    // Fire the pagination (if there is a limit present) method so we can pull in correct details when limiting calls.
    if($this->page_break){
      $this->pagination();
    }

    // Fire init hook.
		$this->init();
	}

  /**
   * Implements init();
   *
   * Fired when a model is ready.
   */
	public function init(){ }

  /**
   * Implements create();
   *
   * Function is used to create a record in the database.
   * Model database tables will always have an autogenerated ID, as the primary key.
   * The table will also include a column for each of the following, please take this into consideration when creating tables:
   *
   * -crdate (created date)
   * -uddate (amended date)
   * -created_by (owner of the record)
   * -updated_by (last user who updated)
   *
   * This function automatically adds these in.
   *
   * The data, statement and instances are all passed to the Database controller.
   *
   * @param array   $data         An array of data to insert in FIELD => VALUE array.
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return mixed
   */
	public function create($data, $statement, $instances = null){
    $statement['table'] = $this->table;

    // Add the current time to the data.
    $data['crdate'] = time();
    $data['uddate'] = time();
    // Add the current user id to the data.
    $data['created_by'] = $this->user->session['fw']['id'];
    $data['updated_by'] = $this->user->session['fw']['id'];

    // Pass the amended variables to the database controller and return it.
    return $this->db->insert($data, $statement, $instances = null);
  }

  /**
   * Implements get();
   *
   * Function to pull out the data form the database.
   *
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return mixed
   */
	public function get($statement = array(),  $instances = null){
    $statement['table'] = $this->table;
    if($this->pagination){
      if(isset($this->user->session['fw']['db'])){
        $db = $this->user->session['fw']['db'];
      } else {
        $db = 0;
      }
      if(!isset($this->pagination[$db]['paginate'])){
        $start = 0;
      } else {
        $start = $this->pagination[$db]['paginate'];
      }
      $statement['limit'] = array($start, $this->page_break);
    }
		$this->data = $this->db->select($statement,  $instances = null);
    return true;
	}

  /**
   * Implements save();
   *
   * Function to save already existing data back into the database.
   *
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return mixed
   */
	public function save($data, $statement,  $instances = null){
    $statement['table'] = $this->table;

    // Add the current time to the data.
    $data['crdate'] = time();
    $data['uddate'] = time();
    // Add the current user id to the data.
    $data['created_by'] = $this->user->session['fw']['id'];
    $data['updated_by'] = $this->user->session['fw']['id'];

    // Pass the amended variables to the database controller and return it.
    return $this->db->update($data, $statement, $instances = null);
  }

  /**
   * Implements delete();
   *
   * Remove a record from the database. Record ID must be present in arguments.
   *
   * @param array   $statement    The statement to be passed to the driver.
   * @param array   $instances    Used to run statement on one or more instances of a database, rather than all.
   * @return mixed
   */
	public function delete($statement,  $instances = null){
    // Make sure we can't delete anything through the model that doesn't have an ID.
    // Stops the ability to delete everything by mistake.
    $statement['table'] = $this->table;
    if(!isset($statement['args']) || $statement['args'][0][0] != 'id'){
      return false;
    } else return $this->db->delete($statement, $instances);
  }

  /**
   * Implements other_models();
   *
   * This function sets up other models that will be used within this instance.
   *
   * @return void
   */
	private function other_models(){
		if(!empty($this->models)){
			foreach($this->models as $model){
				$this->incl(MODEL_ROOT."{$model}.php");
				$m = $model.'Model';
				$this->$model = new $m;
			}
		}
	}

  /**
   * Implements pagination();
   *
   * This is the pager function that will calculate splits and pages and limits for the database call.
   *
   * @return void
   */
	public function pagination($instances = null){
		if(isset($_GET['page'])){
			$page = $_GET['page'];
			$paginate = $this->page_break*($page-1);
		} else {
			$page = 1;
			$paginate = 0;
		}
		$results = $this->db->select(array('table' => $this->table, 'cols' => array('id')));
    foreach($results as $key => $db){
      $count = count($results);
      $this->pagination[$key] = array(
        'count' => $count,
        'pages' => ceil($count/$this->page_break),
        'current' => $page,
        'paginate' => $paginate
      );
    }
	}
}

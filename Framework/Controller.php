<?php
namespace Framework;

use Model;

/**
 * Framework Parent Controller.
 *
 * This is the main controller that is extended by user controllers and extends
 * the App class, that sets up framework specific objects and functions.
 * Helper functions are set up for inheritance and are used for "page" specific things.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Controller{
  /**
   * Holds the model object that is defined for this controller.
   * @var object
   */
	public $model;

  /**
   * Variable holds the current layout to use when rendering the page.
   * This defaults to 'index'.
   * @var string
   */
	public $layout = 'index';

  /**
   * A holder array for a queue of frontend assets (css/js) located in the assets folder.
   * @var array
   */
	public $queue = array();

  /**
   * Holds the view that will be passed to the layout.
   * @var object
   */
  public $view;

  /**
   * An array of messages to be output to the frontend.
   * @var array
   */
	public $messages = array();

	/**
   * Variable determines whether a controller is protected from logged out users.
   * This defaults to false.
   * @var boolean
   */
	public $protected = false;

  /**
   * A variable to determine whether to show a login on this page (only when $protected is true).
   * This defaults to false.
   * @var boolean
   */
	public $login = false;

  /**
   * Auth variable will hold allow and deny for each controller to set permissions based access
   * (when $protected is true).
   * @var array
   */
	public $auth = array();

  /**
   * Implements __construct();
   *
   * The main Controller constructor.
   *
   * This will invoke App::__construct() so we can get access to the database,
   * current user and router from within the controller.
   *
   * @return void
   */
	public function __construct(){
    $this->router = new Router();
    $this->user   = new Auth();

    // Call the pre_init hook form the children.
		$this->pre_init();

    // Run the request through the authentication method to see if it's needed.
		if($this->auth()){
      // Fire the init hook.
			$this->init();
		} else {
      // Check to see if a login screen is needed.
			if(isset($this->login) && $this->login){
        $this->render('login');
			} else {
        // Else we show an Access Denied page.
				$this->render('no-access');
			}
		}
	}

  /**
   * Implements pre_init();
   *
   * pre_init is fired before a page is rendered.
   * It is generally used to attach assets.
   */
	public function pre_init(){}

  /**
   * Implements init();
   *
   * Fired when a controller is ready.
   */
	public function init(){}

  /**
   * Implements auth();
   *
   * Function to check whether current user is worthy of accessing the controller.
   * Will check for the existance of allow and deny arrays.  If there, then the users
   * table will need to be present (to allow login).
   *
   * @return boolean
   */
	public function auth(){
    // Check to see if the controller is protected.
		if(isset($this->protected) && $this->protected){
      // Check to see if there are any ALLOW or DENY arrays present.
			if(!empty($this->auth) && is_array($this->auth)){
        // Loop through the allowed and denied ACLS (requires users table)
				foreach($this->auth as $type => $acls){
          // Check to see if the user has an acl.
					if(isset($this->user->session['fw']['acl']) && in_array($this->user->session['fw']['acl'], $acls)){
						switch($type){
							case 'allow' : return true;  break;
							case 'deny'  : return false; break;
						}
					} else {
						return false;
					}
				}
			} elseif(isset($this->user->loggedin) && $this->user->loggedin){
        // If there are no allow/deny arrays, but the controller is protected, check to see
        // if the current user is logged in.
				return true;
			} else{
        return false;
      }
		} else {
			return true;
		}
	}

  /**
   * Implements render();
   *
   * Render is the function that controls rendering a layout (which in turn
   * will render a view inside a layout).
   *
   * @param string  $view    Assigned to the object $this->view so that it can be accessed in the layout.
   */
	public function render($view){
    $this->view = $view;

		$path = LAYOUT_ROOT;
		$this->incl($path.$this->layout);
	}

  /**
   * Implements view();
   *
   * Gets the view file and includes it where needed.
   *
   * @param string  $view    Holds the view file (minus php) to include.
   */
	public function view($view){
		$path = VIEW_ROOT;
		$this->incl($path.$view);
	}

  /**
   * Implements layout();
   *
   * Includes the layout file defined.
   *
   * @param string  $layout    A string of the layout to be included.
   */
	public function layout($layout){
		$path = LAYOUT_ROOT;
		$this->incl($path.$layout);
	}

  /**
   * Implements model();
   *
   * Includes the model file defined.
   * Sets up the model in the context.
   *
   * @param string  $model    String holds what model to include and initialise.
   */
	public function model($model){
		$model = '\Model\\'.$model.'Model';
		$this->model = new $model();
	}

  /**
   * Implements asset();
   *
   * Add an asset (js/css) to the queue array.
   *
   * @param string  $type    The type of asset to include ('css' or 'js').
   * @param string  $file    The file to include.  This file should be available in the assets folder, but can be a subfolder.
   * @return void
   */
  public function asset($type, $file){
    $this->queue[$type][] = "$path/Assets/$type/$file";
  }

  /**
   * Implements flush_assets();
   *
   * Echos everything in the queue (js/css).
   *
   * @return void
   */
  public function flush_assets(){
    // If there is anything in the queue.
    if(!empty($this->queue) && is_array($this->queue)){
      foreach($this->queue as $key => $value){
        foreach($value as $q){
          switch($key){
            case 'css': echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"$q\" />\n"; break;
            case 'js': echo "<script src=\"$q\"></script>\n"; break;
          }
        }
      }
    }
  }

  /**
   * Implements incl();
   *
   * Include a custom php file (minus .php).
   *
   * @param string  $inc  The asset to include minus the .php
   * @return int/boolean  Will return 1 or false.
   */
  public function incl($inc, $once = true){
    // Make sure the file exists before including it.
    if(file_exists("{$inc}.php")){
      if($once){
        return include_once("{$inc}.php");
      } else{
        return include("{$inc}.php");
      }
    } else return false;
  }

  /**
   * Implements req();
   *
   * Require a custom php file (minus .php).
   *
   * @param string  $inc  The asset to include minus the .php
   * @return int/boolean  Will return 1 or false.
   */
  public function req($inc, $once = true){
    // Make sure the file exists before including it.
    if(file_exists("{$inc}.php")){
      if($once){
        return require_once("{$inc}.php");
      } else{
        return require("{$inc}.php");
      }
    } else return false;
  }
}

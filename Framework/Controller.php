<?php
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
class Controller extends App{
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
   * Holds the whether we are in admin or not in the layout.
   * @var boolean
   */
  public $admin;

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
    // Call the App constructor to get access to users, databases etc.
    parent::__construct();

    // Call the pre_init hook form the children.
		$this->pre_init();

    // If there is post data, check to see if we are logging in.
    if($_POST) $this->check_for_login($_POST);

    // Run the request through the authentication method to see if it's needed.
		if($this->auth()){
      // Fire the init hook.
			$this->init();
		} else {
      // Check to see if a login screen is needed.
			if(isset($this->login) && $this->login){
        // Check if it's the admin that is needed.
				if($this->router->controller == 'Admin'){
					$this->layout = 'login';
					$this->asset('css', 'login.css', true);
					$this->render('login', true);
				} else $this->render('login');
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
							case 'allow': return true; break;
							case 'deny': return false; break;
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
   * @param boolean $admin   Whether to use the admin specific layouts.
   */
	public function render($view, $admin = false){
    $this->view = $view;
    $this->admin = $admin;
		if($admin) $path = ADMIN_LAYOUT_ROOT;
		else $path = LAYOUT_ROOT;
		$this->incl($path.$this->layout);
	}

  /**
   * Implements view();
   *
   * Gets the view file and includes it where needed.
   *
   * @param string  $view    Holds the view file (minus php) to include.
   * @param boolean $admin   Whether to use the admin specific view.
   */
	public function view($view, $admin = false){
		if($admin) $path = ADMIN_VIEW_ROOT;
		else $path = VIEW_ROOT;
		$this->incl($path.$view);
	}

  /**
   * Implements layout();
   *
   * Includes the layout file defined.
   *
   * @param string  $layout    A string of the layout to be included.
   * @param boolean $admin   Whether to use the admin specific layouts.
   */
	public function layout($layout, $admin = false){
		if($admin) $path = ADMIN_LAYOUT_ROOT;
		else $path = LAYOUT_ROOT;
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
		$this->incl(MODEL_ROOT.$model);
		$model = $model.'Model';
		$this->model = new $model();
	}

  /**
   * Implements check_for_login();
   *
   * This is the catch for a posted page. The function expects at least $_POST['fw']['username']
   * and $_POST['fw']['password'].
   *
   * @param string  $data    $_POST sent through from constructor.
   */
  private function check_for_login($data){
    if(!$this->user->loggedin){
      // Check to see if the array fw[] is there which is used on all login forms.
      if(isset($data['fw']['username'])){
        // Pull in the Users model.
        $this->model('Users');

        // Get the correct data from the database.
        $this->model->get(array('args' => array(array('username', '=', $data['fw']['username']))));

        // If there is a response.
        if($this->model->data){
          foreach($this->model->data as $db => $results){
            $user = array_pop($results);
            if(!empty($user)){
              if($this->user->check_password($data['fw']['password'], $user->password)){
                $this->user->login(array(
                  'id' => $user->id,
                  'name' => $user->name,
                  'email' => $user->email,
                  'username' => $user->username,
                  'acl' => $user->acl,
                  'db' => $db,
                  'loggedin' => 1
                ));
                break;
              } else {
                $this->messages[] = array('type' => 'danger', 'notice' => 'Password is incorrect.');
              }
            } else {
              $this->messages[] = array('type' => 'danger', 'notice' => 'Username is incorrect.');
            }
          }
        }
      }
      $this->model = null;
    } else {
      $this->messages[] = array('type' => 'info', 'notice' => 'You are already logged in!');
    }
  }
}

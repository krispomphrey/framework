<?php
/**
 * Framework Parent Controller.
 *
 * This is the main controller that is extended by user controllers and extends
 * the WebApp class, that sets up framework specific objects and functions.
 * Helper functions are set up for inheritance and are used for "page" specific things.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Controller extends WebApp{
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
   * @var boolean
   */
	public $auth = array();

  /**
   * Implements __construct();
   *
   * The main Controller constructor.
   *
   * This will invoke WebApp::__construct() so we can get access to the database,
   * current user and router from within the controller.
   */
	public function __construct(){
    parent::__construct();
		$this->pre_init();
    if($_POST) $this->check_for_login($_POST);
		if($this->auth()){
			$this->init();
		} else {
			if(isset($this->login) && $this->login){
				if($this->router->controller == 'Admin'){
					$this->layout = 'login';
					$this->asset('css', 'login.css', true);
					$this->render('login', true);
				}
				else $this->render('login');
			} else {
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
   */
	public function auth(){
		if(isset($this->protected) && $this->protected){
			if(!empty($this->auth) && is_array($this->auth)){
				foreach($this->auth as $type => $acls){
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
  public function check_for_login($data){
    if(isset($data['fw']['username'])){
      $this->model('Login');
      $this->model->get(array('args' => array(array('username', '=', $data['fw']['username']))));
      if($this->model->data){
        $user = array_pop($this->model->data);
        $user = array_pop($user);
        if(!empty($user)){
          if($this->user->check_password($data['fw']['password'], $user->password)){
            $this->user->login($user);
          } else {
            $this->messages[] = array('type' => 'danger', 'notice' => 'Password is incorrect.');
          }
        } else {
          $this->messages[] = array('type' => 'danger', 'notice' => 'Username is incorrect.');
        }
      }
    }
    $this->model = null;
  }
}

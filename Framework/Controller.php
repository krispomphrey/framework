<?php
/**
 * Framework Parent Controller.
 * Parent controller that provides functions to children.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */

// Require the parent model as well for inheritance.
require_once('Model.php');

class Controller{
  // Define all of the variables that the children will need.
	public $model;
	public $view;
	public $layout = 'index';
	public $router;
	public $user;
	public $queue = array();
	public $messages = array();
	// Protect the controller (i.e. needs login).
	public $protected = 0;
	public $login = 0;
	// An array of ALLOW or DENY access levels.
	public $auth = array();

  /**
   * Implements debug();
   *
   * Function to output debug information.
   */
	public function debug($data){
		$config = new Config();
		if($config->debug == 1){
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
		}
	}

  /**
   * Implements __construct();
   *
   * The main Controller constructor.
   */
	public function __construct(&$db, &$router, &$auth){
		$this->db = &$db;
		$this->router = &$router;
		$this->user = &$auth;
		$this->pre_init();
		if($this->auth()){
			$this->init();
		} else {
			if(isset($this->login) && $this->login == 1){
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
   * Fired when a page is ready to be rendered.
   */
	public function init(){}

  /**
   * Implements auth();
   *
   * Function to check whether current user is worthy of viewing the content.
   */
	public function auth(){
		if(isset($this->protected) && $this->protected == 1 && $this->user->loggedin == 0){
			if(!empty($this->auth) && is_array($this->auth)){
				foreach($this->auth as $type => $acls){
					if(in_array($this->user->acl, $acls)){
						switch($type){
							case 'allow': return true; break;
							case 'deny': return false; break;
						}
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

  /**
   * Implements render();
   *
   * Render is used to include the layout difined in the context.
   */
	public function render($view, $admin = false){
		if($admin) $path = ADMIN_LAYOUT_ROOT;
		else $path = LAYOUT_ROOT;
		include_once($path."{$this->layout}.php");
	}

  /**
   * Implements view();
   *
   * Includes the view file defined.
   */
	public function view($view, $admin = false){
		if($admin) $path = ADMIN_VIEW_ROOT;
		else $path = VIEW_ROOT;
		include_once($path."{$view}.php");
	}

  /**
   * Implements layout();
   *
   * Includes the layout file defined.
   */
	public function layout($layout, $admin = false){
		if($admin) $path = ADMIN_LAYOUT_ROOT;
		else $path = LAYOUT_ROOT;
		include_once($path."{$layout}.php");
	}

  /**
   * Implements model();
   *
   * Includes the model file defined.
   * Sets up the model in the context.
   */
	public function model($model){
		include_once(MODEL_ROOT."{$model}.php");
		$model = $model.'Model';
		$this->model = new $model($this->db, $this->router, $this->user);
	}

  /**
   * Implements incl();
   *
   * Include a custom php file (minus .php).
   */
	public function incl($inc){
		include_once("{$inc}.php");
	}

  /**
   * Implements asset();
   *
   * Add an asset (js/css) to the queue array.
   */
	public function asset($type, $file, $admin = false){
		$path = null;
		if($admin) $path = '/Framework/Admin';
		$this->queue[$type][] = "$path/assets/$type/$file";
	}

  /**
   * Implements flush_queue();
   *
   * Echos everything in the queue (js/css).
   * TODO: Remove HTML from code.
   */
	public function flush_queue(){
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
}

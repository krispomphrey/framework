<?php
/**
 * Framework Router.
 *
 * The main class that controls the routing of Framework.  The router will split
 * the requested url into controller and actions.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Router{
  /**
   * Route holds the route that was requested by the user.
   * @var string
   */
	public $route;

  /**
   * Store all the headers from the current request.
   * @var array
   */
  public $headers;

  /**
   * Variable to hold the controller for the current request.
   * @var string
   */
	public $controller;

  /**
   * Variable that hold everything after the controller in the request.
   * @var array
   */
	public $action;

  /**
   * An array of paths to ignore (i.e. to goto without running through the router).
   * @var array
   */
	public $ignore;

  /**
   * Implements __construct();
   *
   * The router constructor.
   * This calls the build_root() function as well as assigning all the headers so they are
   * available to all and also sets the base url based on whether it's in the config.
   */
	public function __construct(){
		$this->build_route();
    $this->headers = getallheaders();
		$this->ignore = array('Uploads', 'assets', 'Framework/admin', 'Favicon.ico', 'docs', 'output');
    $config = new Config();
    if($config->base_url){
      $this->base_url = $config->base_url;
    } else {
      $this->base_url = $_SERVER['HTTP_HOST'];
    }
	}

  /**
   * Implements build_route();
   *
   * The main function in the router, it takes the request from the user and splits
   * it out into usable variables.
   */
	private function build_route(){
		$this->route = substr($_SERVER['PHP_SELF'], strlen('/index.php/'));
		if($this->route){
			$parts = explode('/',$this->route);
			if(strpos($parts[0], '-') !== false){
				$control_parts = explode('-', $parts[0]);
				foreach($control_parts as &$prt){
					$prt = ucwords($prt);
				}
				$control = implode('', $control_parts);
			} else {
				$control = ucwords($parts[0]);
			}
			$this->controller = $control;
			unset($parts[0]);
		 	$this->action = $parts;
		} else {
			$this->controller = 'Index';
		}
	}

  /**
   * Implements header();
   *
   * Function to set custom headers in the app so headers can be controlled.
   * @param mixed  $header    An array/string of headers to be added to app.
   */
  public function header($header){
    if(is_array($header)){
      foreach($header as $head){
        header($head);
      }
    } else header($header);
  }

  /**
   * Implements redirect();
   *
   * A custom function to allow quick redirects.
   *
   * @param string  $url    A string determining what path to goto.
   */
	public function redirect($url){
    ob_clean();
		$this->header("Location: {$url}");
	}
}

/**
 * Define constants to the different directories needed.
 */

// Framework constants.
define('FW_ROOT', DIR_ROOT.'/Framework/');
define('ADMIN_ASSETS_ROOT', FW_ROOT.'admin/assets/');
define('ADMIN_VIEW_ROOT', FW_ROOT.'admin/View/');
define('ADMIN_LAYOUT_ROOT', FW_ROOT.'admin/Layout/');

// Site constants.
define('ASSETS_ROOT', DIR_ROOT.'/Assets/');
define('VIEW_ROOT', DIR_ROOT.'/View/');
define('MODEL_ROOT', DIR_ROOT.'/Model/');
define('LAYOUT_ROOT', DIR_ROOT.'/Layout/');
define('CONTROLLER_ROOT', DIR_ROOT.'/Controller/');
define('UPLOAD_ROOT', DIR_ROOT.'/Uploads/');

define('ASSETS_URL', BASE_URL.'/assets/');
define('UPLOAD_URL', BASE_URL.'/Uploads/');
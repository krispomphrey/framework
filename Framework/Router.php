<?php
namespace Framework;

use Framework\Config;

use Controllers;

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
   * An array of request parameters
   * @var array
   */
  public $request;

  /**
   * Implements __construct();
   *
   * The router constructor.
   * This calls the build_root() function as well as assigning all the headers so they are
   * available to all and also sets the base url based on whether it's in the config.
   */
	public function __construct(){
    $config = new Config();

		$this->build_route();
    $this->headers = getallheaders();
    $this->request = $_REQUEST;

    // Merge some things into the array that definetly need to be ignored.
		$this->ignore = array_merge(array('Uploads', 'Assets', 'Framework/Admin', 'Favicon.ico', 'docs', 'output'), $config->ignore);

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
    // Grab the parts from the rewrite.
		$this->route = substr($_SERVER['PHP_SELF'], strlen('/index.php/'));

    // If anything is after the base url.
		if($this->route){
      // Get rid of the foward slash and grab all the url parts.
			$parts = explode('/', $this->route);

      // The first part will always be a controller.
      $control = $parts[0];
      // Make sure the parts are in an array otherwise the app will fail.
      if(is_array($parts)){
        // Run through all the parts and format them properly.
        foreach($parts as &$part){
          if(strpos($part, '-') !== false){
            $control_parts = explode('-', $part);
            foreach($control_parts as &$prt){
              $prt = ucwords($prt);
            }
            $part = implode('', $control_parts);
          } else {
            $part = ucwords($part);
          }
        }

        // Loop through the parts and test to see if the controller is "Part" or "PartPart".
        for($c = count($parts); $c > 0; $c--){
          $con_test = array();
          for($i = 0; $i < $c; $i++){
            $con_test[] = $parts[$i];
          }
          foreach($con_test as &$ct){
            $ct = ucwords($ct);
          }
          $test = implode('', $con_test);

          // Check to see if the class has been auto loaded.
          if(class_exists("{$test}Controller")){
            for($i = 0; $i < $c; $i++){
              unset($parts[$i]);
            }
            $control = $test;
            break;
          } else {
            $control = null;
          }
        }
      }

      // Get the final control and reassign it to the router controller.
			$this->controller = $control;

      // Reset the action positions.
		 	$this->action = array_map('strtolower', $parts);
		} else {
      // Use the default controller.
			$this->controller = 'Index';
		}

    // Append the controller suffix so it can be used.
    $this->controller .= 'Controller';
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
define('FW_ROOT',         DIR_ROOT.'/Framework/');

// Site constants.
define('CONFIG_ROOT',     DIR_ROOT.'/Config/');
define('ASSETS_ROOT',     DIR_ROOT.'/Assets/');
define('VIEW_ROOT',       DIR_ROOT.'/View/');
define('MODEL_ROOT',      DIR_ROOT.'/Model/');
define('LAYOUT_ROOT',     DIR_ROOT.'/Layout/');
define('CONTROLLER_ROOT', DIR_ROOT.'/Controller/');
define('UPLOAD_ROOT',     DIR_ROOT.'/Uploads/');

// Default root for Doctrine Entities
define('ENTITY_ROOT',     MODEL_ROOT.'/Entity/');

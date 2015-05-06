<?php

namespace Framework;

use Framework\AutoLoader;

//use Framework\Core\Config;
use Framework\Router;
use Framework\Database;
use Framework\Auth;
use Framework\Controller;
use Framework\Model;

// Load the custom built controllers.
use Controllers;

/**
 * Framework.
 *
 * @version 0.1.3
 *
 * The base class that holds all the objects (and keys) for the controllers,
 * models and views to gain access to by extension.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class App{
  /**
   * Variable that will hold the app config.
   * @var object
   */
  public $config;
  /**
   * Variable that will hold the DB connection.
   * @var object
   */
	public $db;

  /**
   * Variable that will hold the router.
   * @var object
   */
	public $router;

  /**
   * Variable that will hold the mailer.
   * @var object
   */
  public $mail;

  /**
   * Variable that will hold the user auth.
   * @var object
   */
	public $user;

  /**
   * Variable holds the file operations for the app.
   * @var object
   */
  public $file;

  /**
   * Implements __construct();
   *
   * The main App constructor.
   * This function builds the main helper objects used by the app.
   *
   * @return void
   */
	public function __construct(){
    // Assign helpers to variables.
		$this->db = new Database();
		$this->router = new Router();
		$this->user = new Auth();
    $this->config = new Config();
    $this->mail = new Mail();
	}

  /**
   * Implements debug();
   *
   * Function to output debug in a cleaner format if debug is switched on in the config.
   *
   * @param mixed   $data   The data to be output to the page.
   * @return void
   */
  public function debug($data){
    // Allow prettier output of var_dump.
    if($this->config->debug){
      print_r('<pre>');
      var_dump($data);
      print_r('</pre>');
    }
  }

  /**
   * Implements go();
   *
   * This holds the meat of the app, setting up the controller to use.
   * Function uses router to get correct data to render.
   *
   * It should be called in the index.php in docroot ONLY.
   *
   * 404 happens here.
   *
   * @return void
   */
	public function go(){
    // Make sure that the path isn't in the ignore array (i.e. assets);
    if(!in_array($this->router->controller, $this->router->ignore)){
      // If there is no path, we are on the index page.  Show it!.
		 	if(!isset($this->router->controller) || empty($this->router->controller)){ $this->router->controller = 'Index'; }
      // Check our controllers for the path.
      // TODO: See above.
			if($this->incl(DIR_ROOT."/Controller/{$this->router->controller}") != 1){
        // If controller isn't present, use the default controller.
				$controller = 'Controller';
				$control = new $controller();

        // Add our bootstrap and 404 css files for correct styling.
				$this->asset('css', 'bootstrap.min.css', true);
				$this->asset('css', '404.css', true);

        // Set the header to be 404.
				$this->router->header('HTTP/1.0 404 Not Found');

        // Check if there is a custom 404 page, or use the stock one.
				if(file_exists(LAYOUT_ROOT.'404.php')){
					$control->layout('404');
				} else {
					$this->incl(FW_ROOT.'static/404');
				}
			} else {
        // Create the new controller and invoke it.
				$controller = "{$this->router->controller}Controller";
				$control = new $controller();
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

  /**
   * Implements asset();
   *
   * Add an asset (js/css) to the queue array.
   *
   * @param string  $type    The type of asset to include ('css' or 'js').
   * @param string  $file    The file to include.  This file should be available in the assets folder, but can be a subfolder.
   * @param boolean $admin   Whether to use an admin specific asset.
   * @return void
   */
  public function asset($type, $file, $admin = false){
    $path = null;
    // If we are looking for admin assets.
    if($admin) $path = '/Framework/Admin';
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
}

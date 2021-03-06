<?php

namespace Framework;

use Controller;

/**
 * Framework.
 *
 * @version 0.1.4
 *
 * The main part of the app.
 * This makes sure that the right router is being used.
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
   * Variable that will hold the router.
   * @var object
   */
	public $router;

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
    $this->config = new Config();
		$this->router = new Router();
	}

  /**
   * Implements debug();
   *
   * Function to output debug in a cleaner format if debug is switched on in the config.
   *
   * @param mixed   $data   The data to be output to the page.
   * @return void
   */
  public function debug($data, $die = true){
    // Allow prettier output of var_dump.
    if($this->config->debug){
      print_r('<pre>');
      var_dump($data);
      print_r('</pre>');

      if($die) App::kill();
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
			if(!class_exists($this->router->controller) && $this->router->controller == 'Controller'){

        // Set the header to be 404.
				$this->router->header('HTTP/1.0 404 Not Found');

        // Use the base controller.
        $controller = '\Framework\\'.$this->router->controller;
        $control = new $controller();

        // Check if there is a custom 404 page, or use the stock one.
				if(file_exists(LAYOUT_ROOT.'404.php')){
					$control->layout('404');
				} else {
					App::kill('Page Not Found.');
				}
			} else {
        // Create the new controller and invoke it.
				$controller = "\Controller\\".$this->router->controller;

				$control = new $controller();
			}
		}
	}

  /**
   * Implements die();
   *
   * A custom die function.
   *
   * @param string  $message  A custom message to die with.
   * @return null
   */
  public static function kill($message = null){
    die($message);
  }
}

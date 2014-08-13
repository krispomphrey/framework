<?php
// Require the different parts of the framework.
require_once('Database.php');
require_once('Router.php');
require_once('Auth.php');
require_once('Mail.php');
require_once('Controller.php');

/**
 * The main WebApp that handles rendering page and
 * constructing all the elements to build the app.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class WebApp{
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
  * Variable that will hold the user auth.
  * @var object
  */
	public $user;

  /**
   * Implements __construct();
   *
   * The main WebApp constructor.
   * This function builds the 3 main objects used by the app.
   */
	public function __construct(){
    // Assign helpers to variables.
		$this->db = new Database();
		$this->router = new Router();
		$this->user = new Auth();

		$this->render_page();
	}

  /**
   * Implements render_page();
   *
   * Function uses router to get correct data to render.
   * Handles 404 as well.
   *
   * @todo: user specified paths.
   */
	public function render_page(){
    // Make sure that the path isn't in the ignore array (i.e. assets);
    if(!in_array($this->router->controller, $this->router->ignore)){
      // If there is no path, we are on the index page.  Show it!.
		 	if(!isset($this->router->controller) || empty($this->router->controller)){ $this->router->controller = 'Index'; }
      // Check our controllers for the path.
      // TODO: See above.
			$page_check = include_once(DIR_ROOT."/Controller/{$this->router->controller}.php");
			if($page_check != 1){
        // If controller isn't present, use the default controller.
				$controller = 'Controller';
				$control = new $controller($this->db, $this->router, $this->user);

        // Add our bootstrap and 404 css files for correct styling.
				$control->asset('css', 'bootstrap.min.css', true);
				$control->asset('css', '404.css', true);
				header('HTTP/1.0 404 Not Found');
				if(file_exists(LAYOUT_ROOT.'404.php')){
					$control->layout('404');
				} else {
					$control->incl(FW_ROOT.'static/404');
				}
				echo $control->view;
			} else {
				$controller = $this->router->controller.'Controller';
				$control = new $controller($this->db, $this->router, $this->user);
				echo $control->view;
			}
		}
	}
}

// Set the default date/time to avoid errors.
// TODO: Possibly move into settings to allow user to change.
date_default_timezone_set("Europe/London");



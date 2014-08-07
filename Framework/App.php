<?php
// Require the different parts of the framework.
require_once('Database.php');
require_once('Router.php');
require_once('Auth.php');
require_once('Mail.php');
require_once('Controller.php');
require_once('Model.php');
require_once(DIR_ROOT.'/Config/settings.php');

/**
 * Frameworks WebApp Class.
 *
 * The base class that holds all the objects (and keys) for the controllers,
 * models and views to gain access to by extension.§
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class WebApp{
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
   * Variable that will hold the user auth.
   * @var object
   */
	public $user;

  /**
   * Implements __construct();
   *
   * The main WebApp constructor.
   * This function builds the main helper objects used by the app.
   */
	public function __construct(){
    // Assign helpers to variables.
		$this->db = new Database();
		$this->router = new Router();
		$this->user = new Auth();
    $this->config = new Config();
	}

  /**
   * Implements debug();
   *
   * Function to output debug in a cleaner format.
   */
  public function debug($data){
    if($this->config->debug){
      echo '<pre>';
      var_dump($data);
      echo '</pre>';
    }
  }

  /**
   * Implements render_page();
   *
   * This holds the meat of the app, setting up the controller to use.
   * Function uses router to get correct data to render.
   *
   * It should be called in the index.php in docroot ONLY.
   *
   * 404 happens here.
   *
   * @todo: user specified paths from admin page.
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
				$this->router->header('HTTP/1.0 404 Not Found');
				if(file_exists(LAYOUT_ROOT.'404.php')){
					$control->layout('404');
				} else {
					$control->incl(FW_ROOT.'static/404');
				}
			} else {
				$controller = $this->router->controller.'Controller';
				$control = new $controller($this->db, $this->router, $this->user);
			}
		}
	}
}

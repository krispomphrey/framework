<?php
// Require the different parts of the framework.
require_once(DIR_ROOT.'/Config/settings.php');
require_once('Database.php');
require_once('Router.php');
require_once('Auth.php');
require_once('Mail.php');
require_once('Controller.php');
require_once('Model.php');

/**
 * Frameworks WebApp Class.
 *
 * The base class that holds all the objects (and keys) for the controllers,
 * models and views to gain access to by extension.§
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
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
   * Function to output debug in a cleaner format if debug is switched on in the config.
   *
   * @param mixed   $data   The data to be output to the page.
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
   * @todo user specified paths from admin page.
   */
	public function render_page(){
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
				$this->router->header('HTTP/1.0 404 Not Found');
				if(file_exists(LAYOUT_ROOT.'404.php')){
					$control->layout('404');
				} else {
					$this->incl(FW_ROOT.'static/404');
				}
			} else {
				$controller = $this->router->controller.'Controller';
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
   */
  public function incl($inc){
    return include_once($inc.".php");
  }

  /**
   * Implements asset();
   *
   * Add an asset (js/css) to the queue array.
   *
   * @param string  $type    The type of asset to include ('css' or 'js').
   * @param string  $file    The file to include.  This file should be available in the assets folder, but can be a subfolder.
   * @param boolean $admin   Whether to use an admin specific asset.
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
<?php
class Router{
	public $route;
  public $headers;
	public $controller;
	public $action;
	public $ignore;
	public function __construct(){
		$this->build_route();
    $this->headers = getallheaders();
		$this->ignore = array('Uploads', 'assets', 'Framework/admin', 'Favicon.ico');
	}
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

  public function header($header){
    header($header);
  }
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

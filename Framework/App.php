<?php
require_once('Database.php');
require_once('Router.php');
require_once('Auth.php');
require_once('Mail.php');
require_once('Controller.php');
class WebApp{
	public $db;
	public $router;
	public $user;
	public function __construct(){
		$this->db = new Database();
		$this->router = new Router();
		$this->user = new Auth();
		$this->render_page();
	}
	public function render_page(){
		 if(!in_array($this->router->controller, $this->router->ignore)){
		 	if(!isset($this->router->controller) || empty($this->router->controller)){ $this->router->controller = 'Index'; }
			$page_check = include_once(DIR_ROOT."/Controller/{$this->router->controller}.php");
			if($page_check != 1){
				ob_clean();
				Router::redirect('/not-found');
			} else {
				$controller = $this->router->controller.'Controller';
				$control = new $controller($this->db, $this->router, $this->user);
				echo $control->view;
			}
		}
	}
}
date_default_timezone_set("Europe/London");
define('ASSETS_ROOT', DIR_ROOT.'/Assets/');
define('VIEW_ROOT', DIR_ROOT.'/View/');
define('MODEL_ROOT', DIR_ROOT.'/Model/');
define('LAYOUT_ROOT', DIR_ROOT.'/Layout/');
define('CONTROLLER_ROOT', DIR_ROOT.'/Controller/');
define('UPLOAD_ROOT', DIR_ROOT.'/Uploads/');
define('ADMIN_ASSETS_ROOT', DIR_ROOT.'/Framework/admin/assets/');
define('ADMIN_VIEW_ROOT', DIR_ROOT.'/Framework/admin/View/');
define('ADMIN_LAYOUT_ROOT', DIR_ROOT.'/Framework/admin/Layout/');
<?php
require_once('Model.php');
class Controller{
	public $model;
	public $view;
	public $layout = 'index';
	public $router;
	public $user;
	public $queue = array();
	public $messages = array();
	// Protect the controller (i.e. needs login)
	public $protected = 0;
	public $login = 0;
	// An array of ALLOW or DENY access levels
	public $auth = array();
	public function debug($data){
		$config = new Config();
		if($config->debug == 1){
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
		}
	}
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
	public function pre_init(){}
	public function init(){}
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
	public function render($view, $admin = false){
		if($admin) $path = ADMIN_LAYOUT_ROOT;
		else $path = LAYOUT_ROOT;
		include_once($path."{$this->layout}.php");
	}
	public function view($view, $admin = false){
		if($admin) $path = ADMIN_VIEW_ROOT;
		else $path = VIEW_ROOT;
		include_once($path."{$view}.php");
	}
	public function layout($layout, $admin = false){
		if($admin) $path = ADMIN_LAYOUT_ROOT;
		else $path = LAYOUT_ROOT;
		include_once($path."{$layout}.php");
	}
	public function model($model){
		include_once(MODEL_ROOT."{$model}.php");
		$model = $model.'Model';
		$this->model = new $model($this->db, $this->router, $this->user);
	}
	public function asset($type, $file, $admin = false){
		$path = null;
		if($admin) $path = '/Framework/Admin';
		$this->queue['css'][] = "$path/assets/$type/$file";
	}
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

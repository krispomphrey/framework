<?php
class Router{
	public $route;
	public $controller;
	public $action;
	public $ignore;
	public function __construct(){
		$this->build_route();
		$this->ignore = array('Uploads', 'Assets', 'Framework/admin');
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
	public function redirect($url){
		header("Location: $url");
	}
}

<?php
/**
 * Admin Controller.
 *
 * The controller for the Admin page.  This provides a good starting point for
 * a CMS like experience.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class AdminController extends Controller{
	public $protected = true;
	public $login = true;
	public $auth = array('allow' => array(99));
	public function pre_init(){
		$this->asset('css', 'bootstrap.min.css', true);
		$this->asset('css', 'template.css', true);
	}
	public function init(){
    if(isset($this->router->action[1])){
  		switch($this->router->action[1]){
  			case 'content': $this->render('content', true);
  			case 'pages': $this->render('users', true);
  			case 'users': $this->render('users', true);
  			case 'settings': $this->render('settings', true);
  			default: $this->render('dashboard', true);
  		}
  	} else $this->render('dashboard', true);
  }
}

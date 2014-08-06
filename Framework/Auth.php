<?php
/**
 * Framework Authentication Handler.
 * Anything authentication based in this class.
 *
 * @todo: Auth tokens.
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Auth{
  // An auth obj will always start with a user logged out.
	public $loggedin = 0;
	public $id = 0;
  public $name = '';
  public $username = '';
  public $acl = 0;
  public $acllabel = '';

   /**
   * Implements __construct();
   *
   * The main Auth constructor.
   * Starts sessions.
   */
	public function __construct(){
		if(!isset($_SESSION)){
		    session_start();
		}
		if(isset($_SESSION['auth']['loggedin'])){
			$this->init_user();
		} else {
			$this->loggedin = 0;
		}
	}

   /**
   * Implements login();
   *
   * The actual login of a user. Sets session keys.
   * @param array $user_data
   * @return bool
   */
	public function login($user_data){
		$_SESSION['auth']['loggedin'] = 1;
		foreach($user_data as $key => $udata){
			$_SESSION['auth'][$key] = $udata;
		}
		unset($_SESSION['auth']['password']);
		$this->init_user();
		return true;
	}

  /**
   * Implements init_user();
   *
   * Places the session in the Auth object.
   * Referenced by value.
   */
	public function init_user(){
		foreach($_SESSION['auth'] as $key => &$value){
			$this->$key = $value;
		}
	}

  /**
   * Implements logout();
   *
   * Remove the logged in session and set the Auth object to signed out.
   * @param str $url
   */
	public function logout($url){
		unset($_SESSION['auth']['loggedin']);
		$this->loggedin = 0;
		Router::redirect($url);
	}
} ?>

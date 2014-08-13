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
  /**
   * Variable holds whether the current user is logged in or not.
   * @var int
   */
	public $loggedin = 0;

  /**
   * Variable holds current users session.
   * @var mixed
   */
  public $session;

   /**
   * Implements __construct();
   *
   * The main Auth constructor.
   * Starts sessions.
   */
	public function __construct(){
    if(!$_SESSION) session_start();
    $this->session = &$_SESSION;
    $this->loggedin = &$_SESSION['fw']['loggedin'];
  }

  public function login($data){
    if($data){
      foreach($data as $key => $value){
        $this->session['fw'][$key] = $value;
      }
    }
  }

  public function logout(){
    $this->loggedin = 0;
    unset($this->session['fw']);
  }

  public function create_user($data){
    if($data){
      return password_hash($data['password']);
    }
  }

  private function generate_token(){ }

  private function check_token(){ }
} ?>

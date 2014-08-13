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
    if($this->session['fw']['loggedin'] && $this->session['fw']['loggedin'] == 1){
      $this->loggedin = 1;
    }
  }

  public function login($data){
    if($data){
      foreach($data as $key => $value){
        $this->session['fw'][$key] = $value;
      }
      $this->loggedin = 1;
    }
  }

  public function logout(){
    $this->loggedin = 0;
    unset($this->session['fw']);
  }

  public function create_user($data){
    if($data){
      return array(
        'crdate' => time(),
        'username' => $data['username'],
        'password' => password_hash($data['password'], PASSWORD_BCRYPT)
      );
    }
  }

  public function check_password($password, $hashed){
    if($password && $hashed){
      return password_verify($password, $hased);
    } else {
      return false;
    }
  }

  private function generate_token(){ }

  private function check_token(){ }
} ?>

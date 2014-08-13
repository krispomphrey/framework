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
   * @var boolean
   */
	public $loggedin = false;

  /**
   * Variable holds current users session and is a reference to $_SESSION.
   * @var mixed
   */
  public $session;

   /**
   * Implements __construct();
   *
   * The main Auth constructor.
   * Starts sessions.
   *
   * @return void
   */
	public function __construct(){
    // If the session hasn't already been started in the app, start it now.
    if(!isset($_SESSION)) session_start();

    // Assign a reference to $_SESSION with the public class variable.
    $this->session = &$_SESSION;

    // Check to see if the user is currently has a session and switch to logged in.
    if(isset($this->session['fw']['loggedin'])){
      switch($this->session['fw']['loggedin']){
        case 0:
          $this->session['fw']['loggedin'] = false;
          // Give a logged out user a nice name.
          $this->session['fw']['name'] = 'Guest';
          break;
        case 1:
          $this->session['fw']['loggedin'] = true;
          break;
      }
    } else {
      $this->session['fw']['loggedin'] = false;
    }
    $this->loggedin = &$this->session['fw']['loggedin'];
  }

  /**
   * Implements login();
   *
   * A helper function to set the users session and logged in variable
   * Simply sets the correct session variables and switches the current user to loggedin.
   *
   * @return void
   */
  public function login($data){
    if($data){
      foreach($data as $key => $value){
        $this->session['fw'][$key] = $value;
      }
      $this->loggedin = 1;
    }
  }

  /**
   * Implements logout();
   *
   * Logout function simply removes the users session and switches the name to Guest.
   *
   * @return void
   */
  public function logout(){
    $this->loggedin = false;
    unset($this->session['fw']);
    $this->session['fw']['name'] = 'Guest';
  }

  /**
   * Implements create_user();
   *
   * This function helps the app create a standard user.  Framework expects a table
   * named users with at least 3 columns: crdate, username, password.  The current
   * request time is used, along with the user generated username (checked for letters, hypens
   * periods, underscores and numbers only, no spaces) and a properly hashed password.
   *
   * @uses $this->generate_password().
   *
   * @return array/boolean A partial user array, ready to be entered into a database or false.
   */
  public function create_user($data){
    // Make sure we have data.
    if($data){
      // Validate the username and password to make sure it conforms to our pattern.
      if($this->validate($data['username']) && $this->validate($data['password'])){
        return array(
          'crdate' => time(),
          'username' => $data['username'],
          'password' => $this->generate_password($data['password'])
        );
      } else return false;
    }
  }

  public function generate_password($password){
    if($password){
      return password_hash($password, PASSWORD_BCRYPT, array('cost' => 11));
    }
  }

  public function check_password($password, $hashed){
    if($password && $hashed){
      return password_verify($password, $hashed);
    } else {
      return false;
    }
  }

  private function validate($data){
    if(preg_match('/[a-z._\-0-9]/i', $data)){
      return true;
    } else {
      return false;
    }
  }
} ?>

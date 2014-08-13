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
      // Switch whether the user is logged in or not.
      switch($this->session['fw']['loggedin']){
        case false:
          // Set the logged in as false.
          $this->session['fw']['loggedin'] = false;
          // Give a logged out user a nice name.
          $this->session['fw']['name'] = 'Guest';
          break;
        case true:
          // Set the session to true again.
          $this->session['fw']['loggedin'] = true;
          break;
      }
    } else {
      // Set the logged in to false as default.
      $this->session['fw']['loggedin'] = false;
    }

    // Reference the session variable to an easier to use object variable.
    $this->loggedin = &$this->session['fw']['loggedin'];
  }

  /**
   * Implements login();
   *
   * A helper function to set the users session and logged in variable
   * Simply sets the correct session variables (determined by the user) and switches
   * the current user to loggedin.
   *
   * @return void/boolean
   */
  public function login($data){
    // Make sure there is actually data.
    if($data){
      // Loop through the array and set the session variables.
      foreach($data as $key => $value){
        $this->session['fw'][$key] = $value;
      }

      // Set the user logged in.
      $this->loggedin = true;
    } else {
      return false;
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
    // Log out the user.
    $this->loggedin = false;

    //unset all the session variables.
    unset($this->session['fw']);

    // Switch the name
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

  /**
   * Implements check_password();
   *
   * Function will check a plain text password against a hashed password if both are present.
   *
   * @return boolean
   */
  public function check_password($password, $hashed){
    if($password && $hashed){
      return password_verify($password, $hashed);
    } else {
      return false;
    }
  }

  private function generate_password($password){
    if($password){
      return password_hash($password, PASSWORD_BCRYPT, array('cost' => 11));
    }
  }

  /**
   * Implements validate();
   *
   * Simply validates input to make sure that it conforms to standards.
   * The regex pattern allows the following:
   * - Alphanumeric Characters
   * - Hyphens
   * - Underscores
   * - Periods
   *
   * @return boolean
   */
  private function validate($data){
    if(preg_match('/[a-z._\-0-9]/i', $data)){
      return true;
    } else {
      return false;
    }
  }
} ?>

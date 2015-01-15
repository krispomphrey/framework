<?php
/**
 * Framework Configuration.
 *
 * All the configuration for the framework is held here.
 *
 * @package   Framework
 * @author    Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Config{
  public function __construct(){
  	/* An example of how to register a database.
     * For multiple databases, add to the array with 'db_name' => 'options'.  All options are needed, even if blank.
     */
    /*
    $this->dbs = array(
      'fw_data' => array(
        'type' => 'mysqli',
        'user' => 'DB_USER',
        'password' => 'DB_PASSWORD',
        'host' => 'localhost',
        'prefix' => ''
      )
    );
    */

    $this->dbs = array(
      'fw_data' => array(
        'type' => 'mysqli',
        'user' => 'root',
        'password' => 's10052',
        'host' => 'localhost',
        'prefix' => ''
      )
    );

    /* An example of how to setup the SMTP settings for the mailer.
     * If no settings are found the local mail server will be used.
     */
    /*
    $this->smtp = array(
      'host' => array(
        'smtp1.example.com',
        'smtp2.example.com'
      ),
      'auth' => array(
        'enabled' => true,
        'credentials' => array(
          'username' => 'user@example.com',
          'password' => 'secret'
        ),
        'secure' => 'tls'
      ),
    );
    */

    // Whether to output debugging information or not.
  	$this->debug = true;

    // Whether to expose the logger to the app.
    $this->logging = false;

    // Sets the base url of the site.  Leave as default for multi domain sites.
    $this->base_url = $_SERVER['HTTP_HOST'];

    // Set an array of ignore controllers (i.e. paths), or leave the array empty to default.
    $this->ignore = array();
  }
}

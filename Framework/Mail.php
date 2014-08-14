<?php
// Pull in the mailer plugin.
require_once 'plugins/PHPMailer/PHPMailerAutoload.php';
/**
 * Framework Mailer.
 *
 * The mailer uses PHPMailer to simplify the process of sending mail from the system.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Mail{
  /**
   * An array with name and email keys pairs. array('name' => 'xxx', 'email' => 'xxxx');
   * @var array
   */
  public $from = array();

  /**
   * An array with name and email keys pairs. array('name' => 'xxx', 'email' => 'xxxx');
   * @var array
   */
  public $reply = array();

  /**
   * An array of arrays holding email and name key pairs. array(array('name' => 'xxx', 'email' => 'xxxx'));
   * @var array
   */
  public $to = array();

  /**
   * An array of arrays holding email and name key pairs. array(array('name' => 'xxx', 'email' => 'xxxx'));
   * @var array
   */
  public $cc = array();

  /**
   * An array of arrays holding email and name key pairs. array(array('name' => 'xxx', 'email' => 'xxxx'));
   * @var array
   */
  public $bcc = array();

  /**
   * An array of attachements. array('path/to/attachment', 'second/attachment');
   * @var array
   */
  public $attach = array();

  /**
   * Enter the subject line here.  IF this is empty "(no subject)" will be added.
   * @var string
   */
  public $subject;

  /**
   * Add an HTML body to the mail message.
   * @var string
   */
  public $body;

  /**
   * Add a plain text body alternative.
   * @var string
   */
  public $alt;

  /**
   * Whether the email is html or plain text.
   * @var boolean
   */
  public $html = true;

  /**
   * Holds the PHPMailer class object.
   * @var object
   */
  private $client;

  /**
   * Will hold the config array from the main settings folder.
   * @var string
   */
  private $config;

  /**
   * Implements __construct();
   *
   * The main Mail constructor.
   *
   * This sets up the PHPMailer class as a client and sets a default from and reply details.
   *
   * @return void
   */
  public function __construct(){
    $this->client = new PHPMailer;
    $this->from = array('name' => 'Framework', 'email' => 'mail@framework');
    $this->reply = array('name' => 'No Reply', 'email' => 'no-replay@framework');

    $config = new Config();
    if(isset($config->smtp) && !empty($config->smtp)){
      $this->config = $config->smtp;
      $this->smtp();
    }
  }

  /**
   * Implements send();
   *
   * Function to send out the message that has been set up previously.
   *
   * @return boolean
   */
  public function send(){
    $this->client->isHTML($this->html);
    // Check to make sure we have some from details
    if(!empty($this->from)){
      // If the name is not empty add it to the client.
      if(!empty($this->from['name'])){
        $this->client->FromName = $this->from['name'];
      }

      // If the email is not empty add it to the client.
      if(!empty($this->from['email'])){
        $this->client->From = $this->from['email'];
      }
    }

    // Check to make sure we have some from details
    if(!empty($this->reply)){
      $this->client->ReplyTo($this->reply['email'], $this->from['name']);
    }

    // Check to see if we have any reciepients.
    if(!empty($this->to) || !empty($this->cc) || !empty($this->bcc)){
      // If there are any TOS.
      if(!empty($this->to)){
        foreach($this->to as $to){
          $this->client->addAddress($to['email'], $to['name']);
        }
      }

      // If there are any CCS.
      if(!empty($this->cc)){
        foreach($this->cc as $cc){
          $this->client->addCC($cc['email'], $cc['name']);
        }
      }

      // If there are any BCCS.
      if(!empty($this->bcc)){
        foreach($this->bcc as $bcc){
          $this->client->addBCC($bcc['email'], $bcc['name']);
        }
      }

      // Add the subject to the client.
      if($this->subject && !empty($this->subject)){
        $this->client->Subject = $this->subject;
      } else {
        $this->client->Subject = '(no subject)';
      }

      // Add the body to the client.
      if($this->body && !empty($this->body)){
        $this->client->Body = $this->body;
      }

      // Add the alternative body to the client.
      if($this->alt && !empty($this->alt)){
        $this->client->AltBody = $this->alt;
      }

      // Add the alternative body to the client.
      if(!empty($this->attach)){
        foreach($this->attach as $attach){
          $this->client->addAttachment($attach);
        }
      }

      // Try and send the mail.
      if(!$this->client->send()){
        return $this->client->ErrorInfo;
      } else {
        // Reset the details so we can reuse it.
        $this->reset();
        return true;
      }
    } else {
      return false;
    }
  }

  /**
   * Implements smtp();
   *
   * Function to setup the SMTP details in the PHPMailer class.
   *
   * @return void
   */
  private function smtp(){
    $this->client->Host = implode(';', $this->config['host']);
    if(isset($this->config['port']) && !empty($this->config['port'])){
      $this->client->Port = $this->config['port'];
    }
    if($this->config['auth']['enabled']){
      $this->client->isSMTP();
      $this->client->Username = $this->config['auth']['credentials']['username'];
      $this->client->Password = $this->config['auth']['credentials']['password'];
      if(!isset($this->config['auth']['secure'])){
        $this->SMTPSecure = $this->config['auth']['secure'];
      }
    }
  }

  /**
   * Implements reset();
   *
   * Function to reset all the details in the mailer.
   *
   * @return void
   */
  private function reset() {
    foreach ($this as $key => $value) {
      $this->$key = null;
    }
  }
}

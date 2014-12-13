<?php
/**
 * Logger Class.
 *
 * The logger allows for the app to log certain events into a database table.
 * The table should be called log and have at least the following columns:
 * - id
 * - crdate - when the log happened.
 * - created_by - by who.
 * - address - the remote address of the log.
 * - type (error|warning|info) - one of the preceding three.
 * - log - A free text log field.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */
class Logger{

  /**
   * Implements add();
   *
   * Function allows app to log to the database table, if logging is switched on.
   *
   * @return void
   */
  public function add($data){
    $config = new Config();
    if($config->logging){
      $db = new Database();
      $user = new Auth();
      $data['crdate'] = time();
      $data['created_by'] = $user->session['fw']['id'];
      $data['address'] = $_SERVER['REMOTE_ADDR'];

      // Insert when a DB is available.
      if($db) $db->insert($data, array('table' => 'log'));
    } else {
      return false;
    }
  }
}

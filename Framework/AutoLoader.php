<?php
/**
 * Framework Autoloader.
 *
 * An autoloader for registering classes.
 *
 * @package     Framework
 * @author      Kris Pomphrey <kris@krispomphrey.co.uk>
 */

namespace Framework;

class AutoLoader{

  public function __construct(){
    spl_autoload_register(array($this, 'load'));
  }

  protected function load($class){
    $load = DIR_ROOT."/$class.php";

    if(is_readable($load)){
      require_once($load);
    }
  }
}

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

  public function __construct($data = null){
    spl_autoload_register(array($this, 'load'));
  }

  protected function load($class){
    $load = DIR_ROOT."/$class.php";
      var_dump($load);

    if(is_readable($load)){
      //var_dump($load);
      var_dump($class);
      require_once($load);
    }
  }
}

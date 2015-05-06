<?php
/**
 * This is the main index.php file that
 * handles the routing of the pages
 *
 * @package   Framework
 * @author    Kris Pomphrey <kris@krispomphrey.co.uk>
 **/
namespace Framework;

// Define the directory root of the site for use through the site.
define('DIR_ROOT', getcwd());

// Make sure Framework is ready.
require_once('Framework/AutoLoader.php');
$auto_loader = new AutoLoader();

use Framework\App;

// Initialise the app
$app = new App();

// Give us an output from the controller.
$app->go(); ?>

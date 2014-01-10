<?php
/****************************************
 * This is the main index.php file that
 * handles the routing of the pages
 *
 * @package: Framework
 * @author: Kris Pomphrey <kris@krispomphrey.co.uk>
 *
 ****************************************/

// Define the directory root of the site for use through the site.
define('DIR_ROOT', getcwd());

// Pull in the settings and our App class
require_once('Config/settings.php');
require_once('Framework/App.php');

// Initialise the app
$app = new WebApp(); ?>

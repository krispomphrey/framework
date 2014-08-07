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

// Make sure Framework is ready.
require_once('Framework/App.php');

// Initialise the app
$app = new WebApp();

// Give us an output from the controller.
$app->render_page(); ?>
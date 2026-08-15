<?php
/**
 * Plugin Name: <b style="color: #C82333;">Visitor Log Management
 * Plugin URI:https://github.com/Ozgurujim/ 
 * Description: Deletion of visitor log records via Master Files menu tool
 * Version: 1.0
 * Author: jim richardson
 * Author URI: https://github.com/Ozgurujim/
 */

// get instance of plugin object
$plugin = \SLiMS\Plugins::getInstance();

// registering our plugin into master_file module
$plugin->registerMenu('master_file', __('Visitor Log Deletion'), __DIR__ . '/index.php'); 
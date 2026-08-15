<?php
/**
 * Plugin Name: <b style="color: #2566BE;">SLiMS Documentation
 * Plugin URI:https://github.com/Ozgurujim/ 
 * Description: A searchable set of pages that serve as a manual for SLiMS
 * Version: 0.9
 * Author: jim richardson
 * Author URI: https://github.com/Ozgurujim/
 */

// get instance of plugin object
$plugin = \SLiMS\Plugins::getInstance();

// registering our plugin into home menu
$plugin->registerMenu('system', __('SLiMS Manual'), __DIR__ . '/launch-SLIMSdocs.html'); 
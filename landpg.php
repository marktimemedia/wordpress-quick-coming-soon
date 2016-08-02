<?php
/*
	Plugin Name: A Quick Landing Page
	Description: A simple "Coming Soon" page with redirects
	Author: Marktime Media
	Version: 0.1
	Author URI: http://www.marktimemedia.com
 */
 
define( 'landpg_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( landpg_PLUGIN_DIR . 'lib/landpg-config.php' );

register_activation_hook( __FILE__, 'landpg_create_landing_page' ); 
register_deactivation_hook( __FILE__, 'landpg_unpublish_page_on_deactivation' ); 

?>
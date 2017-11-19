<?php
/*
 Plugin Name: BEA Mark As Read
 Version: 1.0.2
 Version Boilerplate: 2.1.6
 Plugin URI: https://beapi.fr
 Description: Your plugin description
 Author: Be API Technical team
 Author URI: https://beapi.fr
 Domain Path: languages
 Text Domain: bea-mark-as-read

 ----

 Copyright 2017 Be API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'BEA_MAS_VERSION', time() );
define( 'BEA_MAS_MIN_PHP_VERSION', '5.4' );
define( 'BEA_MAS_VIEWS_FOLDER_NAME', 'bea-mas' );

// Plugin URL and PATH
define( 'BEA_MAS_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_MAS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEA_MAS_PLUGIN_DIRNAME', basename( rtrim( dirname( __FILE__ ), '/' ) ) );


// Check PHP min version
if ( version_compare( PHP_VERSION, BEA_MAS_MIN_PHP_VERSION, '<' ) ) {
	require_once( BEA_MAS_DIR . 'compat.php' );

	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA\MAS\Compatibility', 'admin_init' ) );

	// stop execution of this file
	return;
}

/**
 * Autoload all the things \o/
 */
require_once BEA_MAS_DIR . 'autoload.php';

add_action( 'plugins_loaded', 'init_bea_mark_as_read_plugin' );
/**
 * Init the plugin
 */
function init_bea_mark_as_read_plugin() {
	// Client
	\BEA\MAS\Main::get_instance();
	\BEA\MAS\Plugin::get_instance();

	// Shortcode
	\BEA\MAS\Shortcodes\Shortcode_Factory::register( 'Mas' );

	// Admin
	if ( is_admin() ) {
		\BEA\MAS\Admin\Main::get_instance();
	}
}

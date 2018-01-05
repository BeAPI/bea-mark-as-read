<?php
/*
 Plugin Name: BEA Mark As Read
 Version: 1.1
 Plugin URI: https://beapi.fr
 Description: This plugin lets you know which user has read an article from your WordPress site. It also makes it possible to display this information (percentage of reading), a practical thing for intranet type use.
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

// Plugin tables
global $wpdb;
$wpdb->tables[]     = 'mas_activity';
$wpdb->mas_activity = $wpdb->prefix . 'mas_activity';

// Plugin constants
define( 'BEA_MAS_VERSION', '1.1' );
define( 'BEA_MAS_MIN_PHP_VERSION', '7.0' );
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

// Plugin activate/deactive hooks
register_activation_hook( __FILE__, array( '\BEA\MAS\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\BEA\MAS\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', 'init_bea_mark_as_read_plugin' );
/**
 * Init the plugin
 */
function init_bea_mark_as_read_plugin() {
	// Client
	\BEA\MAS\Main::get_instance();
	\BEA\MAS\Plugin::get_instance();
	\BEA\MAS\Model::get_instance();

	// WP Cli
	\BEA\MAS\WP_Cli\Main::get_instance();

	// Shortcode
	\BEA\MAS\Shortcodes\Shortcode_Factory::register( 'Mas' );

	// Admin
	if ( is_admin() ) {
		\BEA\MAS\Admin\Main::get_instance();
	}
}

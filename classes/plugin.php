<?php

namespace BEA\MAS;

/**
 * The purpose of the plugin class is to have the methods for
 *  - activation actions
 *  - deactivation actions
 *  - uninstall actions
 *
 * Class Plugin
 * @package BEA\MAS
 */
class Plugin {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
	}

	/**
	 * Create tables for this plugin
	 */
	public static function activate() {
		global $wpdb;

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		// Add one library admin function for next function
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Data table
		maybe_create_table( $wpdb->mas_activity, "CREATE TABLE IF NOT EXISTS `{$wpdb->mas_activity}` (
			`mas_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`post_id` bigint(20) NOT NULL,
			PRIMARY KEY (`mas_id`),
			UNIQUE KEY `user_id` (`user_id`,`post_id`)
		) $charset_collate AUTO_INCREMENT=1;" );
	}

	public static function deactivate() {

	}
}
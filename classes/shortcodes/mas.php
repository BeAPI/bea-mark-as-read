<?php

namespace BEA\MAS\Shortcodes;

use BEA\MAS\Helpers;

/**
 * Class Mas
 *
 * @package BEA\MAS\Shortcodes
 * @since   2.1.0
 */
class Mas extends Shortcode {

	/**
	 * The shortcode [tag]
	 * @since   2.1.0
	 */
	protected $tag = 'bea-mas';

	/**
	 * Display shortcode content
	 *
	 * @since   2.1.0
	 *
	 * @param array $attributes
	 * @param string $content
	 *
	 * @return string
	 */
	public function render( $attributes = array(), $content = '' ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$users_has_no_read = get_users( [ 'fields' => 'ids' ] );
		$users_has_read    = get_post_meta( get_the_ID(), 'bea_users_has_read', true );
		if ( ! empty( $users_has_read ) ) {
			$users_has_no_read = array_diff( $users_has_no_read, $users_has_read );
			$users_has_read    = array_map( function ( $user ) {
				return new \WP_User( $user );
			}, $users_has_read );
		}

		$users_has_no_read = array_map( function ( $user ) {
			return new \WP_User( $user );
		}, $users_has_no_read );

		ob_start();
		require( Helpers::locate_template( 'mas' ) );
		$glossary = ob_get_clean();

		return $glossary;
	}


}
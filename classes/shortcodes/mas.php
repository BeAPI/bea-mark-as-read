<?php

namespace BEA\MAS\Shortcodes;

use BEA\MAS\Helpers;
use BEA\MAS\Model;

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
		global $post;

		if ( ! is_user_logged_in() || $post->post_status != 'publish' ) {
			return '';
		}

		$post_stats = Model::get_post_stats( get_the_ID() );

		ob_start();
		require( Helpers::locate_template( 'mas' ) );
		$output_html = ob_get_clean();

		return $output_html;
	}

}
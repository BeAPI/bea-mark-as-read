<?php

namespace BEA\MAS;

/**
 * The purpose of the main class is to init all the plugin base code like :
 *  - Taxonomies
 *  - Post types
 *  - Shortcodes
 *  - Posts to posts relations etc.
 *  - Loading the text domain
 *
 * Class Main
 * @package BEA\MAS
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
		add_action( 'init', array( $this, 'init_translations' ) );

		add_action( 'wp', array( $this, 'wp_register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_ajax_bea_mas', array( $this, 'wp_ajax_counter' ) );

		add_action( 'after_delete_post', [ $this, 'after_delete_post' ], 10, 1 );
		add_action( 'deleted_user', [ $this, 'deleted_user' ], 10, 1 );
	}

	/**
	 * Load the plugin translation
	 */
	public function init_translations() {
		// Load translations
		load_plugin_textdomain( 'bea-mark-as-read', false, BEA_MAS_PLUGIN_DIRNAME . '/languages' );
	}

	/**
	 * Register assets JS/CSS
	 */
	public function wp_register_scripts() {
		if ( is_admin() ) {
			return false;
		}

		// External libraries
		wp_register_style( 'tooltipster', BEA_MAS_URL . 'assets/js/tooltipster/dist/css/tooltipster.bundle.min.css', false, '4.0', 'all' );
		wp_register_style( 'tooltipster-theme', BEA_MAS_URL . 'assets/js/tooltipster/dist/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css', false, '4.0', 'all' );
		wp_register_script( 'tooltipster', BEA_MAS_URL . 'assets/js/tooltipster/dist/js/tooltipster.bundle.min.js', array( 'jquery' ), '4.0', true );
		wp_register_script( 'waypoints', BEA_MAS_URL . 'assets/js/waypoints/lib/jquery.waypoints.min.js', array( 'jquery' ), '4.0.1', true );

		// Custom JS/CSS
		wp_register_style( 'bea-mas', BEA_MAS_URL . 'assets/css/bea-mas.css', false, BEA_MAS_VERSION, 'all' );
		wp_register_script( 'bea-mas', BEA_MAS_URL . 'assets/js/bea-mas.min.js', array(
			'jquery',
			'tooltipster',
			'waypoints'
		), BEA_MAS_VERSION, true );
		wp_localize_script( 'bea-mas', 'bea_mas', [
			'ajax_url'          => admin_url( 'admin-ajax.php?action=bea_mas' ),
			'ajax_nonce'        => wp_create_nonce( 'bea_mas_' . get_the_ID() ),
			'current_object_id' => get_the_ID(),
			'jquery_target'     => apply_filters( 'bea/mas/jquery_target', '.entry-content, .hkb-article__content' )
		] );

		return true;
	}

	/**
	 * Enqueue only for singular view
	 *
	 * @return boolean
	 */
	public function wp_enqueue_scripts() {
		global $post;

		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( is_a( $post, 'WP_Post' ) ) {
			wp_enqueue_script( 'bea-mas' );

			wp_enqueue_style( 'bea-mas' );
			wp_enqueue_style( 'tooltipster' );
			wp_enqueue_style( 'tooltipster-theme' );

			return true;
		}

		return false;
	}

	/**
	 * Check AJAX request for set post meta if user
	 *
	 */
	public function wp_ajax_counter() {
		// Check member
		if ( ! isset( $_POST['id'] ) || ! is_user_logged_in() || empty( $_POST['id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Impossible to add this element ID.', 'bea-mark-as-read' ) ) );
		}

		// Get the member id
		$object_id = (int) $_POST['id'];
		$nonce     = self::generate_nonce_name( 'bea_mas', $object_id );

		// Update counter only if post is published
		$object = get_post( $object_id );
		if ( $object === false || $object->post_status != 'publish' ) {
			wp_send_json_error( array( 'message' => __( 'Post not exist or not published.', 'bea-mark-as-read' ) ) );
		}

		// Check the nonce
		if ( ! self::check_nonce( $nonce ) ) {
			wp_send_json_error( array( 'message' => __( 'Security error', 'bea-mark-as-read' ) ) );
		}

		$user_id = wp_get_current_user()->ID;

		// Users has already read this post ?
		$has_read = Model::exists( $user_id, $object_id );
		if ( empty( $has_read ) ) {
			// Add user to activity table
			$result = Model::merge( $user_id, $object_id );

			// check everything is ok
			if ( false === $result ) {
				wp_send_json_error( array( 'message' => __( 'An error occured during post meta update.', 'bea-mark-as-read' ) ) );
			}

			// Send the message
			wp_send_json_success( array( 'message' => __( 'Counter refreshed with success !', 'bea-mark-as-read' ) ) );
		} else {
			wp_send_json_success( array( 'message' => __( 'Already read !', 'bea-mark-as-read' ) ) );
		}
	}

	/**
	 * Generate the nonce name from current user
	 *
	 * @param  string $type [description]
	 * @param  integer $object_id [description]
	 *
	 * @return string            [description]
	 */
	public static function generate_nonce_name( $type, $object_id ) {
		// Return the nonce name
		return sprintf( '%s_%d', $type, $object_id );
	}

	/**
	 * Check nonce from request
	 *
	 * @param  string $action [description]
	 * @param  string $name [description]
	 *
	 * @return string         [description]
	 */
	public static function check_nonce( $action, $name = '_wpnonce' ) {
		return ! isset( $_REQUEST[ $name ] ) || ! wp_verify_nonce( $_REQUEST[ $name ], $action ) ? false : true;
	}

	/**
	 * Called when deleting a post
	 *
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function after_delete_post( $post_id = 0 ) {
		return Model::delete_by_post( $post_id );
	}

	/**
	 * Called when deleting a user
	 *
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public function deleted_user( $user_id = 0 ) {
		return Model::delete_by_user( $user_id );
	}

}

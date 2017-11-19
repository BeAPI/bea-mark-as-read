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
		add_action( 'wp', array( $this, 'wp_register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'wp_ajax_bea_mas', array( $this, 'wp_ajax_counter' ) );
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
		wp_register_script( 'bea-mas', BEA_MAS_URL . 'assets/js/bea-mas.js', array(
			'jquery',
			'tooltipster',
			'waypoints'
		), BEA_MAS_VERSION, true );
		wp_localize_script( 'bea-mas', 'bea_mas', [
			'ajax_url'          => admin_url( 'admin-ajax.php?action=bea_mas' ),
			'ajax_nonce'        => wp_create_nonce( 'bea_mas_' . get_the_ID() ),
			'current_object_id' => get_the_ID(),
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
			wp_send_json_error( array( 'message' => 'Impossible d\'ajouter cet élément.' ) );
		}

		// Get the member id
		$object_id = (int) $_POST['id'];
		$nonce     = self::generate_nonce_name( 'bea_mas', $object_id );

		// Update counter only if post is published
		$object = get_post( $object_id );
		if ( $object === false || $object->post_status != 'publish' ) {
			wp_send_json_error( array( 'message' => 'Article non publié ou non existant.' ) );
		}

		// Check the nonce
		if ( ! self::check_nonce( $nonce ) ) {
			wp_send_json_error( array( 'message' => 'Erreur de securité.' ) );
		}

		$user_id  = wp_get_current_user()->ID;
		$has_read = get_post_meta( $object_id, 'bea_users_has_read', true );
		if ( empty( $has_read ) ) {
			self::update_counter( $object_id, $user_id );
		} else {
			if ( in_array( $user_id, $has_read ) ) {
				// Send the message
				wp_send_json_success( array(
					'message' => 'Déjà vu !',
				) );
			} else {
				self::update_counter( $object_id, $user_id, $has_read );
			}
		}
	}

	/**
	 * Update meta with users array
	 *
	 * @param  integer $object_id [description]
	 * @param  integer $user_id [description]
	 * @param  array $has_read [description]
	 *
	 * @return boolean            [description]
	 */
	private static function update_counter( $object_id, $user_id, $has_read = [] ) {
		$has_read[] = $user_id;

		//Add user id to meta
		$result = update_post_meta( $object_id, 'bea_users_has_read', $has_read );

		// check everything is ok
		if ( false === $result ) {
			wp_send_json_error( array( 'message' => 'Erreur de compteur' ) );

			return false;
		}

		clean_post_cache( $object_id );

		// Send the message
		wp_send_json_success( array(
			'message' => 'Compteur à jour !',
		) );

		return true;
	}

	/**
	 * Generate the nonce name from current user
	 *
	 * @param  string $type      [description]
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
}
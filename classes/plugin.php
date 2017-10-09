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
	 * Load the plugin translation
	 */
	public function wp_register_scripts() {
		if ( is_admin() ) {
			return false;
		}
		wp_register_style( 'bea-mas', BEA_MAS_URL . 'assets/css/bea-mas.css', false, BEA_MAS_VERSION, 'all' );
		wp_register_style( 'dropit', BEA_MAS_URL . 'assets/css/dropit.css', false, '1.1.0', 'all' );

		wp_register_script( 'dropit', BEA_MAS_URL . 'assets/js/dropit.min.js', array( 'jquery' ), '1.1.0', true );

		wp_register_script( 'bea-mas', BEA_MAS_URL . 'assets/js/bea-mas.js', array(
			'jquery',
			'dropit'
		), BEA_MAS_VERSION, true );
		wp_localize_script( 'bea-mas', 'bea_mas', [
			'ajax_url'          => admin_url( 'admin-ajax.php?action=bea_mas' ),
			'ajax_nonce'        => wp_create_nonce( 'bea_mas_' . get_the_ID() ),
			'current_object_id' => get_the_ID(),
		] );
	}

	public function wp_enqueue_scripts() {
		global $post;

		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'bea-mas' ) ) {
			wp_enqueue_script( 'dropit' );
			wp_enqueue_script( 'bea-mas' );
			wp_enqueue_style( 'bea-mas' );
			wp_enqueue_style( 'dropit' );
		}
	}

	public function wp_ajax_counter() {
		// Check member
		if ( ! isset( $_POST['id'] ) || ! is_user_logged_in() || empty( $_POST['id'] ) ) {
			wp_send_json_error( array( 'message' => 'Impossible d\'ajouter cet élément.' ) );
		}

		// Get the member id
		$object_id = (int) $_POST['id'];
		$nonce     = self::generate_nonce_name( 'bea_mas', $object_id );

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

	private static function update_counter( $object_id, $user_id, $has_read = [] ) {
		$has_read[] = $user_id;

		//Add user id to meta
		$result = update_post_meta( $object_id, 'bea_users_has_read', $has_read );

		// check everything is ok
		if ( false === $result ) {
			wp_send_json_error( array( 'message' => 'Erreur de compteur' ) );
		}

		clean_post_cache( $object_id );

		// Send the message
		wp_send_json_success( array(
			'message' => 'Compteur à jour !',
		) );
	}

	/**
	 * Generate the nonce name from current user
	 *
	 */
	public static function generate_nonce_name( $type, $object_id ) {
		// Return the nonce name
		return sprintf( '%s_%d', $type, $object_id );
	}

	public static function check_nonce( $action, $name = '_wpnonce' ) {
		return ! isset( $_REQUEST[ $name ] ) || ! wp_verify_nonce( $_REQUEST[ $name ], $action ) ? false : true;
	}
}
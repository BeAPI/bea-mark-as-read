<?php

namespace BEA\MAS;


class Model {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init() {
	}

	/**
	 * @param int $user_id
	 *
	 * @return mixed
	 */
	public static function delete_by_user( $user_id = 0 ) {
		global $wpdb;

		/** @var wpdb $wpdb */
		return $wpdb->delete( $wpdb->mas_activity, array( 'user_id' => $user_id ), array( '%d' ) );
	}

	/**
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public static function delete_by_post( $post_id = 0 ) {
		global $wpdb;

		/** @var wpdb $wpdb */
		return $wpdb->delete( $wpdb->mas_activity, array( 'post_id' => $post_id ), array( '%d' ) );
	}

	/**
	 * @param int $id
	 *
	 * @return mixed
	 */
	public static function delete( $id = 0 ) {
		global $wpdb;

		/** @var wpdb $wpdb */
		return $wpdb->delete( $wpdb->mas_activity, array( 'mas_id' => $id ), array( '%d' ) );
	}

	/**
	 * @param $user_id
	 * @param $post_id
	 *
	 * @return int|mixed
	 */
	public static function merge( $user_id, $post_id ) {
		$activity_id = self::exists( $user_id, $post_id );
		if ( $activity_id != false ) {
			return $activity_id;
		}

		return self::insert( $user_id, $post_id );
	}

	/**
	 * @param $user_id
	 * @param $post_id
	 *
	 * @return int
	 */
	public static function insert( $user_id, $post_id ) {
		global $wpdb;

		/** @var wpdb $wpdb */
		$wpdb->insert(
			$wpdb->mas_activity,
			array(
				'user_id' => $user_id,
				'post_id' => $post_id
			),
			array( '%d', '%d' )
		);

		return $wpdb->insert_id;
	}

	/**
	 * Get one row by ID...
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function get( $id ) {
		global $wpdb;

		/** @var wpdb $wpdb */
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->mas_activity WHERE mas_id = %d", $id ) );
	}

	/**
	 *
	 * @return mixed
	 */
	public static function exists( $user_id, $post_id ) {
		global $wpdb;

		/** @var wpdb $wpdb */
		return $wpdb->get_var( $wpdb->prepare( "SELECT mas_id
			FROM $wpdb->mas_activity
			WHERE user_id = %d
			AND post_id = %d"
			, $user_id, $post_id ) );
	}

	/**
	 * @return mixed
	 */
	public static function get_all() {
		global $wpdb;

		/** @var wpdb $wpdb */
		return $wpdb->get_results( "SELECT * FROM $wpdb->mas_activity" );
	}

	/**
	 * Get all rows for an user or many users
	 *
	 * @param $user_ids
	 *
	 * @return mixed
	 * @author Amaury BALMER
	 */
	public static function get_by_user( $user_ids ) {
		global $wpdb;

		$user_ids = array_map( 'intval', (array) $user_ids );

		/** @var wpdb $wpdb */
		return $wpdb->get_results( "SELECT * FROM $wpdb->mas_activity WHERE user_id IN ( " . implode( ', ', $user_ids ) . " )" );
	}

	/**
	 * Get all rows for an post or many posts
	 *
	 * @param $post_ids
	 *
	 * @return mixed
	 * @author Amaury BALMER
	 */
	public static function get_by_post( $post_ids ) {
		global $wpdb;

		$post_ids = array_map( 'intval', (array) $post_ids );

		/** @var wpdb $wpdb */
		return $wpdb->get_results( "SELECT * FROM $wpdb->mas_activity WHERE post_id IN ( " . implode( ', ', $post_ids ) . " )" );
	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	public static function get_post_stats( $post_id ) {
		// Prepare array return
		$post_stats = [ 'has_read' => array(), 'has_no_read' => array(), 'total_users' => 0 ];

		// Get all users IDs
		$users_has_no_read = get_users( [ 'fields' => 'ids' ] );

		// Save counter total users
		$post_stats['total_users'] = count( $users_has_no_read );

		// Get results for the post
		$users_has_read    = Model::get_by_post( $post_id );
		if ( ! empty( $users_has_read ) ) {
			// keep only user_id data
			$users_has_read    = wp_list_pluck( $users_has_read, 'user_id' );

			// Distinct read and unread users
			$users_has_no_read = array_diff( $users_has_no_read, $users_has_read );

			// Get full data for reader
			$users_has_read    = array_map( function ( $user ) {
				return new \WP_User( $user );
			}, $users_has_read );
		} else {
			$users_has_read = array();
		}

		// Get full data for "unreader"
		$users_has_no_read = array_map( function ( $user ) {
			return new \WP_User( $user );
		}, $users_has_no_read );

		// Prepare data for return
		$post_stats['has_read'] = $users_has_read;
		$post_stats['has_no_read'] = $users_has_no_read;

		return $post_stats;
	}
}
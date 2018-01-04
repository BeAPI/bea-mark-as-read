<?php

namespace BEA\MAS\WP_Cli;

use BEA\MAS\Model;

class Migration extends \WP_CLI_Command {

	/**
	 * Migrate from metadata to new table structure
	 *
	 * ## EXAMPLES
	 * wp bea_mas migration_from_postmeta --url=
	 *
	 * @since  1.0.0
	 * @author Amaury BALMER
	 *
	 * @synopsis
	 */
	function migration_from_postmeta() {
		$contents_to_migrate = new \WP_Query( [
			'no_found_rows' => true,
			'nopaging'      => true,
			'post_type'     => 'any',
			'meta_key'      => 'bea_users_has_read',
			'fields'        => 'ids'
		] );
		if ( ! $contents_to_migrate->have_posts() ) {
			\WP_CLI::error( sprintf( 'No content to migrate.' ) );

			return;
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'Migrate post metadata', $contents_to_migrate->posts );
		foreach ( $contents_to_migrate->posts as $post_id ) {
			$users_has_read = (array) get_post_meta( $post_id, 'bea_users_has_read', true );
			foreach ( $users_has_read as $user_id ) {
				Model::merge( $user_id, $post_id );
			}
			//delete_post_meta( $post_id, 'bea_users_has_read' );

			$progress->tick();
		}
		$progress->finish();

		\WP_CLI::success( sprintf( '%d migrated contents !', count( $contents_to_migrate->posts ) ) );
	}
}
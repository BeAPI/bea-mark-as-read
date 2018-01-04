<?php
Namespace BEA\MAS\WP_Cli;

use BEA\MAS\Singleton;

class Main {

	use Singleton;

	protected function init() {
		if ( defined( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'bea_mas', 'BEA\MAS\WP_Cli\Migration' );
		}
	}
}
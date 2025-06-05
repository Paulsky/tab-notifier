<?php

/**
 * Short Description. (use period)
 *
 * Long Description.
 *
 * @since      1.0.0
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
trait Tab_Return_Notifier_Content_Types {

	/**
	 * Get all public post types
	 *
	 * @param string $output Type of output. 'names' or 'objects'.
	 * @return array
	 * @since    1.0.0
	 */
	public function get_public_post_types( $output = 'names' ) {
		$post_types = get_post_types( [ 'public' => true ], $output );
		unset( $post_types['attachment'] );

		return $post_types;
	}

	/**
	 * Get all public taxonomies.
	 *
	 * @param string $output Type of output. 'names' or 'objects'.
	 * @return array
	 * @since    1.0.0
	 */
	public function get_public_taxonomies( $output = 'names' ) {
		return get_taxonomies( [ 'public' => true ], $output );
	}

}
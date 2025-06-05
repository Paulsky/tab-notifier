<?php

/**
 * The helper functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/includes
 */

/**
 * The helper functionality of the plugin.
 *
 * Defines helper methods for retrieving notifier status and getting messages.
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
trait Tab_Return_Notifier_Helper {

	/**
	 * Get all public post types
	 *
	 * @param string $output Type of output. 'names' or 'objects'.
	 *
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
	 *
	 * @return array
	 * @since    1.0.0
	 */
	public function get_public_taxonomies( $output = 'names' ) {
		return get_taxonomies( [ 'public' => true ], $output );
	}


	/**
	 * Get default settings for the plugin.
	 *
	 * @return array Default settings array
	 * @since    1.0.0
	 */
	public function get_default_settings() {
		$settings = array(
			'general'    => array(
				'enabled'   => true,
				'animation' => 'rotating',
				'speed'     => 500,
				'messages'  => ''
			),
			'post_types' => array(),
			'taxonomies' => array()
		);

		$post_types = $this->get_public_post_types();

		foreach ( $post_types as $post_type_name ) {
			$settings['post_types'][ $post_type_name ] = array(
				'enabled'  => true,
				'messages' => '',
			);
		}

		$taxonomies = $this->get_public_taxonomies();

		foreach ( $taxonomies as $taxonomy_name ) {
			$settings['taxonomies'][ $taxonomy_name ] = array(
				'enabled'  => true,
				'messages' => '',
			);
		}

		return $settings;
	}

}

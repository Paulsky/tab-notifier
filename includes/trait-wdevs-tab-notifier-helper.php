<?php

/**
 * The helper functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tab_Notifier
 * @subpackage Wdevs_Tab_Notifier/includes
 */

/**
 * The helper functionality of the plugin.
 *
 * Defines helper methods for retrieving notifier status and getting messages.
 *
 * @package    Wdevs_Tab_Notifier
 * @subpackage Wdevs_Tab_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
trait Wdevs_Tab_Notifier_Helper {

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
				'messages'  => array()
			),
			'post_types' => array(),
			'taxonomies' => array()
		);

		$post_types = $this->get_public_post_types();

		foreach ( $post_types as $post_type_name ) {
			$settings['post_types'][ $post_type_name ] = array(
				'enabled'  => true,
				'messages' => array()
			);
		}

		$taxonomies = $this->get_public_taxonomies();

		foreach ( $taxonomies as $taxonomy_name ) {
			$settings['taxonomies'][ $taxonomy_name ] = array(
				'enabled'  => true,
				'messages' => array()
			);
		}

		return $settings;
	}

	/**
	 * Register a string translation
	 *
	 * @since 1.0.0
	 */
	public function register_translation( $name, $string ) {
		// Check if WPML is active
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}

		do_action( 'wpml_register_single_string', 'tab-return-notifier', $name, $string );
	}

	/**
	 * Get a string translation
	 *
	 * @since 1.0.0
	 */
	public function get_translation( $name, $default ) {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return $default;
		}

		return apply_filters( 'wpml_translate_single_string', $default, 'tab-return-notifier', $name );
	}

}

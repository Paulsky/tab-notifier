<?php

/**
 * Handles variables for the Tab Return Notifier plugin.
 *
 * This class provides methods to retrieve various variables that can be used
 * in notifications, including document titles, post titles, site names, and
 * WooCommerce-specific data when available.
 *
 * @since      1.0.0
 * @package    Wdevs_Tab_Notifier
 * @subpackage Wdevs_Tab_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tab_Notifier_Variables {

	/**
	 * Retrieves all available variables with their labels and values.
	 *
	 * @return   array  An associative array of variables with their labels and values
	 * @since    1.0.0
	 */
	public static function get_variables() {
		$variables = array(
			'document_title' => array(
				'label' => __( 'Document title', 'tab-return-notifier' ),
				'value' => self::get_document_title(),
			),
			'post_title'     => array(
				'label' => __( 'Post title', 'tab-return-notifier' ),
				'value' => self::get_post_title(),
			),
			'site_name'      => array(
				'label' => __( 'Site name', 'tab-return-notifier' ),
				'value' => self::get_site_name(),
			),
		);

		// Add WooCommerce specific variables if WooCommerce is active
		if ( self::is_woocommerce_active() ) {
			$woocommerce_variables = array(
				'cart_items_count' => array(
					'label' => __( 'Number of items in cart', 'tab-return-notifier' ),
					'value' => self::get_cart_items_count()
				),
				'recently_viewed'  => array(
					'label' => __( 'Recently viewed product', 'tab-return-notifier' ),
					'value' => self::get_recently_viewed_product_name()
				),
			);

			$variables = array_merge( $variables, $woocommerce_variables );
		}

		uasort( $variables, function ( $a, $b ) {
			return strcmp( $a['label'], $b['label'] );
		} );

		return apply_filters( 'wtn_get_variables', $variables );
	}

	/**
	 * Gets the document title for the current page.
	 *
	 * @return   string  The document title
	 * @since    1.0.0
	 */
	protected static function get_document_title() {
		return html_entity_decode( wp_get_document_title() );
	}

	/**
	 * Gets the title of the current post.
	 *
	 * @return   string|null  The post title or null if not available
	 * @since    1.0.0
	 */
	protected static function get_post_title() {
		return html_entity_decode( get_the_title() ) ?: null;
	}

	/**
	 * Gets the site name from WordPress settings.
	 *
	 * @return   string  The site name
	 * @since    1.0.0
	 */
	protected static function get_site_name() {
		return get_bloginfo( 'name' );
	}

	/**
	 * Checks if WooCommerce is active.
	 *
	 * @return   bool  True if WooCommerce is active, false otherwise
	 * @since    1.0.0
	 */
	private static function is_woocommerce_active() {
		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

		return in_array( $plugin_path, wp_get_active_and_valid_plugins() )
		       || in_array( $plugin_path, wp_get_active_network_plugins() );
	}

	/**
	 * Gets the number of items in the cart
	 *
	 * @return   int  Number of items
	 * @since    1.0.0
	 */
	protected static function get_cart_items_count() {
		if ( WC()->cart ) {
			return WC()->cart->get_cart_contents_count();
		}

		return 0;
	}

	/**
	 * Gets the recently viewed product name
	 *
	 * @return   string  The site name
	 * @since    1.0.0
	 */
	protected static function get_recently_viewed_product_name() {
		$viewed_products   = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
		$viewed_product_id = end( $viewed_products );

		if ( ! empty( $viewed_product_id ) && is_numeric( $viewed_product_id ) ) {
			$product = wc_get_product( (int) $viewed_product_id );
			if ( $product ) {
				return $product->get_name();
			}
		}

		return null;
	}
}
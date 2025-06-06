<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/public
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Tab_Return_Notifier_Public {

	use Tab_Return_Notifier_Helper;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * @since 1.0.0
	 */
	const AJAX_NONCE_ACTION = 'tab-return-notifier-nonce';

	/**
	 * @since 1.0.0
	 */
	const AJAX_ACTION_RENDER = 'get_messages';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( $this->is_enabled_for_current_view() ) {
			$script_asset  = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/tab-return-notifier-public.asset.php' );
			$public_handle = $this->plugin_name;
			wp_enqueue_script(
				$public_handle,
				plugin_dir_url( dirname( __FILE__ ) ) . 'build/tab-return-notifier-public.js',
				array_merge($script_asset['dependencies'], ['tab-return-notifier-shared']),
				$script_asset['version']
			);

			$messages = $this->get_messages_for_current_view();
			$options  = get_option( 'tab_return_notifier_options', $this->get_default_settings() );

			wp_localize_script(
				$public_handle,
				'trnData',
				array(
					'animation'      => $options['general']['animation'],
					'speed'          => $options['general']['speed'],
					'messages'       => $messages,
					'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( self::AJAX_NONCE_ACTION ),
					'messagesAction' => self::AJAX_ACTION_RENDER
				)
			);
		}

	}

	public function is_enabled_for_current_view() {
		$options = get_option( 'tab_return_notifier_options', $this->get_default_settings() );

		if ( empty( $options['general']['enabled'] ) ) {
			return false;
		}

		$enabled = false;

		if ( is_home() || is_front_page() ) {
			$enabled = true;
		} elseif ( is_singular() ) {
			$post_type = get_post_type();
			$enabled   = ! empty( $options['post_types'][ $post_type ]['enabled'] );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$queried_object = get_queried_object();
			if ( $queried_object instanceof WP_Term ) {
				$taxonomy = $queried_object->taxonomy;
				$enabled  = ! empty( $options['taxonomies'][ $taxonomy ]['enabled'] );
			}
		} elseif ( is_post_type_archive() ) {
			$post_type = get_queried_object()->name;
			$enabled   = isset( $options['post_types'][ $post_type ] ) && ! empty( $options['post_types'][ $post_type ]['enabled'] );
		}

		return apply_filters( 'trn_is_enabled_for_current_view', $enabled, $options );
	}

	public function get_templates_for_current_view() {
		$options = get_option( 'tab_return_notifier_options', $this->get_default_settings() );

		$messages = $options['general']['messages'];

		if ( is_singular() ) {
			$post_type = get_post_type();
			if ( ! empty( $options['post_types'][ $post_type ]['messages'] ) ) {
				$messages = $options['post_types'][ $post_type ]['messages'];
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$queried_object = get_queried_object();
			if ( $queried_object instanceof WP_Term ) {
				$taxonomy = $queried_object->taxonomy;
				if ( ! empty( $options['taxonomies'][ $taxonomy ]['messages'] ) ) {
					$messages = $options['taxonomies'][ $taxonomy ]['messages'];
				}
			}
		} elseif ( is_post_type_archive() ) {
			$post_type = get_queried_object()->name;
			if ( isset( $options['post_types'][ $post_type ] ) ) {
				if ( ! empty( $options['post_types'][ $post_type ]['messages'] ) ) {
					$messages = $options['post_types'][ $post_type ]['messages'];
				}
			}
		}

		return apply_filters( 'trn_get_messages_for_current_view', $messages, $options );
	}

	public function get_messages_for_current_view() {
		$templates = $this->get_templates_for_current_view();
		$variables = Tab_Return_Notifier_Variables::get_variables();

		$processed_messages = array();

		foreach ( $templates as $template ) {
			$message = $template;

			foreach ( $variables as $key => $var ) {
				$placeholder = '{{' . $key . '}}';

				$value = ( $var['value'] !== null ) ? $var['value'] : '';

				$message = str_replace( $placeholder, $value, $message );
			}

			$processed_messages[] = $message;
		}

		return apply_filters( 'trn_get_processed_messages_for_current_view', $processed_messages, $templates, $variables );
	}

	/**
	 * AJAX request
	 * Render the shortcode by AJAX request
	 *
	 * @since 1.0.0
	 */
	public function get_messages_action() {
		if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( 'missing_fields' );
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), self::AJAX_NONCE_ACTION ) ) {
			wp_send_json_error( 'bad_nonce' );
		}

		wp_send_json_success(
			array(
				'messages' => $this->get_messages_for_current_view(),
			)
		);
	}

}

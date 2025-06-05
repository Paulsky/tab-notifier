<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/admin
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Tab_Return_Notifier_Admin {

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
	 * The admin view handler for the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Tab_Return_Notifier_Admin_View $admin_view
	 * */
	private $admin_view;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name   = $plugin_name;
		$this->version       = $version;
		$this->admin_view    = new Tab_Return_Notifier_Admin_View();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if ( $this->is_settings_page() ) {
			wp_enqueue_style(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'css/tab-return-notifier-admin.css',
				array( 'site-health' ),
				$this->version,
				'all'
			);

			wp_enqueue_style(
				'tab-return-notifier-elements',
				plugin_dir_url( __FILE__ ) . 'css/tab-return-notifier-elements.css',
				array(),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( $this->is_settings_page() ) {
			$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/tab-return-notifier-admin.asset.php' );
			$admin_handle = $this->plugin_name . '-admin';
			wp_enqueue_script(
				$admin_handle,
				plugin_dir_url( dirname( __FILE__ ) ) . 'build/tab-return-notifier-admin.js',
				array_merge($script_asset['dependencies'], ['jquery-ui-sortable']),
				$script_asset['version']
			);

			wp_localize_script(
				$admin_handle,
				'trnData',
				array(
					'variables' => Tab_Return_Notifier_Variables::get_variables(),
				)
			);
		}
	}

	/**
	 * Add the plugin settings page to the WordPress admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Tab Return Notifier Settings', 'tab-return-notifier' ),
			__( 'Tab Return Notifier', 'tab-return-notifier' ),
			'manage_options',
			'tab-return-notifier',
			array( $this, 'display_settings_page' )
		);
	}

	public function is_settings_page() {
		return ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'tab-return-notifier' );
	}

	/**
	 * Register plugin settings with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			'tab_return_notifier_settings',
			'tab_return_notifier_options',
			array(
				'default'           => $this->get_default_settings(),
				'sanitize_callback' => array( $this, 'sanitize_settings' )
			)
		);
	}

	/**
	 * Sanitize plugin settings before saving.
	 *
	 * @param array $input Unsanitized settings input
	 *
	 * @return array Sanitized settings
	 * @since    1.0.0
	 */
	public function sanitize_settings( $input ) {
		$output = $this->get_default_settings();

		if ( ! is_array( $input ) ) {
			return $output;
		}

		if ( isset( $input['general'] ) ) {
			$output['general']['enabled']   = ! empty( $input['general']['enabled'] );
			$output['general']['animation'] = in_array( $input['general']['animation'], array(
				'rotating',
				'scrolling'
			) )
				? $input['general']['animation']
				: 'rotating';
			$output['general']['speed']     = absint( $input['general']['speed'] );
			$output['general']['messages']  = $this->sanitize_messages( $input['general']['messages'] );
		}

		if ( isset( $input['post_types'] ) ) {
			foreach ( $this->get_public_post_types() as $post_type ) {
				if ( isset( $input['post_types'][ $post_type ] ) ) {
					$output['post_types'][ $post_type ]['enabled']  = ! empty( $input['post_types'][ $post_type ]['enabled'] );
					$output['post_types'][ $post_type ]['messages'] = $this->sanitize_messages( $input['post_types'][ $post_type ]['messages'] );
				}
			}
		}

		if ( isset( $input['taxonomies'] ) ) {
			foreach ( $this->get_public_taxonomies() as $taxonomy ) {
				if ( isset( $input['taxonomies'][ $taxonomy ] ) ) {
					$output['taxonomies'][ $taxonomy ]['enabled']  = ! empty( $input['taxonomies'][ $taxonomy ]['enabled'] );
					$output['taxonomies'][ $taxonomy ]['messages'] = $this->sanitize_messages( $input['taxonomies'][ $taxonomy ]['messages'] );
				}
			}
		}

		return $output;
	}

	private function sanitize_messages( $messages ) {
		if ( empty( $messages ) ) {
			return array();
		}

		if ( ! is_array( $messages ) ) {
			$messages = array( $messages );
		}

		$sanitized = array();
		foreach ( $messages as $message ) {
			$clean = sanitize_text_field( $message );
			if ( ! empty( $clean ) ) {
				$sanitized[] = $clean;
			}
		}

		return $sanitized;
	}

	/**
	 * Display the plugin settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
		$options    = get_option( 'tab_return_notifier_options', $this->get_default_settings() );
		$post_types = $this->get_public_post_types( 'objects' );
		$taxonomies = $this->get_public_taxonomies( 'objects' );

		if ( isset( $_POST['tab_return_notifier_options'] ) && check_admin_referer( 'tab_return_notifier_settings' ) ) {

			$options = $_POST['tab_return_notifier_options'] ?? array();

			update_option( 'tab_return_notifier_options', $options );

			add_settings_error(
				'tab_return_notifier_messages',
				'tab_return_notifier_message',
				__( 'Settings saved', 'tab-return-notifier' ),
				'updated'
			);
		}

		$this->admin_view->render_settings_page( $active_tab, $options, $post_types, $taxonomies );
	}
}
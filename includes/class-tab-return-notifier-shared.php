<?php

/**
 * The shared block functionality of the plugin.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tax_Switch
 * @subpackage Wdevs_Tax_Switch/includes
 */

/**
 * The shared  functionality of the plugin.
 *
 * Defines functions for all the shared code of Tab_Return_Notifier
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Tab_Return_Notifier_Shared {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	protected $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$script_asset = require( plugin_dir_path( dirname( __FILE__ ) ) . 'build/tab-return-notifier-shared.asset.php' );
		$shared_handle = $this->plugin_name . '-shared';

		wp_register_script(
			$shared_handle,
			plugin_dir_url( dirname( __FILE__ ) ) . 'build/tab-return-notifier-shared.js',
			$script_asset['dependencies'],
			$script_asset['version']
		);
	}
}

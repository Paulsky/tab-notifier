<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tab_Return_Notifier
 * @subpackage Tab_Return_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Tab_Return_Notifier_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tab-return-notifier',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

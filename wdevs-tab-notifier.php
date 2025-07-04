<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wijnberg.dev
 * @since             1.0.0
 * @package           Wdevs_Tab_Notifier
 *
 * @wordpress-plugin
 * Plugin Name:       Tab Return Notifier
 * Plugin URI:        https://products.wijnberg.dev
 * Description:       Bring visitors back to your site with animated tab notifications triggered when they switch to another browser tab.
 * Version:           1.0.0
 * Author:            Wijnberg Developments
 * Author URI:        https://wijnberg.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tab-return-notifier
 * Domain Path:       /languages
 * Tested up to:      6.8
 * Requires PHP:      7.4
 * Requires at least: 5.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WDEVS_TAB_NOTIFIER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wdevs-tab-notifier-activator.php
 */
function activate_wdevs_tab_notifier() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-tab-notifier-activator.php';
	Wdevs_Tab_Notifier_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wdevs-tab-notifier-deactivator.php
 */
function deactivate_wdevs_tab_notifier() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-tab-notifier-deactivator.php';
	Wdevs_Tab_Notifier_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wdevs_tab_notifier' );
register_deactivation_hook( __FILE__, 'deactivate_wdevs_tab_notifier' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wdevs-tab-notifier.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wdevs_tab_notifier() {

	$plugin = new Wdevs_Tab_Notifier();
	$plugin->run();

}

run_wdevs_tab_notifier();

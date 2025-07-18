<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wijnberg.dev
 * @since      1.0.0
 *
 * @package    Wdevs_Tab_Notifier
 * @subpackage Wdevs_Tab_Notifier/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wdevs_Tab_Notifier
 * @subpackage Wdevs_Tab_Notifier/includes
 * @author     Wijnberg Developments <contact@wijnberg.dev>
 */
class Wdevs_Tab_Notifier {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wdevs_Tab_Notifier_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WDEVS_TAB_NOTIFIER_VERSION' ) ) {
			$this->version = WDEVS_TAB_NOTIFIER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wdevs-tab-notifier';

		$this->load_dependencies();
		$this->define_shared_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wdevs_Tab_Notifier_Loader. Orchestrates the hooks of the plugin.
	 * - Wdevs_Tab_Notifier_Admin. Defines all hooks for the admin area.
	 * - Wdevs_Tab_Notifier_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tab-notifier-loader.php';

		/**
		 * The class responsible for message variables.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tab-notifier-variables.php';

		/**
		 * The class responsible for settings and helpers
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/trait-wdevs-tab-notifier-helper.php';

		/**
		 * The class responsible for shared functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wdevs-tab-notifier-shared.php';

		/**
		 * The class responsible for rendering the admin html.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wdevs-tab-notifier-admin-view.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wdevs-tab-notifier-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wdevs-tab-notifier-public.php';

		$this->loader = new Wdevs_Tab_Notifier_Loader();

	}

	/**
	 * Register all of the hooks related to both the admin and the public area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shared_hooks() {
		$plugin_shared = new Wdevs_Tab_Notifier_Shared( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_shared, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wdevs_Tab_Notifier_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

		$this->loader->add_filter( 'plugin_action_links_' . plugin_basename( dirname( __DIR__ ) . '/' . $this->plugin_name . '.php' ), $plugin_admin, 'add_action_links' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wdevs_Tab_Notifier_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		//AJAX Requests
		$this->loader->add_action( 'wp_ajax_' . Wdevs_Tab_Notifier_Public::AJAX_ACTION_RENDER, $plugin_public, Wdevs_Tab_Notifier_Public::AJAX_ACTION_RENDER . '_action' );
		$this->loader->add_action( 'wp_ajax_nopriv_' . Wdevs_Tab_Notifier_Public::AJAX_ACTION_RENDER, $plugin_public, Wdevs_Tab_Notifier_Public::AJAX_ACTION_RENDER . '_action' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wdevs_Tab_Notifier_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}

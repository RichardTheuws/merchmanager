<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
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
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 * @author     Theuws Consulting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Merchmanager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Merchmanager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
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
		if ( defined( 'MERCHMANAGER_VERSION' ) ) {
			$this->version = MERCHMANAGER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'merchmanager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_post_types();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Merchmanager_Loader. Orchestrates the hooks of the plugin.
	 * - Merchmanager_i18n. Defines internationalization functionality.
	 * - Merchmanager_Admin. Defines all hooks for the admin area.
	 * - Merchmanager_Public. Defines all hooks for the public side of the site.
	 * - Merchmanager_Post_Types. Defines custom post types.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-merchmanager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-merchmanager-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-merchmanager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-merchmanager-public.php';
		
		/**
		 * The class responsible for defining custom post types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/post-types/class-merchmanager-post-types.php';

		$this->loader = new Merchmanager_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Merchmanager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Merchmanager_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Merchmanager_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// Add admin menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		// Reorder submenu: Dashboard, Bands, Tours, Shows, Merchandise, Sales Pages, Sales, Reports, Settings, Setup Wizard
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'reorder_merchmanager_submenu', 999 );
		
		// Redirect to onboarding on first run
		$this->loader->add_action( 'admin_init', $plugin_admin, 'maybe_redirect_to_onboarding' );
		
		// Register settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

		// Handle CSV export before any output
		$this->loader->add_action( 'admin_init', $plugin_admin, 'maybe_handle_report_csv_export' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'maybe_handle_low_stock_export' );

		// Handle tour show import/export
		$this->loader->add_action( 'admin_post_msp_import_shows', $plugin_admin, 'handle_tour_import_shows' );
		$this->loader->add_action( 'admin_post_msp_export_shows', $plugin_admin, 'handle_tour_export_shows' );
		
		// Add settings link to plugins page
		$this->loader->add_filter( 'plugin_action_links_' . MERCHMANAGER_PLUGIN_BASENAME, $plugin_admin, 'add_action_links' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Merchmanager_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// Register shortcodes
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

	}
	
	/**
	 * Register custom post types.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_post_types() {
		
		$plugin_post_types = new Merchmanager_Post_Types();
		
		$this->loader->add_action( 'init', $plugin_post_types, 'register_post_types' );
		
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
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Merchmanager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
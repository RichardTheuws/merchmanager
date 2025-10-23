<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Load meta boxes
		$this->load_meta_boxes();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Merchmanager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Merchmanager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/merchmanager-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Merchmanager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Merchmanager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/merchmanager-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add admin menu items.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {

		// Add main menu item - accessible to anyone with any MSP capability
		add_menu_page(
			__( 'Merchandise Sales', 'merchmanager' ),
			__( 'Merchandise Sales', 'merchmanager' ),
			'manage_msp',
			'merchmanager',
			array( $this, 'display_dashboard_page' ),
			'dashicons-store',
			26
		);

		// Add dashboard submenu - accessible to anyone with any MSP capability
		add_submenu_page(
			'merchmanager',
			__( 'Dashboard', 'merchmanager' ),
			__( 'Dashboard', 'merchmanager' ),
			'manage_msp',
			'merchmanager',
			array( $this, 'display_dashboard_page' )
		);

		// Add sales submenu - accessible to anyone with sales capability
		add_submenu_page(
			'merchmanager',
			__( 'Sales', 'merchmanager' ),
			__( 'Sales', 'merchmanager' ),
			'manage_msp_sales',
			'msp-sales',
			array( $this, 'display_sales_page' )
		);

		// Add reports submenu - accessible to MSP Management and Sales roles
		add_submenu_page(
			'merchmanager',
			__( 'Reports', 'merchmanager' ),
			__( 'Reports', 'merchmanager' ),
			'manage_msp_sales',
			'msp-reports',
			array( $this, 'display_reports_page' )
		);

		// Add settings submenu - accessible to MSP Management only
		add_submenu_page(
			'merchmanager',
			__( 'Settings', 'merchmanager' ),
			__( 'Settings', 'merchmanager' ),
			'manage_msp',
			'msp-settings',
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 * Display the dashboard page.
	 *
	 * @since    1.0.0
	 */
	public function display_dashboard_page() {
		if ( ! current_user_can( 'manage_msp' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-dashboard.php';
	}

	/**
	 * Display the sales page.
	 *
	 * @since    1.0.0
	 */
	public function display_sales_page() {
		if ( ! current_user_can( 'manage_msp_sales' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-sales.php';
	}

	/**
	 * Display the reports page.
	 *
	 * @since    1.0.0
	 */
	public function display_reports_page() {
		if ( ! current_user_can( 'manage_msp_sales' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-reports.php';
	}

	/**
	 * Display the settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		if ( ! current_user_can( 'manage_msp' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-settings.php';
	}

	/**
	 * Add action links to the plugins page.
	 *
	 * @since    1.0.0
	 * @param    array    $links    The existing action links.
	 * @return   array              The modified action links.
	 */
	public function add_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=msp-settings' ) . '">' . __( 'Settings', 'merchmanager' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Load meta boxes.
	 *
	 * @since    1.0.0
	 */
	public function load_meta_boxes() {
		// Load meta box loader
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-merchmanager-meta-box-loader.php';

		// Initialize meta box loader
		$meta_box_loader = new Merchmanager_Meta_Box_Loader();
	}

	/**
	 * Get total sales count.
	 *
	 * @since    1.0.0
	 * @return   int    Total sales count
	 */
	public function get_total_sales() {
		// Placeholder implementation - will be replaced with actual database query
		return 0;
	}

	/**
	 * Get total revenue amount.
	 *
	 * @since    1.0.0
	 * @return   string    Formatted total revenue
	 */
	public function get_total_revenue() {
		// Placeholder implementation - will be replaced with actual database query
		return '€0.00';
	}

	/**
	 * Get count of active tours.
	 *
	 * @since    1.0.0
	 * @return   int    Active tours count
	 */
	public function get_active_tours_count() {
		// Placeholder implementation - will be replaced with actual database query
		return 0;
	}

	/**
	 * Get count of low stock items.
	 *
	 * @since    1.0.0
	 * @return   int    Low stock items count
	 */
	public function get_low_stock_count() {
		// Placeholder implementation - will be replaced with actual database query
		return 0;
	}

	/**
	 * Display recent sales table.
	 *
	 * @since    1.0.0
	 */
	public function display_recent_sales() {
		echo '<p>' . __( 'No recent sales found.', 'merchmanager' ) . '</p>';
	}

	/**
	 * Display top selling items.
	 *
	 * @since    1.0.0
	 */
	public function display_top_selling_items() {
		echo '<p>' . __( 'No top selling items found.', 'merchmanager' ) . '</p>';
	}

	/**
	 * Display upcoming shows.
	 *
	 * @since    1.0.0
	 */
	public function display_upcoming_shows() {
		echo '<p>' . __( 'No upcoming shows found.', 'merchmanager' ) . '</p>';
	}

	/**
	 * Display low stock alerts.
	 *
	 * @since    1.0.0
	 */
	public function display_low_stock_alerts() {
		echo '<p>' . __( 'No low stock alerts.', 'merchmanager' ) . '</p>';
	}

}
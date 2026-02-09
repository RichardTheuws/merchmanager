<?php
/**
 * Fired during plugin activation
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 * @author     Theuws Consulting
 */
class Merchmanager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Create custom database tables
		self::create_tables();

		// Set default settings if none exist
		self::set_default_settings();

		// Register custom post types
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/post-types/class-merchmanager-post-types.php';
		$post_types = new Merchmanager_Post_Types();
		$post_types->register_post_types();

		// Flush rewrite rules to add custom post types
		flush_rewrite_rules();

		// Create custom user roles
		self::create_user_roles();
	}

	/**
	 * Set default plugin settings on first activation.
	 *
	 * @since    1.0.3
	 */
	private static function set_default_settings() {
		if ( false !== get_option( 'msp_settings', false ) ) {
			return;
		}
		$defaults = array(
			'currency'              => 'EUR',
			'date_format'           => 'Y-m-d',
			'time_format'           => 'H:i',
			'low_stock_threshold'   => 5,
			'sales_page_expiry'     => 7,
			'management_manage_bands'       => 1,
			'management_manage_tours'       => 1,
			'management_manage_merchandise' => 1,
			'management_manage_sales'       => 1,
			'management_view_reports'       => 1,
			'tour_management_manage_bands'       => 0,
			'tour_management_manage_tours'       => 1,
			'tour_management_manage_merchandise' => 1,
			'tour_management_manage_sales'       => 1,
			'tour_management_view_reports'       => 1,
			'merch_sales_manage_bands'       => 0,
			'merch_sales_manage_tours'       => 0,
			'merch_sales_manage_merchandise' => 0,
			'merch_sales_manage_sales'       => 1,
			'merch_sales_view_reports'       => 0,
			'enable_email_notifications'     => 0,
			'notification_email'             => get_option( 'admin_email', '' ),
			'notify_low_stock'               => 1,
			'notify_sales_summary'           => 1,
			'notify_new_sales_page'          => 1,
			'data_retention'                 => 365,
			'debug_mode'                     => 0,
			'csv_delimiter'                  => ',',
			'remove_data'                    => 0,
		);
		update_option( 'msp_settings', $defaults );
		update_option( 'msp_remove_data_on_uninstall', 0 );
	}

	/**
	 * Create custom database tables
	 *
	 * @since    1.0.0
	 */
	private static function create_tables() {
		// Load database class
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/database/class-merchmanager-database.php';

		// Create tables
		$database = new Merchmanager_Database();
		$database->create_tables();

		// Insert sample data if needed
		if ( get_option( 'msp_insert_sample_data', false ) ) {
			$database->insert_sample_data();
			delete_option( 'msp_insert_sample_data' );
		}

		// Set database version
		update_option( 'msp_db_version', '1.0' );
	}

	/**
	 * Update user roles and capabilities
	 *
	 * @since    1.0.1
	 */
	public static function update_user_roles() {
		// Add custom capabilities to existing roles
		$admin_role = get_role( 'administrator' );
		if ( $admin_role ) {
			$admin_role->add_cap( 'manage_msp' );
			$admin_role->add_cap( 'manage_msp_tours' );
			$admin_role->add_cap( 'manage_msp_sales' );
		}

		// Update existing MSP roles with new capabilities
		$msp_management_role = get_role( 'msp_management' );
		if ( $msp_management_role ) {
			$msp_management_role->add_cap( 'manage_msp' );
		}

		$msp_tour_management_role = get_role( 'msp_tour_management' );
		if ( $msp_tour_management_role ) {
			$msp_tour_management_role->add_cap( 'manage_msp_tours' );
		}

		$msp_merch_sales_role = get_role( 'msp_merch_sales' );
		if ( $msp_merch_sales_role ) {
			$msp_merch_sales_role->add_cap( 'manage_msp_sales' );
		}
	}

	/**
	 * Create custom user roles
	 *
	 * @since    1.0.0
	 */
	private static function create_user_roles() {
		// MSP Management role
		add_role(
			'msp_management',
			__('MSP Management', 'merchmanager'),
			array(
				'read' => true,
				'edit_posts' => true,
				'delete_posts' => true,
				'publish_posts' => true,
				'upload_files' => true,
				'manage_options' => false,
				'manage_msp' => true, // Custom capability for full MSP access
			)
		);

		// MSP Tour Management role
		add_role(
			'msp_tour_management',
			__('MSP Tour Management', 'merchmanager'),
			array(
				'read' => true,
				'edit_posts' => true,
				'delete_posts' => false,
				'publish_posts' => true,
				'upload_files' => true,
				'manage_options' => false,
				'manage_msp_tours' => true, // Custom capability for tour management
			)
		);

		// MSP Merch Sales role
		add_role(
			'msp_merch_sales',
			__('MSP Merch Sales', 'merchmanager'),
			array(
				'read' => true,
				'edit_posts' => false,
				'delete_posts' => false,
				'publish_posts' => false,
				'upload_files' => false,
				'manage_options' => false,
				'manage_msp_sales' => true, // Custom capability for sales management
			)
		);

		// Add custom capabilities to administrator role
		$admin_role = get_role( 'administrator' );
		if ( $admin_role ) {
			$admin_role->add_cap( 'manage_msp' );
			$admin_role->add_cap( 'manage_msp_tours' );
			$admin_role->add_cap( 'manage_msp_sales' );
		}
	}
}
<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://theuws.com
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
 * @author     Theuws Consulting
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

		// Add Setup Wizard submenu - always registered so users can re-run from Settings
		add_submenu_page(
			'merchmanager',
			__( 'Setup Wizard', 'merchmanager' ),
			__( 'Setup Wizard', 'merchmanager' ),
			'manage_msp',
			'merchmanager-onboarding',
			array( $this, 'display_onboarding_page' )
		);
	}

	/**
	 * Reorder MerchManager submenu for logical hierarchy.
	 *
	 * @since 1.0.3
	 */
	public function reorder_merchmanager_submenu() {
		global $submenu;
		if ( empty( $submenu['merchmanager'] ) ) {
			return;
		}
		$order = array(
			'merchmanager'                    => 0,  // Dashboard
			'edit.php?post_type=msp_band'     => 1,  // Bands
			'edit.php?post_type=msp_tour'     => 2,  // Tours
			'edit.php?post_type=msp_show'     => 3,  // Shows
			'edit.php?post_type=msp_merchandise' => 4,  // Merchandise
			'edit.php?post_type=msp_sales_page'  => 5,  // Sales Pages
			'msp-sales'                       => 6,  // Sales
			'msp-reports'                     => 7,  // Reports
			'msp-settings'                    => 8,  // Settings
			'merchmanager-onboarding'         => 9,  // Setup Wizard
		);
		$items = $submenu['merchmanager'];
		uasort( $items, function ( $a, $b ) use ( $order ) {
			$slug_a = isset( $a[2] ) ? $a[2] : '';
			$slug_b = isset( $b[2] ) ? $b[2] : '';
			$pos_a  = isset( $order[ $slug_a ] ) ? $order[ $slug_a ] : 99;
			$pos_b  = isset( $order[ $slug_b ] ) ? $order[ $slug_b ] : 99;
			return $pos_a - $pos_b;
		} );
		$submenu['merchmanager'] = array_values( $items );
	}

	/**
	 * Display the dashboard page.
	 *
	 * @since    1.0.0
	 */
	public function display_dashboard_page() {
		if ( ! current_user_can( 'manage_msp' ) ) {
			wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) ) );
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
			wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) ) );
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
			wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-reports.php';
	}

	/**
	 * Redirect to onboarding wizard on first run.
	 *
	 * @since    1.0.3
	 */
	public function maybe_redirect_to_onboarding() {
		// Skip on AJAX, cron, or if user cannot manage MSP
		if ( wp_doing_ajax() || wp_doing_cron() || ! current_user_can( 'manage_msp' ) ) {
			return;
		}
		// Skip if onboarding already complete
		if ( get_option( 'merchmanager_onboarding_complete', false ) ) {
			return;
		}
		// Skip if already on onboarding page
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( 'merchmanager-onboarding' === $page ) {
			return;
		}
		// Redirect when visiting any MerchManager admin page (Dashboard, Sales, Reports, Settings)
		$msp_pages = array( 'merchmanager', 'msp-sales', 'msp-reports', 'msp-settings' );
		if ( in_array( $page, $msp_pages, true ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=merchmanager-onboarding' ) );
			exit;
		}
	}

	/**
	 * Display the onboarding wizard page.
	 *
	 * @since    1.0.3
	 */
	public function display_onboarding_page() {
		if ( ! current_user_can( 'manage_msp' ) ) {
			wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-onboarding.php';
	}

	/**
	 * Display the settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		if ( ! current_user_can( 'manage_msp' ) ) {
			wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.', 'merchmanager' ) ) );
		}
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/merchmanager-admin-settings.php';
	}

	/**
	 * Register plugin settings with WordPress Settings API.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			'msp_settings',
			'msp_settings',
			array(
				'type'              => 'array',
				'description'       => __( 'MerchManager settings.', 'merchmanager' ),
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @since    1.0.0
	 * @param    array    $input    Raw settings input.
	 * @return   array              Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$existing = get_option( 'msp_settings', array() );
		$sanitized = is_array( $existing ) ? $existing : array();
		$allowed_keys = array(
			'currency', 'date_format', 'time_format', 'low_stock_threshold', 'sales_page_expiry',
			'management_manage_bands', 'management_manage_tours', 'management_manage_merchandise',
			'management_manage_sales', 'management_view_reports',
			'tour_management_manage_bands', 'tour_management_manage_tours', 'tour_management_manage_merchandise',
			'tour_management_manage_sales', 'tour_management_view_reports',
			'merch_sales_manage_bands', 'merch_sales_manage_tours', 'merch_sales_manage_merchandise',
			'merch_sales_manage_sales', 'merch_sales_view_reports',
			'enable_email_notifications', 'notification_email',
			'notify_low_stock', 'notify_sales_summary', 'notify_new_sales_page',
			'data_retention', 'debug_mode', 'csv_delimiter', 'remove_data',
		);
		$checkbox_keys = array(
			'management_manage_bands', 'management_manage_tours', 'management_manage_merchandise',
			'management_manage_sales', 'management_view_reports',
			'tour_management_manage_bands', 'tour_management_manage_tours', 'tour_management_manage_merchandise',
			'tour_management_manage_sales', 'tour_management_view_reports',
			'merch_sales_manage_bands', 'merch_sales_manage_tours', 'merch_sales_manage_merchandise',
			'merch_sales_manage_sales', 'merch_sales_view_reports',
			'enable_email_notifications', 'notify_low_stock', 'notify_sales_summary', 'notify_new_sales_page',
			'debug_mode', 'remove_data',
		);
		foreach ( $allowed_keys as $key ) {
			if ( isset( $input[ $key ] ) ) {
				if ( in_array( $key, array( 'low_stock_threshold', 'sales_page_expiry', 'data_retention' ), true ) ) {
					$sanitized[ $key ] = absint( $input[ $key ] );
				} elseif ( 'notification_email' === $key ) {
					$sanitized[ $key ] = sanitize_email( $input[ $key ] );
				} elseif ( in_array( $key, array( 'currency', 'date_format', 'time_format', 'csv_delimiter' ), true ) ) {
					$sanitized[ $key ] = sanitize_text_field( $input[ $key ] );
				} elseif ( in_array( $key, $checkbox_keys, true ) ) {
					$sanitized[ $key ] = $input[ $key ] ? 1 : 0;
				}
			} elseif ( in_array( $key, $checkbox_keys, true ) ) {
				$sanitized[ $key ] = 0;
			}
		}
		// Sync remove_data to standalone option for uninstall.php
		if ( isset( $sanitized['remove_data'] ) ) {
			update_option( 'msp_remove_data_on_uninstall', $sanitized['remove_data'] ? 1 : 0 );
		}
		return $sanitized;
	}

	/**
	 * Handle report CSV export request.
	 *
	 * @since    1.0.3
	 */
	public function maybe_handle_report_csv_export() {
		$is_summary = isset( $_GET['msp_export_csv'] ) && $_GET['msp_export_csv'] === '1';
		$is_detail  = isset( $_GET['msp_export_sales_detail'] ) && $_GET['msp_export_sales_detail'] === '1';
		if ( ! $is_summary && ! $is_detail ) {
			return;
		}
		if ( ! current_user_can( 'manage_msp_sales' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'merchmanager' ) );
		}
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'msp_export_sales_report' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'merchmanager' ) );
		}
		$band_id    = isset( $_GET['band_id'] ) ? absint( $_GET['band_id'] ) : 0;
		$start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : gmdate( 'Y-m-01' );
		$end_date   = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : gmdate( 'Y-m-d' );

		if ( $is_detail ) {
			// P2.1: Row-level export for Excel (Jim – margin analysis).
			require_once MERCHMANAGER_PLUGIN_DIR . 'includes/services/class-merchmanager-sales-service.php';
			$sales_service = new Merchmanager_Sales_Service();
			$args          = array(
				'band_id'    => $band_id,
				'start_date' => $start_date,
				'end_date'   => $end_date,
			);
			$filename = 'sales-detail-' . gmdate( 'Y-m-d' ) . '.csv';
			$filepath = wp_tempnam( $filename );
			$result   = $sales_service->export_sales_to_csv( $args, $filepath );
			if ( empty( $result['success'] ) ) {
				wp_die( esc_html( $result['message'] ?? __( 'Export failed.', 'merchmanager' ) ) );
			}
			nocache_headers();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Temp file from wp_tempnam(); stream for download.
			readfile( $filepath );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Delete temp file after download.
			@unlink( $filepath );
			exit;
		}

		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/services/class-merchmanager-report-service.php';
		$report_service = new Merchmanager_Report_Service();
		$report         = $report_service->generate_sales_report( array(
			'band_id'    => $band_id,
			'start_date' => $start_date,
			'end_date'   => $end_date,
		) );

		if ( ! empty( $report['integrity_error'] ) ) {
			wp_die( esc_html( $report['integrity_message'] ?? __( 'Data consistency check failed. Export aborted.', 'merchmanager' ) ) );
		}

		$filename = 'sales-report-' . gmdate( 'Y-m-d' ) . '.csv';
		$filepath = wp_tempnam( $filename );
		$result   = $report_service->export_report_to_csv( $report, 'sales', $filepath );

		if ( empty( $result['success'] ) ) {
			wp_die( esc_html( $result['message'] ?? __( 'Export failed.', 'merchmanager' ) ) );
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Temp file from wp_tempnam(); stream for download.
		readfile( $filepath );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Delete temp file after download.
		@unlink( $filepath );
		exit;
	}

	/**
	 * Handle low stock CSV export for reorder (P2.3).
	 *
	 * @since    1.1.4
	 */
	public function maybe_handle_low_stock_export() {
		if ( ! isset( $_GET['msp_export_low_stock'] ) || $_GET['msp_export_low_stock'] !== '1' ) {
			return;
		}
		if ( ! current_user_can( 'manage_msp_merchandise' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'merchmanager' ) );
		}
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'msp_export_low_stock' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'merchmanager' ) );
		}
		$band_id = isset( $_GET['band_id'] ) ? absint( $_GET['band_id'] ) : 0;
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/services/class-merchmanager-stock-service.php';
		$stock_service   = new Merchmanager_Stock_Service();
		$low_stock_items = $stock_service->get_low_stock_items( array( 'band_id' => $band_id ) );
		$options        = get_option( 'msp_settings', array() );
		$delimiter      = isset( $options['csv_delimiter'] ) && $options['csv_delimiter'] === 'semicolon' ? ';' : ',';
		$filename       = 'low-stock-reorder-' . gmdate( 'Y-m-d' ) . '.csv';
		$filepath       = wp_tempnam( $filename );
		$fh             = fopen( $filepath, 'w' );
		if ( ! $fh ) {
			wp_die( esc_html__( 'Export failed.', 'merchmanager' ) );
		}
		fputcsv( $fh, array( __( 'Item', 'merchmanager' ), __( 'SKU', 'merchmanager' ), __( 'Current Stock', 'merchmanager' ), __( 'Threshold', 'merchmanager' ), __( 'Band', 'merchmanager' ) ), $delimiter );
		foreach ( $low_stock_items as $item ) {
			$threshold = $item->get_low_stock_threshold();
			if ( ! $threshold ) {
				$threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;
			}
			$band_name = '';
			$bid       = $item->get_band_id();
			if ( $bid ) {
				$band = get_post( $bid );
				$band_name = $band && $band->post_type === 'msp_band' ? $band->post_title : '';
			}
			fputcsv( $fh, array( $item->get_name(), $item->get_sku(), $item->get_stock(), $threshold, $band_name ), $delimiter );
		}
		fclose( $fh );
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Temp file for download.
		readfile( $filepath );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Delete temp file after download.
		@unlink( $filepath );
		exit;
	}

	/**
	 * Handle tour shows import from CSV.
	 *
	 * @since    1.0.3
	 */
	public function handle_tour_import_shows() {
		if ( ! isset( $_POST['msp_import_shows_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_import_shows_nonce'] ) ), 'msp_import_shows' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'merchmanager' ) );
		}
		if ( ! current_user_can( 'manage_msp_tours' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'merchmanager' ) );
		}
		$tour_id = isset( $_POST['tour_id'] ) ? absint( $_POST['tour_id'] ) : 0;
		if ( ! $tour_id || get_post_type( $tour_id ) !== 'msp_tour' ) {
			wp_die( esc_html__( 'Invalid tour.', 'merchmanager' ) );
		}
		if ( empty( $_FILES['msp_import_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['msp_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( array( 'msp_import_error' => 'no_file' ), admin_url( 'post.php?post=' . $tour_id . '&action=edit' ) ) );
			exit;
		}
		$file = $_FILES['msp_import_file']['tmp_name'];
		$mapping = isset( $_POST['msp_import_mapping'] ) && is_array( $_POST['msp_import_mapping'] ) ? array_map( 'absint', wp_unslash( $_POST['msp_import_mapping'] ) ) : array();
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-tour.php';
		$tour = new Merchmanager_Tour( $tour_id );
		$result = $tour->import_shows_from_csv( $file, $mapping );
		$redirect = add_query_arg( array( 'msp_import_result' => $result['imported'] ?? 0, 'msp_import_skipped' => $result['skipped'] ?? 0 ), admin_url( 'post.php?post=' . $tour_id . '&action=edit' ) );
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Handle tour shows export to CSV.
	 *
	 * @since    1.0.3
	 */
	public function handle_tour_export_shows() {
		if ( ! isset( $_POST['msp_export_shows_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_export_shows_nonce'] ) ), 'msp_export_shows' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'merchmanager' ) );
		}
		if ( ! current_user_can( 'manage_msp_tours' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'merchmanager' ) );
		}
		$tour_id = isset( $_POST['tour_id'] ) ? absint( $_POST['tour_id'] ) : 0;
		if ( ! $tour_id || get_post_type( $tour_id ) !== 'msp_tour' ) {
			wp_die( esc_html__( 'Invalid tour.', 'merchmanager' ) );
		}
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-tour.php';
		$tour = new Merchmanager_Tour( $tour_id );
		$filename = 'shows-tour-' . $tour_id . '-' . gmdate( 'Y-m-d' ) . '.csv';
		$filepath = wp_tempnam( $filename );
		$result = $tour->export_shows_to_csv( $filepath );
		if ( empty( $result['success'] ) ) {
			wp_die( esc_html( $result['message'] ?? __( 'Export failed.', 'merchmanager' ) ) );
		}
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Temp file from wp_tempnam(); stream for download.
		readfile( $filepath );
		// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Delete temp file after download.
		@unlink( $filepath );
		exit;
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
	 * Get services for dashboard data.
	 *
	 * @since    1.0.0
	 * @return   array    Array of service instances.
	 */
	private function get_dashboard_services() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/class-merchmanager-sales-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/services/class-merchmanager-stock-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-merchmanager-merchandise.php';
		return array(
			'sales' => new Merchmanager_Sales_Service(),
			'stock' => new Merchmanager_Stock_Service(),
		);
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
		$services = $this->get_dashboard_services();
		$summary = $services['sales']->get_sales_summary( array() );
		if ( ! empty( $summary ) && isset( $summary[0]->count ) ) {
			return (int) $summary[0]->count;
		}
		return 0;
	}

	/**
	 * Get total revenue amount.
	 *
	 * @since    1.0.0
	 * @return   string    Formatted total revenue
	 */
	public function get_total_revenue() {
		$options = get_option( 'msp_settings', array() );
		$currency = isset( $options['currency'] ) ? $options['currency'] : 'EUR';
		$symbols = array( 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => '$', 'AUD' => '$' );
		$symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '€';
		$services = $this->get_dashboard_services();
		$summary = $services['sales']->get_sales_summary( array() );
		if ( ! empty( $summary ) && isset( $summary[0]->total_amount ) ) {
			return $symbol . number_format( (float) $summary[0]->total_amount, 2 );
		}
		return $symbol . '0.00';
	}

	/**
	 * Get count of active tours.
	 *
	 * @since    1.0.0
	 * @return   int    Active tours count
	 */
	public function get_active_tours_count() {
		$tours = get_posts( array(
			'post_type'      => 'msp_tour',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => '_msp_tour_status',
					'value' => 'active',
				),
			),
		) );
		return count( $tours );
	}

	/**
	 * Get count of low stock items.
	 *
	 * @since    1.0.0
	 * @return   int    Low stock items count
	 */
	public function get_low_stock_count() {
		$services = $this->get_dashboard_services();
		$items = $services['stock']->get_low_stock_items( array() );
		return count( $items );
	}

	/**
	 * Display recent sales table.
	 *
	 * @since    1.0.0
	 */
	public function display_recent_sales() {
		$options = get_option( 'msp_settings', array() );
		$currency = isset( $options['currency'] ) ? $options['currency'] : 'EUR';
		$symbols = array( 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => '$', 'AUD' => '$' );
		$symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '€';
		$services = $this->get_dashboard_services();
		$sales = $services['sales']->get_sales( array( 'limit' => 10, 'orderby' => 'date', 'order' => 'DESC' ) );
		if ( ! empty( $sales ) ) {
			echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Date', 'merchmanager' ) . '</th><th>' . esc_html__( 'Item', 'merchmanager' ) . '</th><th>' . esc_html__( 'Qty', 'merchmanager' ) . '</th><th>' . esc_html__( 'Amount', 'merchmanager' ) . '</th></tr></thead><tbody>';
			foreach ( $sales as $sale ) {
				$merch = new Merchmanager_Merchandise( $sale->merchandise_id );
				$amount = (float) $sale->price * (int) $sale->quantity;
				echo '<tr><td>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $sale->date ) ) ) . '</td><td>' . esc_html( $merch->get_name() ) . '</td><td>' . esc_html( $sale->quantity ) . '</td><td>' . esc_html( $symbol . number_format( $amount, 2 ) ) . '</td></tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>' . esc_html__( 'No recent sales found.', 'merchmanager' ) . '</p>';
		}
	}

	/**
	 * Display top selling items.
	 *
	 * @since    1.0.0
	 */
	public function display_top_selling_items() {
		$options = get_option( 'msp_settings', array() );
		$currency = isset( $options['currency'] ) ? $options['currency'] : 'EUR';
		$symbols = array( 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => '$', 'AUD' => '$' );
		$symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '€';
		$services = $this->get_dashboard_services();
		$top = $services['sales']->get_top_selling_merchandise( array() );
		if ( ! empty( $top ) ) {
			echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Item', 'merchmanager' ) . '</th><th>' . esc_html__( 'Sold', 'merchmanager' ) . '</th><th>' . esc_html__( 'Revenue', 'merchmanager' ) . '</th></tr></thead><tbody>';
			foreach ( array_slice( $top, 0, 5 ) as $item ) {
				$name = isset( $item->merchandise_name ) ? $item->merchandise_name : __( 'Unknown', 'merchmanager' );
				$qty = isset( $item->total_quantity ) ? $item->total_quantity : 0;
				$amt = isset( $item->total_amount ) ? $item->total_amount : 0;
				echo '<tr><td>' . esc_html( $name ) . '</td><td>' . esc_html( $qty ) . '</td><td>' . esc_html( $symbol . number_format( (float) $amt, 2 ) ) . '</td></tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>' . esc_html__( 'No top selling items found.', 'merchmanager' ) . '</p>';
		}
	}

	/**
	 * Display upcoming shows.
	 *
	 * @since    1.0.0
	 */
	public function display_upcoming_shows() {
		$shows = get_posts( array(
			'post_type'      => 'msp_show',
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'meta_key'       => '_msp_show_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_msp_show_date',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '>=',
					'type'    => 'DATE',
				),
			),
		) );
		if ( ! empty( $shows ) ) {
			echo '<ul>';
			foreach ( $shows as $show ) {
				$date = get_post_meta( $show->ID, '_msp_show_date', true );
				$venue = get_post_meta( $show->ID, '_msp_show_venue_name', true );
				$date_str = $date ? date_i18n( get_option( 'date_format' ), strtotime( $date ) ) : '';
				echo '<li><a href="' . esc_url( get_edit_post_link( $show->ID ) ) . '">' . esc_html( $show->post_title ) . '</a>';
				if ( $date_str ) {
					echo ' - ' . esc_html( $date_str );
				}
				if ( $venue ) {
					echo ' (' . esc_html( $venue ) . ')';
				}
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . esc_html__( 'No upcoming shows found.', 'merchmanager' ) . '</p>';
		}
	}

	/**
	 * Display low stock alerts.
	 *
	 * @since    1.0.0
	 */
	public function display_low_stock_alerts() {
		$services = $this->get_dashboard_services();
		$items = $services['stock']->get_low_stock_items( array() );
		if ( ! empty( $items ) ) {
			echo '<ul>';
			foreach ( array_slice( $items, 0, 5 ) as $item ) {
				echo '<li><a href="' . esc_url( get_edit_post_link( $item->get_id() ) ) . '">' . esc_html( $item->get_name() ) . '</a> - ' . esc_html( sprintf(
					/* translators: %1$d: stock quantity */
					__( '%1$d in stock', 'merchmanager' ),
					$item->get_stock()
				) ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . esc_html__( 'No low stock alerts.', 'merchmanager' ) . '</p>';
		}
	}

}
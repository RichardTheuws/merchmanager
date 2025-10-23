<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/public
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/merchmanager-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/merchmanager-public.js', array( 'jquery' ), $this->version, false );
		
		// Localize script with AJAX URL
		wp_localize_script( $this->plugin_name, 'msp_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'msp_nonce' ),
		) );

	}
	
	/**
	 * Register shortcodes.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		
		// Sales page shortcode
		add_shortcode( 'msp_sales_page', array( $this, 'sales_page_shortcode' ) );
		
		// Band dashboard shortcode
		add_shortcode( 'msp_band_dashboard', array( $this, 'band_dashboard_shortcode' ) );
		
		// Sales recording shortcode
		add_shortcode( 'msp_sales_recording', array( $this, 'sales_recording_shortcode' ) );
		
	}
	
	/**
	 * Sales page shortcode callback.
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes.
	 * @return   string            Shortcode output.
	 */
	public function sales_page_shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'msp_sales_page' );
		
		// Check if sales page exists
		$sales_page_id = absint( $atts['id'] );
		if ( ! $sales_page_id || 'msp_sales_page' !== get_post_type( $sales_page_id ) ) {
			return '<p>' . __( 'Sales page not found.', 'merchmanager' ) . '</p>';
		}
		
		// Check if sales page is active
		$status = get_post_meta( $sales_page_id, '_msp_sales_page_status', true );
		if ( 'active' !== $status ) {
			return '<p>' . __( 'This sales page is no longer active.', 'merchmanager' ) . '</p>';
		}
		
		// Check if sales page has expired
		$expiry_date = get_post_meta( $sales_page_id, '_msp_sales_page_expiry_date', true );
		if ( $expiry_date && strtotime( $expiry_date ) < time() ) {
			return '<p>' . __( 'This sales page has expired.', 'merchmanager' ) . '</p>';
		}
		
		// Get sales page content
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/merchmanager-public-sales-page.php';
		return ob_get_clean();
	}
	
	/**
	 * Band dashboard shortcode callback.
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes.
	 * @return   string            Shortcode output.
	 */
	public function band_dashboard_shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
			'band_id' => 0,
		), $atts, 'msp_band_dashboard' );
		
		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return '<p>' . __( 'You must be logged in to view the band dashboard.', 'merchmanager' ) . '</p>';
		}
		
		// Get band ID
		$band_id = absint( $atts['band_id'] );
		
		// If no band ID is provided, try to get the user's band
		if ( ! $band_id ) {
			$user_id = get_current_user_id();
			$user_bands = $this->get_user_bands( $user_id );
			
			if ( empty( $user_bands ) ) {
				return '<p>' . __( 'You are not associated with any bands.', 'merchmanager' ) . '</p>';
			}
			
			// If user has multiple bands, show a band selector
			if ( count( $user_bands ) > 1 ) {
				ob_start();
				include plugin_dir_path( __FILE__ ) . 'partials/merchmanager-public-band-selector.php';
				return ob_get_clean();
			}
			
			// If user has only one band, use that
			$band_id = $user_bands[0]->ID;
		}
		
		// Check if band exists
		if ( ! $band_id || 'msp_band' !== get_post_type( $band_id ) ) {
			return '<p>' . __( 'Band not found.', 'merchmanager' ) . '</p>';
		}
		
		// Check if user has access to this band
		$user_id = get_current_user_id();
		$user_bands = $this->get_user_bands( $user_id );
		$has_access = false;
		
		foreach ( $user_bands as $user_band ) {
			if ( $user_band->ID === $band_id ) {
				$has_access = true;
				break;
			}
		}
		
		if ( ! $has_access && ! current_user_can( 'manage_options' ) ) {
			return '<p>' . __( 'You do not have access to this band.', 'merchmanager' ) . '</p>';
		}
		
		// Get band dashboard content
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/merchmanager-public-band-dashboard.php';
		return ob_get_clean();
	}
	
	/**
	 * Get bands associated with a user.
	 *
	 * @since    1.0.0
	 * @param    int      $user_id    User ID.
	 * @return   array                Array of band post objects.
	 */
	private function get_user_bands( $user_id ) {
		
		// If user is admin, return all bands
		if ( current_user_can( 'manage_options' ) ) {
			return get_posts( array(
				'post_type'      => 'msp_band',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			) );
		}
		
		// Get bands associated with user
		$bands = array();
		$band_ids = get_user_meta( $user_id, '_msp_associated_bands', true );
		
		if ( ! empty( $band_ids ) && is_array( $band_ids ) ) {
			$bands = get_posts( array(
				'post_type'      => 'msp_band',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'post__in'       => $band_ids,
			) );
		}
		
		return $bands;
	}

	/**
	 * Sales recording shortcode callback.
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes.
	 * @return   string            Shortcode output.
	 */
	public function sales_recording_shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
			'band_id' => 0,
			'show_id' => 0,
		), $atts, 'msp_sales_recording' );
		
		// Check if user has permission
		if ( ! current_user_can( 'edit_posts' ) ) {
			return '<p>' . __( 'You do not have permission to record sales.', 'merchmanager' ) . '</p>';
		}
		
		// Get sales recording content
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/merchmanager-public-sales-recording.php';
		return ob_get_clean();
	}

}
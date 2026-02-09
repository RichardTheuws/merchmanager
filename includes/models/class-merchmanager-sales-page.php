<?php
/**
 * The sales page model class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 */

/**
 * The sales page model class.
 *
 * This class represents a sales page entity and provides methods for CRUD operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 * @author     Theuws Consulting
 */
class Merchmanager_Sales_Page {

    /**
     * The ID of the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $id    The ID of the sales page.
     */
    private $id;

    /**
     * The name of the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The name of the sales page.
     */
    private $name;

    /**
     * The ID of the show associated with the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $show_id    The ID of the show associated with the sales page.
     */
    private $show_id;

    /**
     * The ID of the band associated with the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $band_id    The ID of the band associated with the sales page.
     */
    private $band_id;

    /**
     * The access code for the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $access_code    The access code for the sales page.
     */
    private $access_code;

    /**
     * The status of the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $status    The status of the sales page.
     */
    private $status;

    /**
     * The expiry date of the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $expiry_date    The expiry date of the sales page.
     */
    private $expiry_date;

    /**
     * The merchandise IDs associated with the sales page.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $merchandise    The merchandise IDs associated with the sales page.
     */
    private $merchandise;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    int       $id      The ID of the sales page.
     * @param    string    $name    The name of the sales page.
     */
    public function __construct( $id = 0, $name = '' ) {
        $this->id = $id;
        $this->name = $name;
        $this->status = 'active';
        $this->merchandise = array();

        // If ID is provided, load the sales page data
        if ( $id > 0 ) {
            $this->load();
        }
    }

    /**
     * Load sales page data from the database.
     *
     * @since    1.0.0
     * @return   bool    True if data was loaded, false otherwise.
     */
    public function load() {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Get post
        $post = get_post( $this->id );
        if ( ! $post || 'msp_sales_page' !== $post->post_type ) {
            return false;
        }

        // Set properties
        $this->name = $post->post_title;
        $this->show_id = get_post_meta( $this->id, '_msp_sales_page_show_id', true );
        $this->band_id = get_post_meta( $this->id, '_msp_sales_page_band_id', true );
        $this->access_code = get_post_meta( $this->id, '_msp_sales_page_access_code', true );
        $this->status = get_post_meta( $this->id, '_msp_sales_page_status', true );
        $this->expiry_date = get_post_meta( $this->id, '_msp_sales_page_expiry_date', true );
        $this->merchandise = get_post_meta( $this->id, '_msp_sales_page_merchandise', true );

        // Ensure merchandise is an array
        if ( ! is_array( $this->merchandise ) ) {
            $this->merchandise = array();
        }

        return true;
    }

    /**
     * Save sales page data to the database.
     *
     * @since    1.0.0
     * @return   int|WP_Error    The sales page ID on success, WP_Error on failure.
     */
    public function save() {
        // Prepare post data
        $post_data = array(
            'post_title'   => $this->name,
            'post_status'  => 'publish',
            'post_type'    => 'msp_sales_page',
        );

        // Filter post data
        $post_data = apply_filters( 'msp_sales_page_data', $post_data, $this );

        // Insert or update post
        if ( $this->id > 0 ) {
            $post_data['ID'] = $this->id;
            $result = wp_update_post( $post_data, true );
        } else {
            $result = wp_insert_post( $post_data, true );
        }

        // Check for errors
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Set ID if new post
        $this->id = $result;

        // Save meta data
        update_post_meta( $this->id, '_msp_sales_page_show_id', $this->show_id );
        update_post_meta( $this->id, '_msp_sales_page_band_id', $this->band_id );
        update_post_meta( $this->id, '_msp_sales_page_access_code', $this->access_code );
        update_post_meta( $this->id, '_msp_sales_page_status', $this->status );
        update_post_meta( $this->id, '_msp_sales_page_expiry_date', $this->expiry_date );
        update_post_meta( $this->id, '_msp_sales_page_merchandise', $this->merchandise );

        // Trigger action after save
        do_action( 'msp_after_sales_page_save', $this->id, $post_data );

        return $this->id;
    }

    /**
     * Delete the sales page from the database.
     *
     * @since    1.0.0
     * @param    bool    $force_delete    Whether to bypass trash and force deletion.
     * @return   bool|WP_Error            True on success, false or WP_Error on failure.
     */
    public function delete( $force_delete = false ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Trigger action before delete
        do_action( 'msp_before_sales_page_delete', $this->id );

        // Delete post
        $result = wp_delete_post( $this->id, $force_delete );

        // Check for errors
        if ( ! $result ) {
            return false;
        }

        // Trigger action after delete
        do_action( 'msp_after_sales_page_delete', $this->id );

        // Reset ID
        $this->id = 0;

        return true;
    }

    /**
     * Get all sales pages.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Sales_Page objects.
     */
    public static function get_all( $args = array() ) {
        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_sales_page',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Filter arguments
        $args = apply_filters( 'msp_sales_page_query_args', $args );

        // Get posts
        $posts = get_posts( $args );

        // Create sales page objects
        $sales_pages = array();
        foreach ( $posts as $post ) {
            $sales_pages[] = new self( $post->ID );
        }

        return $sales_pages;
    }

    /**
     * Get sales page by ID.
     *
     * @since    1.0.0
     * @param    int       $id    The sales page ID.
     * @return   Merchmanager_Sales_Page|false    Sales page object on success, false on failure.
     */
    public static function get_by_id( $id ) {
        $sales_page = new self( $id );
        return $sales_page->id > 0 ? $sales_page : false;
    }

    /**
     * Get sales page by show ID.
     *
     * @since    1.0.0
     * @param    int       $show_id    The show ID.
     * @return   Merchmanager_Sales_Page|false    Sales page object on success, false on failure.
     */
    public static function get_by_show_id( $show_id ) {
        // Get sales page
        $args = array(
            'post_type'      => 'msp_sales_page',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_sales_page_show_id',
                    'value' => $show_id,
                ),
            ),
        );
        $posts = get_posts( $args );

        if ( empty( $posts ) ) {
            return false;
        }

        return new self( $posts[0]->ID );
    }

    /**
     * Get sales for this sales page.
     *
     * @since    1.0.0
     * @return   array    Array of sales data.
     */
    public function get_sales() {
        global $wpdb;

        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array();
        }

        // Get sales from database (WP 6.2+ %i for identifier).
        $sales = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE sales_page_id = %d ORDER BY date DESC',
                $wpdb->prefix . 'msp_sales',
                $this->id
            )
        );

        return $sales;
    }

    /**
     * Get total sales amount for this sales page.
     *
     * @since    1.0.0
     * @return   float    Total sales amount.
     */
    public function get_total_sales_amount() {
        global $wpdb;

        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return 0;
        }

        // Get total sales amount from database (WP 6.2+ %i for identifier).
        $total = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT SUM(price * quantity) FROM %i WHERE sales_page_id = %d',
                $wpdb->prefix . 'msp_sales',
                $this->id
            )
        );

        return (float) $total;
    }

    /**
     * Generate a unique access code.
     *
     * @since    1.0.0
     * @param    int       $length    The length of the access code.
     * @return   string               The generated access code.
     */
    public function generate_access_code( $length = 6 ) {
        // Characters to use in access code
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen( $characters );
        
        // Generate access code
        $access_code = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $access_code .= $characters[ wp_rand( 0, $characters_length - 1 ) ];
        }
        
        // Set access code
        $this->access_code = $access_code;
        
        return $access_code;
    }

    /**
     * Get the URL for this sales page.
     *
     * @since    1.0.0
     * @return   string    The URL for this sales page.
     */
    public function get_url() {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return '';
        }

        // Get permalink
        $permalink = get_permalink( $this->id );
        
        return $permalink;
    }

    /**
     * Get the shortcode for this sales page.
     *
     * @since    1.0.0
     * @return   string    The shortcode for this sales page.
     */
    public function get_shortcode() {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return '';
        }

        return '[msp_sales_page id="' . $this->id . '"]';
    }

    /**
     * Check if the sales page has expired.
     *
     * @since    1.0.0
     * @return   bool    True if expired, false otherwise.
     */
    public function is_expired() {
        // Check if expiry date is set
        if ( ! $this->expiry_date ) {
            return false;
        }

        // Check if expiry date has passed
        $current_time = current_time( 'timestamp' );
        $expiry_time = strtotime( $this->expiry_date );
        
        return $expiry_time < $current_time;
    }

    /**
     * Get the ID of the sales page.
     *
     * @since    1.0.0
     * @return   int    The ID of the sales page.
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the name of the sales page.
     *
     * @since    1.0.0
     * @return   string    The name of the sales page.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the sales page.
     *
     * @since    1.0.0
     * @param    string    $name    The name of the sales page.
     * @return   void
     */
    public function set_name( $name ) {
        $this->name = $name;
    }

    /**
     * Get the ID of the show associated with the sales page.
     *
     * @since    1.0.0
     * @return   int    The ID of the show associated with the sales page.
     */
    public function get_show_id() {
        return $this->show_id;
    }

    /**
     * Set the ID of the show associated with the sales page.
     *
     * @since    1.0.0
     * @param    int    $show_id    The ID of the show associated with the sales page.
     * @return   void
     */
    public function set_show_id( $show_id ) {
        $this->show_id = $show_id;
    }

    /**
     * Get the ID of the band associated with the sales page.
     *
     * @since    1.0.0
     * @return   int    The ID of the band associated with the sales page.
     */
    public function get_band_id() {
        return $this->band_id;
    }

    /**
     * Set the ID of the band associated with the sales page.
     *
     * @since    1.0.0
     * @param    int    $band_id    The ID of the band associated with the sales page.
     * @return   void
     */
    public function set_band_id( $band_id ) {
        $this->band_id = $band_id;
    }

    /**
     * Get the access code for the sales page.
     *
     * @since    1.0.0
     * @return   string    The access code for the sales page.
     */
    public function get_access_code() {
        return $this->access_code;
    }

    /**
     * Set the access code for the sales page.
     *
     * @since    1.0.0
     * @param    string    $access_code    The access code for the sales page.
     * @return   void
     */
    public function set_access_code( $access_code ) {
        $this->access_code = $access_code;
    }

    /**
     * Get the status of the sales page.
     *
     * @since    1.0.0
     * @return   string    The status of the sales page.
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * Set the status of the sales page.
     *
     * @since    1.0.0
     * @param    string    $status    The status of the sales page.
     * @return   void
     */
    public function set_status( $status ) {
        $this->status = $status;
    }

    /**
     * Get the expiry date of the sales page.
     *
     * @since    1.0.0
     * @return   string    The expiry date of the sales page.
     */
    public function get_expiry_date() {
        return $this->expiry_date;
    }

    /**
     * Set the expiry date of the sales page.
     *
     * @since    1.0.0
     * @param    string    $expiry_date    The expiry date of the sales page.
     * @return   void
     */
    public function set_expiry_date( $expiry_date ) {
        $this->expiry_date = $expiry_date;
    }

    /**
     * Get the merchandise IDs associated with the sales page.
     *
     * @since    1.0.0
     * @return   array    The merchandise IDs associated with the sales page.
     */
    public function get_merchandise() {
        return $this->merchandise;
    }

    /**
     * Set the merchandise IDs associated with the sales page.
     *
     * @since    1.0.0
     * @param    array    $merchandise    The merchandise IDs associated with the sales page.
     * @return   void
     */
    public function set_merchandise( $merchandise ) {
        $this->merchandise = $merchandise;
    }

    /**
     * Add a merchandise ID to the sales page.
     *
     * @since    1.0.0
     * @param    int    $merchandise_id    The merchandise ID to add.
     * @return   void
     */
    public function add_merchandise( $merchandise_id ) {
        if ( ! in_array( $merchandise_id, $this->merchandise, true ) ) {
            $this->merchandise[] = $merchandise_id;
        }
    }

    /**
     * Remove a merchandise ID from the sales page.
     *
     * @since    1.0.0
     * @param    int    $merchandise_id    The merchandise ID to remove.
     * @return   void
     */
    public function remove_merchandise( $merchandise_id ) {
        $key = array_search( $merchandise_id, $this->merchandise, true );
        if ( false !== $key ) {
            unset( $this->merchandise[ $key ] );
            $this->merchandise = array_values( $this->merchandise );
        }
    }
}
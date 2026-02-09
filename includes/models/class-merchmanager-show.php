<?php
/**
 * The show model class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 */

/**
 * The show model class.
 *
 * This class represents a show entity and provides methods for CRUD operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 * @author     Theuws Consulting
 */
class Merchmanager_Show {

    /**
     * The ID of the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $id    The ID of the show.
     */
    private $id;

    /**
     * The name of the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The name of the show.
     */
    private $name;

    /**
     * The description of the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $description    The description of the show.
     */
    private $description;

    /**
     * The ID of the tour associated with the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $tour_id    The ID of the tour associated with the show.
     */
    private $tour_id;

    /**
     * The date and time of the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $date    The date and time of the show.
     */
    private $date;

    /**
     * The venue name for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_name    The venue name for the show.
     */
    private $venue_name;

    /**
     * The venue address for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_address    The venue address for the show.
     */
    private $venue_address;

    /**
     * The venue city for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_city    The venue city for the show.
     */
    private $venue_city;

    /**
     * The venue state/province for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_state    The venue state/province for the show.
     */
    private $venue_state;

    /**
     * The venue country for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_country    The venue country for the show.
     */
    private $venue_country;

    /**
     * The venue postal code for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_postal_code    The venue postal code for the show.
     */
    private $venue_postal_code;

    /**
     * The venue contact information for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $venue_contact    The venue contact information for the show.
     */
    private $venue_contact;

    /**
     * Additional notes for the show.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $notes    Additional notes for the show.
     */
    private $notes;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    int       $id             The ID of the show.
     * @param    string    $name           The name of the show.
     * @param    string    $description    The description of the show.
     */
    public function __construct( $id = 0, $name = '', $description = '' ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;

        // If ID is provided, load the show data
        if ( $id > 0 ) {
            $this->load();
        }
    }

    /**
     * Load show data from the database.
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
        if ( ! $post || 'msp_show' !== $post->post_type ) {
            return false;
        }

        // Set properties
        $this->name = $post->post_title;
        $this->description = $post->post_content;
        $this->tour_id = get_post_meta( $this->id, '_msp_show_tour_id', true );
        $this->date = get_post_meta( $this->id, '_msp_show_date', true );
        $this->venue_name = get_post_meta( $this->id, '_msp_show_venue_name', true );
        $this->venue_address = get_post_meta( $this->id, '_msp_show_venue_address', true );
        $this->venue_city = get_post_meta( $this->id, '_msp_show_venue_city', true );
        $this->venue_state = get_post_meta( $this->id, '_msp_show_venue_state', true );
        $this->venue_country = get_post_meta( $this->id, '_msp_show_venue_country', true );
        $this->venue_postal_code = get_post_meta( $this->id, '_msp_show_venue_postal_code', true );
        $this->venue_contact = get_post_meta( $this->id, '_msp_show_venue_contact', true );
        $this->notes = get_post_meta( $this->id, '_msp_show_notes', true );

        return true;
    }

    /**
     * Save show data to the database.
     *
     * @since    1.0.0
     * @return   int|WP_Error    The show ID on success, WP_Error on failure.
     */
    public function save() {
        // Prepare post data
        $post_data = array(
            'post_title'   => $this->name,
            'post_content' => $this->description,
            'post_status'  => 'publish',
            'post_type'    => 'msp_show',
        );

        // Filter post data
        $post_data = apply_filters( 'msp_show_data', $post_data, $this );

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
        update_post_meta( $this->id, '_msp_show_tour_id', $this->tour_id );
        update_post_meta( $this->id, '_msp_show_date', $this->date );
        update_post_meta( $this->id, '_msp_show_venue_name', $this->venue_name );
        update_post_meta( $this->id, '_msp_show_venue_address', $this->venue_address );
        update_post_meta( $this->id, '_msp_show_venue_city', $this->venue_city );
        update_post_meta( $this->id, '_msp_show_venue_state', $this->venue_state );
        update_post_meta( $this->id, '_msp_show_venue_country', $this->venue_country );
        update_post_meta( $this->id, '_msp_show_venue_postal_code', $this->venue_postal_code );
        update_post_meta( $this->id, '_msp_show_venue_contact', $this->venue_contact );
        update_post_meta( $this->id, '_msp_show_notes', $this->notes );

        // Trigger action after save
        do_action( 'msp_after_show_save', $this->id, $post_data );

        return $this->id;
    }

    /**
     * Delete the show from the database.
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
        do_action( 'msp_before_show_delete', $this->id );

        // Delete post
        $result = wp_delete_post( $this->id, $force_delete );

        // Check for errors
        if ( ! $result ) {
            return false;
        }

        // Trigger action after delete
        do_action( 'msp_after_show_delete', $this->id );

        // Reset ID
        $this->id = 0;

        return true;
    }

    /**
     * Get all shows.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Show objects.
     */
    public static function get_all( $args = array() ) {
        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_show',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_key'       => '_msp_show_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Filter arguments
        $args = apply_filters( 'msp_show_query_args', $args );

        // Get posts
        $posts = get_posts( $args );

        // Create show objects
        $shows = array();
        foreach ( $posts as $post ) {
            $shows[] = new self( $post->ID );
        }

        return $shows;
    }

    /**
     * Get show by ID.
     *
     * @since    1.0.0
     * @param    int       $id    The show ID.
     * @return   Merchmanager_Show|false    Show object on success, false on failure.
     */
    public static function get_by_id( $id ) {
        $show = new self( $id );
        return $show->id > 0 ? $show : false;
    }

    /**
     * Get upcoming shows.
     *
     * @since    1.0.0
     * @param    int       $limit    Maximum number of shows to return.
     * @return   array               Array of Merchmanager_Show objects.
     */
    public static function get_upcoming( $limit = 10 ) {
        // Get current date
        $current_date = gmdate( 'Y-m-d H:i:s' );

        // Default arguments
        $args = array(
            'post_type'      => 'msp_show',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'meta_key'       => '_msp_show_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => array(
                array(
                    'key'     => '_msp_show_date',
                    'value'   => $current_date,
                    'compare' => '>=',
                    'type'    => 'DATETIME',
                ),
            ),
        );

        // Get posts
        $posts = get_posts( $args );

        // Create show objects
        $shows = array();
        foreach ( $posts as $post ) {
            $shows[] = new self( $post->ID );
        }

        return $shows;
    }

    /**
     * Get the tour associated with this show.
     *
     * @since    1.0.0
     * @return   Merchmanager_Tour|false    Tour object on success, false on failure.
     */
    public function get_tour() {
        // Check if tour ID is valid
        if ( ! $this->tour_id ) {
            return false;
        }

        return new Merchmanager_Tour( $this->tour_id );
    }

    /**
     * Get the band associated with this show's tour.
     *
     * @since    1.0.0
     * @return   Merchmanager_Band|false    Band object on success, false on failure.
     */
    public function get_band() {
        // Get tour
        $tour = $this->get_tour();
        if ( ! $tour ) {
            return false;
        }

        // Get band
        return $tour->get_band();
    }

    /**
     * Get sales for this show.
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
                'SELECT * FROM %i WHERE show_id = %d ORDER BY date DESC',
                $wpdb->prefix . 'msp_sales',
                $this->id
            )
        );

        return $sales;
    }

    /**
     * Get total sales amount for this show.
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
                'SELECT SUM(price * quantity) FROM %i WHERE show_id = %d',
                $wpdb->prefix . 'msp_sales',
                $this->id
            )
        );

        return (float) $total;
    }

    /**
     * Get sales page for this show.
     *
     * @since    1.0.0
     * @return   Merchmanager_Sales_Page|false    Sales page object on success, false on failure.
     */
    public function get_sales_page() {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Get sales page
        $args = array(
            'post_type'      => 'msp_sales_page',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_sales_page_show_id',
                    'value' => $this->id,
                ),
            ),
        );
        $posts = get_posts( $args );

        if ( empty( $posts ) ) {
            return false;
        }

        return new Merchmanager_Sales_Page( $posts[0]->ID );
    }

    /**
     * Generate a sales page for this show.
     *
     * @since    1.0.0
     * @param    string    $title          The title for the sales page.
     * @param    array     $merchandise    Array of merchandise IDs to include.
     * @param    string    $access_code    Optional access code for the sales page.
     * @param    string    $expiry_date    Optional expiry date for the sales page.
     * @return   Merchmanager_Sales_Page|WP_Error    Sales page object on success, WP_Error on failure.
     */
    public function generate_sales_page( $title, $merchandise = array(), $access_code = '', $expiry_date = '' ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return new WP_Error( 'invalid_show', __( 'Invalid show ID.', 'merchmanager' ) );
        }

        // Check if a sales page already exists
        $existing_page = $this->get_sales_page();
        if ( $existing_page ) {
            return new WP_Error( 'sales_page_exists', __( 'A sales page already exists for this show.', 'merchmanager' ) );
        }

        // Get band ID
        $band = $this->get_band();
        if ( ! $band ) {
            return new WP_Error( 'invalid_band', __( 'Could not determine band for this show.', 'merchmanager' ) );
        }

        // Create sales page
        $sales_page = new Merchmanager_Sales_Page();
        $sales_page->set_name( $title );
        $sales_page->set_show_id( $this->id );
        $sales_page->set_band_id( $band->get_id() );
        $sales_page->set_merchandise( $merchandise );
        $sales_page->set_status( 'active' );

        // Set access code if provided
        if ( ! empty( $access_code ) ) {
            $sales_page->set_access_code( $access_code );
        }

        // Set expiry date if provided
        if ( ! empty( $expiry_date ) ) {
            $sales_page->set_expiry_date( $expiry_date );
        } else {
            // Use default expiry date from settings
            $options = get_option( 'msp_settings', array() );
            $default_expiry_days = isset( $options['sales_page_expiry'] ) ? $options['sales_page_expiry'] : 7;
            $expiry_date = gmdate( 'Y-m-d H:i:s', strtotime( '+' . $default_expiry_days . ' days' ) );
            $sales_page->set_expiry_date( $expiry_date );
        }

        // Save sales page
        $result = $sales_page->save();
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return $sales_page;
    }

    /**
     * Get the ID of the show.
     *
     * @since    1.0.0
     * @return   int    The ID of the show.
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the name of the show.
     *
     * @since    1.0.0
     * @return   string    The name of the show.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the show.
     *
     * @since    1.0.0
     * @param    string    $name    The name of the show.
     * @return   void
     */
    public function set_name( $name ) {
        $this->name = $name;
    }

    /**
     * Get the description of the show.
     *
     * @since    1.0.0
     * @return   string    The description of the show.
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set the description of the show.
     *
     * @since    1.0.0
     * @param    string    $description    The description of the show.
     * @return   void
     */
    public function set_description( $description ) {
        $this->description = $description;
    }

    /**
     * Get the ID of the tour associated with the show.
     *
     * @since    1.0.0
     * @return   int    The ID of the tour associated with the show.
     */
    public function get_tour_id() {
        return $this->tour_id;
    }

    /**
     * Set the ID of the tour associated with the show.
     *
     * @since    1.0.0
     * @param    int    $tour_id    The ID of the tour associated with the show.
     * @return   void
     */
    public function set_tour_id( $tour_id ) {
        $this->tour_id = $tour_id;
    }

    /**
     * Get the date and time of the show.
     *
     * @since    1.0.0
     * @return   string    The date and time of the show.
     */
    public function get_date() {
        return $this->date;
    }

    /**
     * Set the date and time of the show.
     *
     * @since    1.0.0
     * @param    string    $date    The date and time of the show.
     * @return   void
     */
    public function set_date( $date ) {
        $this->date = $date;
    }

    /**
     * Get the venue name for the show.
     *
     * @since    1.0.0
     * @return   string    The venue name for the show.
     */
    public function get_venue_name() {
        return $this->venue_name;
    }

    /**
     * Set the venue name for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_name    The venue name for the show.
     * @return   void
     */
    public function set_venue_name( $venue_name ) {
        $this->venue_name = $venue_name;
    }

    /**
     * Get the venue address for the show.
     *
     * @since    1.0.0
     * @return   string    The venue address for the show.
     */
    public function get_venue_address() {
        return $this->venue_address;
    }

    /**
     * Set the venue address for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_address    The venue address for the show.
     * @return   void
     */
    public function set_venue_address( $venue_address ) {
        $this->venue_address = $venue_address;
    }

    /**
     * Get the venue city for the show.
     *
     * @since    1.0.0
     * @return   string    The venue city for the show.
     */
    public function get_venue_city() {
        return $this->venue_city;
    }

    /**
     * Set the venue city for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_city    The venue city for the show.
     * @return   void
     */
    public function set_venue_city( $venue_city ) {
        $this->venue_city = $venue_city;
    }

    /**
     * Get the venue state/province for the show.
     *
     * @since    1.0.0
     * @return   string    The venue state/province for the show.
     */
    public function get_venue_state() {
        return $this->venue_state;
    }

    /**
     * Set the venue state/province for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_state    The venue state/province for the show.
     * @return   void
     */
    public function set_venue_state( $venue_state ) {
        $this->venue_state = $venue_state;
    }

    /**
     * Get the venue country for the show.
     *
     * @since    1.0.0
     * @return   string    The venue country for the show.
     */
    public function get_venue_country() {
        return $this->venue_country;
    }

    /**
     * Set the venue country for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_country    The venue country for the show.
     * @return   void
     */
    public function set_venue_country( $venue_country ) {
        $this->venue_country = $venue_country;
    }

    /**
     * Get the venue postal code for the show.
     *
     * @since    1.0.0
     * @return   string    The venue postal code for the show.
     */
    public function get_venue_postal_code() {
        return $this->venue_postal_code;
    }

    /**
     * Set the venue postal code for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_postal_code    The venue postal code for the show.
     * @return   void
     */
    public function set_venue_postal_code( $venue_postal_code ) {
        $this->venue_postal_code = $venue_postal_code;
    }

    /**
     * Get the venue contact information for the show.
     *
     * @since    1.0.0
     * @return   string    The venue contact information for the show.
     */
    public function get_venue_contact() {
        return $this->venue_contact;
    }

    /**
     * Set the venue contact information for the show.
     *
     * @since    1.0.0
     * @param    string    $venue_contact    The venue contact information for the show.
     * @return   void
     */
    public function set_venue_contact( $venue_contact ) {
        $this->venue_contact = $venue_contact;
    }

    /**
     * Get additional notes for the show.
     *
     * @since    1.0.0
     * @return   string    Additional notes for the show.
     */
    public function get_notes() {
        return $this->notes;
    }

    /**
     * Set additional notes for the show.
     *
     * @since    1.0.0
     * @param    string    $notes    Additional notes for the show.
     * @return   void
     */
    public function set_notes( $notes ) {
        $this->notes = $notes;
    }
}
<?php
/**
 * The band model class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 */

/**
 * The band model class.
 *
 * This class represents a band entity and provides methods for CRUD operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 * @author     Theuws Consulting
 */
class Merchmanager_Band {

    /**
     * The ID of the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $id    The ID of the band.
     */
    private $id;

    /**
     * The name of the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The name of the band.
     */
    private $name;

    /**
     * The description of the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $description    The description of the band.
     */
    private $description;

    /**
     * The contact name for the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $contact_name    The contact name for the band.
     */
    private $contact_name;

    /**
     * The contact email for the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $contact_email    The contact email for the band.
     */
    private $contact_email;

    /**
     * The contact phone for the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $contact_phone    The contact phone for the band.
     */
    private $contact_phone;

    /**
     * The website URL for the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $website    The website URL for the band.
     */
    private $website;

    /**
     * The social media links for the band.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $social_media    The social media links for the band.
     */
    private $social_media;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    int       $id             The ID of the band.
     * @param    string    $name           The name of the band.
     * @param    string    $description    The description of the band.
     */
    public function __construct( $id = 0, $name = '', $description = '' ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->social_media = array();

        // If ID is provided, load the band data
        if ( $id > 0 ) {
            $this->load();
        }
    }

    /**
     * Load band data from the database.
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
        if ( ! $post || 'msp_band' !== $post->post_type ) {
            return false;
        }

        // Set properties
        $this->name = $post->post_title;
        $this->description = $post->post_content;
        $this->contact_name = get_post_meta( $this->id, '_msp_band_contact_name', true );
        $this->contact_email = get_post_meta( $this->id, '_msp_band_contact_email', true );
        $this->contact_phone = get_post_meta( $this->id, '_msp_band_contact_phone', true );
        $this->website = get_post_meta( $this->id, '_msp_band_website', true );
        $this->social_media = get_post_meta( $this->id, '_msp_band_social_media', true );

        // Ensure social_media is an array
        if ( ! is_array( $this->social_media ) ) {
            $this->social_media = array();
        }

        return true;
    }

    /**
     * Save band data to the database.
     *
     * @since    1.0.0
     * @return   int|WP_Error    The band ID on success, WP_Error on failure.
     */
    public function save() {
        // Prepare post data
        $post_data = array(
            'post_title'   => $this->name,
            'post_content' => $this->description,
            'post_status'  => 'publish',
            'post_type'    => 'msp_band',
        );

        // Filter post data
        $post_data = apply_filters( 'msp_band_data', $post_data, $this );

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
        update_post_meta( $this->id, '_msp_band_contact_name', $this->contact_name );
        update_post_meta( $this->id, '_msp_band_contact_email', $this->contact_email );
        update_post_meta( $this->id, '_msp_band_contact_phone', $this->contact_phone );
        update_post_meta( $this->id, '_msp_band_website', $this->website );
        update_post_meta( $this->id, '_msp_band_social_media', $this->social_media );

        // Trigger action after save
        do_action( 'msp_after_band_save', $this->id, $post_data );

        return $this->id;
    }

    /**
     * Delete the band from the database.
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
        do_action( 'msp_before_band_delete', $this->id );

        // Delete post
        $result = wp_delete_post( $this->id, $force_delete );

        // Check for errors
        if ( ! $result ) {
            return false;
        }

        // Trigger action after delete
        do_action( 'msp_after_band_delete', $this->id );

        // Reset ID
        $this->id = 0;

        return true;
    }

    /**
     * Get all bands.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Band objects.
     */
    public static function get_all( $args = array() ) {
        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_band',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Filter arguments
        $args = apply_filters( 'msp_band_query_args', $args );

        // Get posts
        $posts = get_posts( $args );

        // Create band objects
        $bands = array();
        foreach ( $posts as $post ) {
            $bands[] = new self( $post->ID );
        }

        return $bands;
    }

    /**
     * Get band by ID.
     *
     * @since    1.0.0
     * @param    int       $id    The band ID.
     * @return   Merchmanager_Band|false    Band object on success, false on failure.
     */
    public static function get_by_id( $id ) {
        $band = new self( $id );
        return $band->id > 0 ? $band : false;
    }

    /**
     * Get tours for this band.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Tour objects.
     */
    public function get_tours( $args = array() ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array();
        }

        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_tour',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_tour_band_id',
                    'value' => $this->id,
                ),
            ),
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Get posts
        $posts = get_posts( $args );

        // Create tour objects
        $tours = array();
        foreach ( $posts as $post ) {
            $tours[] = new Merchmanager_Tour( $post->ID );
        }

        return $tours;
    }

    /**
     * Get merchandise for this band.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Merchandise objects.
     */
    public function get_merchandise( $args = array() ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array();
        }

        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_merchandise_band_id',
                    'value' => $this->id,
                ),
            ),
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Get posts
        $posts = get_posts( $args );

        // Create merchandise objects
        $merchandise = array();
        foreach ( $posts as $post ) {
            $merchandise[] = new Merchmanager_Merchandise( $post->ID );
        }

        return $merchandise;
    }

    /**
     * Get users associated with this band.
     *
     * @since    1.0.0
     * @return   array    Array of WP_User objects.
     */
    public function get_users() {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array();
        }

        // Get users with this band in their meta
        $users = get_users( array(
            'meta_query' => array(
                array(
                    'key'     => '_msp_associated_bands',
                    'value'   => sprintf( 'i:%d;', $this->id ),
                    'compare' => 'LIKE',
                ),
            ),
        ) );

        return $users;
    }

    /**
     * Associate a user with this band.
     *
     * @since    1.0.0
     * @param    int       $user_id    The user ID.
     * @param    string    $role       The role for the user in this band.
     * @return   bool                  True on success, false on failure.
     */
    public function add_user( $user_id, $role = 'member' ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Get user's associated bands
        $bands = get_user_meta( $user_id, '_msp_associated_bands', true );
        if ( ! is_array( $bands ) ) {
            $bands = array();
        }

        // Add this band if not already associated
        if ( ! in_array( $this->id, $bands, true ) ) {
            $bands[] = $this->id;
            update_user_meta( $user_id, '_msp_associated_bands', $bands );
        }

        // Set user's role in this band
        update_user_meta( $user_id, '_msp_band_' . $this->id . '_role', $role );

        return true;
    }

    /**
     * Remove a user's association with this band.
     *
     * @since    1.0.0
     * @param    int       $user_id    The user ID.
     * @return   bool                  True on success, false on failure.
     */
    public function remove_user( $user_id ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Get user's associated bands
        $bands = get_user_meta( $user_id, '_msp_associated_bands', true );
        if ( ! is_array( $bands ) ) {
            return false;
        }

        // Remove this band if associated
        $key = array_search( $this->id, $bands, true );
        if ( false !== $key ) {
            unset( $bands[ $key ] );
            update_user_meta( $user_id, '_msp_associated_bands', array_values( $bands ) );
        }

        // Remove user's role in this band
        delete_user_meta( $user_id, '_msp_band_' . $this->id . '_role' );

        return true;
    }

    /**
     * Get the ID of the band.
     *
     * @since    1.0.0
     * @return   int    The ID of the band.
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the name of the band.
     *
     * @since    1.0.0
     * @return   string    The name of the band.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the band.
     *
     * @since    1.0.0
     * @param    string    $name    The name of the band.
     * @return   void
     */
    public function set_name( $name ) {
        $this->name = $name;
    }

    /**
     * Get the description of the band.
     *
     * @since    1.0.0
     * @return   string    The description of the band.
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set the description of the band.
     *
     * @since    1.0.0
     * @param    string    $description    The description of the band.
     * @return   void
     */
    public function set_description( $description ) {
        $this->description = $description;
    }

    /**
     * Get the contact name for the band.
     *
     * @since    1.0.0
     * @return   string    The contact name for the band.
     */
    public function get_contact_name() {
        return $this->contact_name;
    }

    /**
     * Set the contact name for the band.
     *
     * @since    1.0.0
     * @param    string    $contact_name    The contact name for the band.
     * @return   void
     */
    public function set_contact_name( $contact_name ) {
        $this->contact_name = $contact_name;
    }

    /**
     * Get the contact email for the band.
     *
     * @since    1.0.0
     * @return   string    The contact email for the band.
     */
    public function get_contact_email() {
        return $this->contact_email;
    }

    /**
     * Set the contact email for the band.
     *
     * @since    1.0.0
     * @param    string    $contact_email    The contact email for the band.
     * @return   void
     */
    public function set_contact_email( $contact_email ) {
        $this->contact_email = $contact_email;
    }

    /**
     * Get the contact phone for the band.
     *
     * @since    1.0.0
     * @return   string    The contact phone for the band.
     */
    public function get_contact_phone() {
        return $this->contact_phone;
    }

    /**
     * Set the contact phone for the band.
     *
     * @since    1.0.0
     * @param    string    $contact_phone    The contact phone for the band.
     * @return   void
     */
    public function set_contact_phone( $contact_phone ) {
        $this->contact_phone = $contact_phone;
    }

    /**
     * Get the website URL for the band.
     *
     * @since    1.0.0
     * @return   string    The website URL for the band.
     */
    public function get_website() {
        return $this->website;
    }

    /**
     * Set the website URL for the band.
     *
     * @since    1.0.0
     * @param    string    $website    The website URL for the band.
     * @return   void
     */
    public function set_website( $website ) {
        $this->website = $website;
    }

    /**
     * Get the social media links for the band.
     *
     * @since    1.0.0
     * @return   array    The social media links for the band.
     */
    public function get_social_media() {
        return $this->social_media;
    }

    /**
     * Set the social media links for the band.
     *
     * @since    1.0.0
     * @param    array    $social_media    The social media links for the band.
     * @return   void
     */
    public function set_social_media( $social_media ) {
        $this->social_media = $social_media;
    }

    /**
     * Add a social media link for the band.
     *
     * @since    1.0.0
     * @param    string    $platform    The social media platform.
     * @param    string    $url         The social media URL.
     * @return   void
     */
    public function add_social_media( $platform, $url ) {
        $this->social_media[ $platform ] = $url;
    }

    /**
     * Remove a social media link for the band.
     *
     * @since    1.0.0
     * @param    string    $platform    The social media platform.
     * @return   void
     */
    public function remove_social_media( $platform ) {
        if ( isset( $this->social_media[ $platform ] ) ) {
            unset( $this->social_media[ $platform ] );
        }
    }
}
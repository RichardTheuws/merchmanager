<?php
/**
 * The merchandise model class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 */

/**
 * The merchandise model class.
 *
 * This class represents a merchandise entity and provides methods for CRUD operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 * @author     Theuws Consulting
 */
class Merchmanager_Merchandise {

    /**
     * The ID of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $id    The ID of the merchandise.
     */
    private $id;

    /**
     * The name of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The name of the merchandise.
     */
    private $name;

    /**
     * The description of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $description    The description of the merchandise.
     */
    private $description;

    /**
     * The ID of the band associated with the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $band_id    The ID of the band associated with the merchandise.
     */
    private $band_id;

    /**
     * The SKU of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $sku    The SKU of the merchandise.
     */
    private $sku;

    /**
     * The price of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      float    $price    The price of the merchandise.
     */
    private $price;

    /**
     * The size of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $size    The size of the merchandise.
     */
    private $size;

    /**
     * The color of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $color    The color of the merchandise.
     */
    private $color;

    /**
     * The current stock level of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $stock    The current stock level of the merchandise.
     */
    private $stock;

    /**
     * The low stock threshold for the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $low_stock_threshold    The low stock threshold for the merchandise.
     */
    private $low_stock_threshold;

    /**
     * The supplier information for the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $supplier    The supplier information for the merchandise.
     */
    private $supplier;

    /**
     * The cost per unit of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      float    $cost    The cost per unit of the merchandise.
     */
    private $cost;

    /**
     * The category of the merchandise.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $category    The category of the merchandise.
     */
    private $category;

    /**
     * Whether the merchandise is active.
     *
     * @since    1.0.0
     * @access   private
     * @var      bool    $active    Whether the merchandise is active.
     */
    private $active;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    int       $id             The ID of the merchandise.
     * @param    string    $name           The name of the merchandise.
     * @param    string    $description    The description of the merchandise.
     */
    public function __construct( $id = 0, $name = '', $description = '' ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->active = true;

        // If ID is provided, load the merchandise data
        if ( $id > 0 ) {
            $this->load();
        }
    }

    /**
     * Load merchandise data from the database.
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
        if ( ! $post || 'msp_merchandise' !== $post->post_type ) {
            return false;
        }

        // Set properties
        $this->name = $post->post_title;
        $this->description = $post->post_content;
        $this->band_id = get_post_meta( $this->id, '_msp_merchandise_band_id', true );
        $this->sku = get_post_meta( $this->id, '_msp_merchandise_sku', true );
        $this->price = get_post_meta( $this->id, '_msp_merchandise_price', true );
        $this->size = get_post_meta( $this->id, '_msp_merchandise_size', true );
        $this->color = get_post_meta( $this->id, '_msp_merchandise_color', true );
        $this->stock = get_post_meta( $this->id, '_msp_merchandise_stock', true );
        $this->low_stock_threshold = get_post_meta( $this->id, '_msp_merchandise_low_stock_threshold', true );
        $this->supplier = get_post_meta( $this->id, '_msp_merchandise_supplier', true );
        $this->cost = get_post_meta( $this->id, '_msp_merchandise_cost', true );
        $this->category = get_post_meta( $this->id, '_msp_merchandise_category', true );
        $this->active = get_post_meta( $this->id, '_msp_merchandise_active', true );

        return true;
    }

    /**
     * Save merchandise data to the database.
     *
     * @since    1.0.0
     * @return   int|WP_Error    The merchandise ID on success, WP_Error on failure.
     */
    public function save() {
        // Prepare post data
        $post_data = array(
            'post_title'   => $this->name,
            'post_content' => $this->description,
            'post_status'  => 'publish',
            'post_type'    => 'msp_merchandise',
        );

        // Filter post data
        $post_data = apply_filters( 'msp_merchandise_data', $post_data, $this );

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
        update_post_meta( $this->id, '_msp_merchandise_band_id', $this->band_id );
        update_post_meta( $this->id, '_msp_merchandise_sku', $this->sku );
        update_post_meta( $this->id, '_msp_merchandise_price', $this->price );
        update_post_meta( $this->id, '_msp_merchandise_size', $this->size );
        update_post_meta( $this->id, '_msp_merchandise_color', $this->color );
        update_post_meta( $this->id, '_msp_merchandise_stock', $this->stock );
        update_post_meta( $this->id, '_msp_merchandise_low_stock_threshold', $this->low_stock_threshold );
        update_post_meta( $this->id, '_msp_merchandise_supplier', $this->supplier );
        update_post_meta( $this->id, '_msp_merchandise_cost', $this->cost );
        update_post_meta( $this->id, '_msp_merchandise_category', $this->category );
        update_post_meta( $this->id, '_msp_merchandise_active', $this->active );

        // Trigger action after save
        do_action( 'msp_after_merchandise_save', $this->id, $post_data );

        return $this->id;
    }

    /**
     * Delete the merchandise from the database.
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
        do_action( 'msp_before_merchandise_delete', $this->id );

        // Delete post
        $result = wp_delete_post( $this->id, $force_delete );

        // Check for errors
        if ( ! $result ) {
            return false;
        }

        // Trigger action after delete
        do_action( 'msp_after_merchandise_delete', $this->id );

        // Reset ID
        $this->id = 0;

        return true;
    }

    /**
     * Get all merchandise.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Merchandise objects.
     */
    public static function get_all( $args = array() ) {
        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Filter arguments
        $args = apply_filters( 'msp_merchandise_query_args', $args );

        // Get posts
        $posts = get_posts( $args );

        // Create merchandise objects
        $merchandise = array();
        foreach ( $posts as $post ) {
            $merchandise[] = new self( $post->ID );
        }

        return $merchandise;
    }

    /**
     * Get merchandise by ID.
     *
     * @since    1.0.0
     * @param    int       $id    The merchandise ID.
     * @return   Merchmanager_Merchandise|false    Merchandise object on success, false on failure.
     */
    public static function get_by_id( $id ) {
        $merchandise = new self( $id );
        return $merchandise->id > 0 ? $merchandise : false;
    }

    /**
     * Update stock level.
     *
     * @since    1.0.0
     * @param    int       $quantity       The quantity to add or subtract.
     * @param    string    $change_reason  The reason for the stock change.
     * @param    int       $user_id        The ID of the user making the change.
     * @return   bool                      True on success, false on failure.
     */
    public function update_stock( $quantity, $change_reason = 'manual', $user_id = 0 ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Get current stock
        $previous_stock = $this->stock;
        
        // Calculate new stock
        $new_stock = $previous_stock + $quantity;
        
        // Ensure stock is not negative
        if ( $new_stock < 0 ) {
            $new_stock = 0;
        }
        
        // Update stock
        $this->stock = $new_stock;
        update_post_meta( $this->id, '_msp_merchandise_stock', $new_stock );
        
        // Log stock change
        $this->log_stock_change( $previous_stock, $new_stock, $change_reason, $user_id );
        
        // Check for low stock
        $this->check_low_stock();
        
        // Trigger stock updated action
        do_action( 'msp_stock_updated', $this->id, $previous_stock, $new_stock, $change_reason );
        
        return true;
    }

    /**
     * Log stock change.
     *
     * @since    1.0.0
     * @param    int       $previous_stock  The previous stock level.
     * @param    int       $new_stock       The new stock level.
     * @param    string    $change_reason   The reason for the stock change.
     * @param    int       $user_id         The ID of the user making the change.
     * @return   int|false                  The log ID on success, false on failure.
     */
    private function log_stock_change( $previous_stock, $new_stock, $change_reason, $user_id ) {
        global $wpdb;
        
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }
        
        // Insert log entry
        $table_name = $wpdb->prefix . 'msp_stock_log';
        $result = $wpdb->insert(
            $table_name,
            array(
                'merchandise_id' => $this->id,
                'previous_stock' => $previous_stock,
                'new_stock'      => $new_stock,
                'change_reason'  => $change_reason,
                'user_id'        => $user_id,
                'created_at'     => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%d', '%s', '%d', '%s' )
        );
        
        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Check for low stock and create alert if needed.
     *
     * @since    1.0.0
     * @return   bool    True if low stock alert was created, false otherwise.
     */
    private function check_low_stock() {
        global $wpdb;
        
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }
        
        // Get low stock threshold
        $threshold = $this->low_stock_threshold;
        if ( ! $threshold ) {
            // Use default threshold from settings
            $options = get_option( 'msp_settings', array() );
            $threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;
        }
        
        // Check if stock is below threshold
        if ( $this->stock <= $threshold ) {
            // Check if alert already exists (WP 6.2+ %i for table identifier).
            $alert_exists = $wpdb->get_var( $wpdb->prepare(
                'SELECT COUNT(*) FROM %i WHERE merchandise_id = %d AND status = %s',
                $wpdb->prefix . 'msp_stock_alerts',
                $this->id,
                'active'
            ) );
            
            // Create alert if it doesn't exist
            if ( ! $alert_exists ) {
                $table_name = $wpdb->prefix . 'msp_stock_alerts';
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        'merchandise_id' => $this->id,
                        'threshold'      => $threshold,
                        'status'         => 'active',
                        'created_at'     => current_time( 'mysql' ),
                        'updated_at'     => current_time( 'mysql' ),
                    ),
                    array( '%d', '%d', '%s', '%s', '%s' )
                );
                
                // Trigger low stock alert action
                if ( $result ) {
                    do_action( 'msp_low_stock_alert', $this->id, $this->stock, $threshold );
                    return true;
                }
            }
        }
        
        return false;
    }

    // Getters and setters for all properties
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_name() {
        return $this->name;
    }
    
    public function set_name( $name ) {
        $this->name = $name;
    }
    
    public function get_description() {
        return $this->description;
    }
    
    public function set_description( $description ) {
        $this->description = $description;
    }
    
    public function get_band_id() {
        return $this->band_id;
    }
    
    public function set_band_id( $band_id ) {
        $this->band_id = $band_id;
    }
    
    public function get_sku() {
        return $this->sku;
    }
    
    public function set_sku( $sku ) {
        $this->sku = $sku;
    }
    
    public function get_price() {
        return $this->price;
    }
    
    public function set_price( $price ) {
        $this->price = $price;
    }
    
    public function get_size() {
        return $this->size;
    }
    
    public function set_size( $size ) {
        $this->size = $size;
    }
    
    public function get_color() {
        return $this->color;
    }
    
    public function set_color( $color ) {
        $this->color = $color;
    }
    
    public function get_stock() {
        return $this->stock;
    }
    
    public function set_stock( $stock ) {
        $this->stock = $stock;
    }
    
    public function get_low_stock_threshold() {
        return $this->low_stock_threshold;
    }
    
    public function set_low_stock_threshold( $threshold ) {
        $this->low_stock_threshold = $threshold;
    }
    
    public function get_supplier() {
        return $this->supplier;
    }
    
    public function set_supplier( $supplier ) {
        $this->supplier = $supplier;
    }
    
    public function get_cost() {
        return $this->cost;
    }
    
    public function set_cost( $cost ) {
        $this->cost = $cost;
    }
    
    public function get_category() {
        return $this->category;
    }
    
    public function set_category( $category ) {
        $this->category = $category;
    }
    
    public function is_active() {
        return $this->active;
    }
    
    public function set_active( $active ) {
        $this->active = $active;
    }
}
<?php
/**
 * The database class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/database
 */

/**
 * The database class.
 *
 * This class handles database operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/database
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Database {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Nothing to do here
    }

    /**
     * Create database tables.
     *
     * @since    1.0.0
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Sales table
        $table_name = $wpdb->prefix . 'msp_sales';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            date datetime NOT NULL,
            merchandise_id bigint(20) unsigned NOT NULL,
            quantity int(11) NOT NULL,
            price decimal(10,2) NOT NULL,
            payment_type varchar(50) NOT NULL,
            show_id bigint(20) unsigned DEFAULT NULL,
            sales_page_id bigint(20) unsigned DEFAULT NULL,
            band_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY merchandise_id (merchandise_id),
            KEY show_id (show_id),
            KEY sales_page_id (sales_page_id),
            KEY band_id (band_id),
            KEY user_id (user_id),
            KEY date (date)
        ) $charset_collate;";

        // Stock log table
        $table_name = $wpdb->prefix . 'msp_stock_log';
        $sql .= "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            merchandise_id bigint(20) unsigned NOT NULL,
            previous_stock int(11) NOT NULL,
            new_stock int(11) NOT NULL,
            change_reason varchar(50) NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY merchandise_id (merchandise_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Stock alerts table
        $table_name = $wpdb->prefix . 'msp_stock_alerts';
        $sql .= "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            merchandise_id bigint(20) unsigned NOT NULL,
            threshold int(11) NOT NULL,
            status varchar(20) NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY merchandise_id (merchandise_id),
            KEY status (status)
        ) $charset_collate;";

        // Include WordPress database upgrade functions
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // Execute SQL
        dbDelta( $sql );
    }

    /**
     * Check if database tables exist.
     *
     * @since    1.0.0
     * @return   bool    True if tables exist, false otherwise.
     */
    public function tables_exist() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'msp_sales',
            $wpdb->prefix . 'msp_stock_log',
            $wpdb->prefix . 'msp_stock_alerts',
        );

        foreach ( $tables as $table ) {
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );
            if ( ! $wpdb->get_var( $query ) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Drop database tables.
     *
     * @since    1.0.0
     */
    public function drop_tables() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'msp_sales',
            $wpdb->prefix . 'msp_stock_log',
            $wpdb->prefix . 'msp_stock_alerts',
        );

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS $table" );
        }
    }

    /**
     * Insert sample data.
     *
     * @since    1.0.0
     */
    public function insert_sample_data() {
        global $wpdb;

        // Check if sample data already exists
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}msp_sales" );
        if ( $count > 0 ) {
            return;
        }

        // Get bands
        $bands = get_posts( array(
            'post_type'      => 'msp_band',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );

        if ( empty( $bands ) ) {
            return;
        }

        $band_id = $bands[0]->ID;

        // Get merchandise
        $merchandise = get_posts( array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_merchandise_band_id',
                    'value' => $band_id,
                ),
            ),
        ) );

        if ( empty( $merchandise ) ) {
            return;
        }

        // Get shows
        $shows = get_posts( array(
            'post_type'      => 'msp_show',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => '_msp_show_date',
                    'value'   => date( 'Y-m-d', strtotime( '-30 days' ) ),
                    'compare' => '>=',
                    'type'    => 'DATE',
                ),
            ),
        ) );

        if ( empty( $shows ) ) {
            return;
        }

        // Insert sample sales
        $payment_types = array( 'cash', 'card', 'venmo' );
        $now = current_time( 'mysql' );

        foreach ( $shows as $show ) {
            $show_date = get_post_meta( $show->ID, '_msp_show_date', true );
            
            foreach ( $merchandise as $merch ) {
                $price = get_post_meta( $merch->ID, '_msp_merchandise_price', true );
                if ( ! $price ) {
                    $price = rand( 10, 30 );
                }
                
                $quantity = rand( 1, 5 );
                $payment_type = $payment_types[ array_rand( $payment_types ) ];
                
                $wpdb->insert(
                    $wpdb->prefix . 'msp_sales',
                    array(
                        'date'          => $show_date,
                        'merchandise_id' => $merch->ID,
                        'quantity'      => $quantity,
                        'price'         => $price,
                        'payment_type'  => $payment_type,
                        'show_id'       => $show->ID,
                        'band_id'       => $band_id,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ),
                    array( '%s', '%d', '%d', '%f', '%s', '%d', '%d', '%s', '%s' )
                );
                
                // Update stock
                $stock = get_post_meta( $merch->ID, '_msp_merchandise_stock', true );
                if ( $stock !== '' ) {
                    $previous_stock = $stock;
                    $new_stock = max( 0, $stock - $quantity );
                    update_post_meta( $merch->ID, '_msp_merchandise_stock', $new_stock );
                    
                    // Log stock change
                    $wpdb->insert(
                        $wpdb->prefix . 'msp_stock_log',
                        array(
                            'merchandise_id' => $merch->ID,
                            'previous_stock' => $previous_stock,
                            'new_stock'      => $new_stock,
                            'change_reason'  => 'sale',
                            'created_at'     => $now,
                        ),
                        array( '%d', '%d', '%d', '%s', '%s' )
                    );
                }
            }
        }
    }
}
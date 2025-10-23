<?php
/**
 * The stock service class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 */

/**
 * The stock service class.
 *
 * This class handles stock management operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Stock_Service {

    /**
     * Get low stock items.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Array of merchandise objects.
     */
    public function get_low_stock_items( $args = array() ) {
        // Default arguments
        $default_args = array(
            'band_id'           => 0,
            'category'          => '',
            'include_inactive'  => false,
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Prepare query arguments
        $query_args = array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(),
        );

        // Add band filter
        if ( $args['band_id'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_band_id',
                'value' => $args['band_id'],
            );
        }

        // Add category filter
        if ( $args['category'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_category',
                'value' => $args['category'],
            );
        }

        // Add active filter
        if ( ! $args['include_inactive'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_active',
                'value' => '1',
            );
        }

        // Get merchandise
        $merchandise_posts = get_posts( $query_args );
        $low_stock_items = array();

        foreach ( $merchandise_posts as $post ) {
            $merchandise = new Merchmanager_Merchandise( $post->ID );
            
            // Check if low stock
            $stock = $merchandise->get_stock();
            $threshold = $merchandise->get_low_stock_threshold();
            
            if ( ! $threshold ) {
                // Use default threshold from settings
                $options = get_option( 'msp_settings', array() );
                $threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;
            }
            
            if ( $stock <= $threshold && $stock > 0 ) {
                $low_stock_items[] = $merchandise;
            }
        }

        return $low_stock_items;
    }

    /**
     * Get out of stock items.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Array of merchandise objects.
     */
    public function get_out_of_stock_items( $args = array() ) {
        // Default arguments
        $default_args = array(
            'band_id'           => 0,
            'category'          => '',
            'include_inactive'  => false,
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Prepare query arguments
        $query_args = array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => '_msp_merchandise_stock',
                    'value'   => '0',
                    'compare' => '<=',
                    'type'    => 'NUMERIC',
                ),
            ),
        );

        // Add band filter
        if ( $args['band_id'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_band_id',
                'value' => $args['band_id'],
            );
        }

        // Add category filter
        if ( $args['category'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_category',
                'value' => $args['category'],
            );
        }

        // Add active filter
        if ( ! $args['include_inactive'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_active',
                'value' => '1',
            );
        }

        // Get merchandise
        $merchandise_posts = get_posts( $query_args );
        $out_of_stock_items = array();

        foreach ( $merchandise_posts as $post ) {
            $merchandise = new Merchmanager_Merchandise( $post->ID );
            $out_of_stock_items[] = $merchandise;
        }

        return $out_of_stock_items;
    }

    /**
     * Get stock alerts.
     *
     * @since    1.0.0
     * @param    string    $status    Alert status ('active', 'resolved', 'all').
     * @return   array                Array of stock alerts.
     */
    public function get_stock_alerts( $status = 'active' ) {
        global $wpdb;

        // Build query
        $table_name = $wpdb->prefix . 'msp_stock_alerts';
        $query = "SELECT * FROM $table_name";

        // Add status filter
        if ( $status !== 'all' ) {
            $query .= $wpdb->prepare( " WHERE status = %s", $status );
        }

        // Add order
        $query .= " ORDER BY created_at DESC";

        // Get alerts
        $alerts = $wpdb->get_results( $query );

        // Add merchandise data
        foreach ( $alerts as &$alert ) {
            $merchandise = new Merchmanager_Merchandise( $alert->merchandise_id );
            $alert->merchandise_name = $merchandise->get_name();
            $alert->merchandise_sku = $merchandise->get_sku();
            $alert->current_stock = $merchandise->get_stock();
        }

        return $alerts;
    }

    /**
     * Resolve a stock alert.
     *
     * @since    1.0.0
     * @param    int       $alert_id    The alert ID.
     * @return   bool|WP_Error          True on success, WP_Error on failure.
     */
    public function resolve_stock_alert( $alert_id ) {
        global $wpdb;

        // Get alert
        $table_name = $wpdb->prefix . 'msp_stock_alerts';
        $alert = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $alert_id ) );

        if ( ! $alert ) {
            return new WP_Error( 'invalid_alert', __( 'Alert not found.', 'merchmanager' ) );
        }

        // Update alert status
        $result = $wpdb->update(
            $table_name,
            array(
                'status'     => 'resolved',
                'updated_at' => current_time( 'mysql' ),
            ),
            array( 'id' => $alert_id ),
            array( '%s', '%s' ),
            array( '%d' )
        );

        // Check for errors
        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Error resolving alert.', 'merchmanager' ), $wpdb->last_error );
        }

        // Trigger action
        do_action( 'msp_stock_alert_resolved', $alert_id, $alert->merchandise_id );

        return true;
    }

    /**
     * Get stock log.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Array of stock log entries.
     */
    public function get_stock_log( $args = array() ) {
        global $wpdb;

        // Default arguments
        $default_args = array(
            'merchandise_id' => 0,
            'user_id'        => 0,
            'change_reason'  => '',
            'start_date'     => '',
            'end_date'       => '',
            'orderby'        => 'created_at',
            'order'          => 'DESC',
            'limit'          => 0,
            'offset'         => 0,
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Build query
        $table_name = $wpdb->prefix . 'msp_stock_log';
        $query = "SELECT * FROM $table_name WHERE 1=1";

        // Add filters
        if ( $args['merchandise_id'] ) {
            $query .= $wpdb->prepare( " AND merchandise_id = %d", $args['merchandise_id'] );
        }
        if ( $args['user_id'] ) {
            $query .= $wpdb->prepare( " AND user_id = %d", $args['user_id'] );
        }
        if ( $args['change_reason'] ) {
            $query .= $wpdb->prepare( " AND change_reason = %s", $args['change_reason'] );
        }
        if ( $args['start_date'] ) {
            $query .= $wpdb->prepare( " AND created_at >= %s", $args['start_date'] );
        }
        if ( $args['end_date'] ) {
            $query .= $wpdb->prepare( " AND created_at <= %s", $args['end_date'] );
        }

        // Add order
        $query .= " ORDER BY " . esc_sql( $args['orderby'] ) . " " . esc_sql( $args['order'] );

        // Add limit
        if ( $args['limit'] > 0 ) {
            $query .= $wpdb->prepare( " LIMIT %d", $args['limit'] );
            if ( $args['offset'] > 0 ) {
                $query .= $wpdb->prepare( " OFFSET %d", $args['offset'] );
            }
        }

        // Get log entries
        $log_entries = $wpdb->get_results( $query );

        // Add merchandise and user data
        foreach ( $log_entries as &$entry ) {
            $merchandise = new Merchmanager_Merchandise( $entry->merchandise_id );
            $entry->merchandise_name = $merchandise->get_name();
            $entry->merchandise_sku = $merchandise->get_sku();
            
            if ( $entry->user_id ) {
                $user = get_user_by( 'id', $entry->user_id );
                $entry->user_name = $user ? $user->display_name : '';
            } else {
                $entry->user_name = '';
            }
        }

        return $log_entries;
    }

    /**
     * Update stock levels for multiple merchandise items.
     *
     * @since    1.0.0
     * @param    array    $stock_updates    Array of stock updates.
     * @return   array                      Array of update results.
     */
    public function update_stock_levels( $stock_updates ) {
        $results = array();

        foreach ( $stock_updates as $update ) {
            // Check required fields
            if ( ! isset( $update['merchandise_id'] ) || ! isset( $update['quantity'] ) ) {
                $results[] = new WP_Error( 'missing_fields', __( 'Missing required fields.', 'merchmanager' ) );
                continue;
            }

            // Get merchandise
            $merchandise = new Merchmanager_Merchandise( $update['merchandise_id'] );
            if ( ! $merchandise->get_id() ) {
                $results[] = new WP_Error( 'invalid_merchandise', __( 'Invalid merchandise ID.', 'merchmanager' ) );
                continue;
            }

            // Set default values
            $reason = isset( $update['reason'] ) ? $update['reason'] : 'manual';
            $user_id = isset( $update['user_id'] ) ? $update['user_id'] : get_current_user_id();
            $notes = isset( $update['notes'] ) ? $update['notes'] : '';

            // Update stock
            $result = $merchandise->update_stock( $update['quantity'], $reason, $user_id, $notes );
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Send low stock alerts.
     *
     * @since    1.0.0
     * @return   array    Array of alert results.
     */
    public function send_low_stock_alerts() {
        // Check if email notifications are enabled
        $options = get_option( 'msp_settings', array() );
        $notifications_enabled = isset( $options['enable_email_notifications'] ) ? $options['enable_email_notifications'] : false;
        $notify_low_stock = isset( $options['notify_low_stock'] ) ? $options['notify_low_stock'] : false;

        if ( ! $notifications_enabled || ! $notify_low_stock ) {
            return array(
                'success' => false,
                'message' => __( 'Low stock email notifications are disabled.', 'merchmanager' ),
            );
        }

        // Get notification email
        $notification_email = isset( $options['notification_email'] ) ? $options['notification_email'] : get_option( 'admin_email' );

        // Get active low stock alerts
        $alerts = $this->get_stock_alerts( 'active' );

        if ( empty( $alerts ) ) {
            return array(
                'success' => true,
                'message' => __( 'No active low stock alerts found.', 'merchmanager' ),
            );
        }

        // Prepare email content
        $subject = sprintf( __( '[%s] Low Stock Alert', 'merchmanager' ), get_bloginfo( 'name' ) );
        
        $message = __( 'The following items are low in stock:', 'merchmanager' ) . "\n\n";
        
        foreach ( $alerts as $alert ) {
            $message .= sprintf(
                "%s (SKU: %s) - %d in stock (threshold: %d)\n",
                $alert->merchandise_name,
                $alert->merchandise_sku,
                $alert->current_stock,
                $alert->threshold
            );
        }
        
        $message .= "\n\n";
        $message .= __( 'Please log in to your website to manage inventory.', 'merchmanager' ) . "\n";
        $message .= admin_url( 'admin.php?page=msp-reports&tab=inventory' );

        // Send email
        $result = wp_mail( $notification_email, $subject, $message );

        return array(
            'success' => $result,
            'message' => $result ? 
                sprintf( __( 'Low stock alerts sent to %s.', 'merchmanager' ), $notification_email ) : 
                __( 'Failed to send low stock alerts.', 'merchmanager' ),
            'alerts' => $alerts,
        );
    }

    /**
     * Get stock level statistics.
     *
     * @since    1.0.0
     * @param    int      $band_id    The band ID (optional).
     * @return   array                Stock statistics.
     */
    public function get_stock_statistics( $band_id = 0 ) {
        // Prepare query arguments
        $query_args = array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_merchandise_active',
                    'value' => '1',
                ),
            ),
        );

        // Add band filter
        if ( $band_id ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_band_id',
                'value' => $band_id,
            );
        }

        // Get merchandise
        $merchandise_posts = get_posts( $query_args );
        
        // Initialize statistics
        $stats = array(
            'total_items' => count( $merchandise_posts ),
            'total_stock' => 0,
            'total_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
            'categories' => array(),
        );

        foreach ( $merchandise_posts as $post ) {
            $merchandise = new Merchmanager_Merchandise( $post->ID );
            
            $stock = $merchandise->get_stock();
            $price = $merchandise->get_price();
            $threshold = $merchandise->get_low_stock_threshold();
            $category = $merchandise->get_category();
            
            if ( ! $threshold ) {
                // Use default threshold from settings
                $options = get_option( 'msp_settings', array() );
                $threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;
            }
            
            // Update statistics
            $stats['total_stock'] += $stock;
            $stats['total_value'] += $stock * $price;
            
            if ( $stock <= $threshold && $stock > 0 ) {
                $stats['low_stock_count']++;
            }
            
            if ( $stock <= 0 ) {
                $stats['out_of_stock_count']++;
            }
            
            // Update category statistics
            if ( $category ) {
                if ( ! isset( $stats['categories'][ $category ] ) ) {
                    $stats['categories'][ $category ] = array(
                        'count' => 0,
                        'stock' => 0,
                        'value' => 0,
                    );
                }
                
                $stats['categories'][ $category ]['count']++;
                $stats['categories'][ $category ]['stock'] += $stock;
                $stats['categories'][ $category ]['value'] += $stock * $price;
            }
        }

        return $stats;
    }
}
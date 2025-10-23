<?php
/**
 * Test utilities for Merchmanager
 */

class MSP_Test_Utils {
    
    /**
     * Create a test band
     */
    public static function create_test_band( $args = array() ) {
        $defaults = array(
            'post_title'   => 'Test Band ' . uniqid(),
            'post_content' => 'Test band description',
            'post_status'  => 'publish',
            'post_type'    => 'msp_band'
        );
        
        $band_id = wp_insert_post( wp_parse_args( $args, $defaults ) );
        
        if ( $band_id ) {
            update_post_meta( $band_id, '_msp_band_contact_email', 'test@example.com' );
            update_post_meta( $band_id, '_msp_band_contact_phone', '+1234567890' );
        }
        
        return $band_id;
    }
    
    /**
     * Create test merchandise
     */
    public static function create_test_merchandise( $band_id, $args = array() ) {
        $defaults = array(
            'post_title'   => 'Test Merch ' . uniqid(),
            'post_content' => 'Test merchandise description',
            'post_status'  => 'publish',
            'post_type'    => 'msp_merchandise'
        );
        
        $merch_id = wp_insert_post( wp_parse_args( $args, $defaults ) );
        
        if ( $merch_id ) {
            update_post_meta( $merch_id, '_msp_merchandise_band_id', $band_id );
            update_post_meta( $merch_id, '_msp_merchandise_price', 25.00 );
            update_post_meta( $merch_id, '_msp_merchandise_stock', 100 );
            update_post_meta( $merch_id, '_msp_merchandise_size', 'M' );
        }
        
        return $merch_id;
    }
    
    /**
     * Create test tour
     */
    public static function create_test_tour( $band_id, $args = array() ) {
        $defaults = array(
            'post_title'   => 'Test Tour ' . uniqid(),
            'post_content' => 'Test tour description',
            'post_status'  => 'publish',
            'post_type'    => 'msp_tour'
        );
        
        $tour_id = wp_insert_post( wp_parse_args( $args, $defaults ) );
        
        if ( $tour_id ) {
            update_post_meta( $tour_id, '_msp_tour_band_id', $band_id );
            update_post_meta( $tour_id, '_msp_tour_start_date', date( 'Y-m-d' ) );
            update_post_meta( $tour_id, '_msp_tour_end_date', date( 'Y-m-d', strtotime( '+30 days' ) ) );
        }
        
        return $tour_id;
    }
    
    /**
     * Create test show
     */
    public static function create_test_show( $tour_id, $args = array() ) {
        $defaults = array(
            'post_title'   => 'Test Show ' . uniqid(),
            'post_content' => 'Test show description',
            'post_status'  => 'publish',
            'post_type'    => 'msp_show'
        );
        
        $show_id = wp_insert_post( wp_parse_args( $args, $defaults ) );
        
        if ( $show_id ) {
            update_post_meta( $show_id, '_msp_show_tour_id', $tour_id );
            update_post_meta( $show_id, '_msp_show_date', date( 'Y-m-d' ) );
            update_post_meta( $show_id, '_msp_show_venue', 'Test Venue' );
            update_post_meta( $show_id, '_msp_show_address', '123 Test Street' );
        }
        
        return $show_id;
    }
    
    /**
     * Clean up test data
     */
    public static function cleanup_test_data() {
        global $wpdb;
        
        // Clean posts
        $post_types = array( 'msp_band', 'msp_tour', 'msp_show', 'msp_merchandise', 'msp_sales_page' );
        foreach ( $post_types as $post_type ) {
            $posts = get_posts( array(
                'post_type'      => $post_type,
                'posts_per_page' => -1,
                'post_status'    => 'any',
                'fields'         => 'ids'
            ) );
            
            foreach ( $posts as $post_id ) {
                wp_delete_post( $post_id, true );
            }
        }
        
        // Clean custom tables
        $tables = array( 'msp_sales', 'msp_stock_log', 'msp_stock_alerts' );
        foreach ( $tables as $table ) {
            $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}{$table}" );
        }
    }
}
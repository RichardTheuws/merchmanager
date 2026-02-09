<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Check if we should remove all data (standalone option or msp_settings for backwards compatibility)
$remove_data = get_option( 'msp_remove_data_on_uninstall', false );
if ( ! $remove_data ) {
	$msp_settings = get_option( 'msp_settings', array() );
	$remove_data  = ! empty( $msp_settings['remove_data'] );
}

if ( $remove_data ) {
    // Load database class
    require_once plugin_dir_path( __FILE__ ) . 'includes/database/class-merchmanager-database.php';
    
    // Drop tables
    $database = new Merchmanager_Database();
    $database->drop_tables();
    
    // Remove custom post types and their data
    $post_types = array( 'msp_band', 'msp_tour', 'msp_show', 'msp_merchandise', 'msp_sales_page' );
    
    foreach ( $post_types as $post_type ) {
        $posts = get_posts( array(
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ) );
        
        foreach ( $posts as $post ) {
            wp_delete_post( $post->ID, true );
        }
    }
    
    // Remove user roles
    $roles = array( 'msp_management', 'msp_tour_management', 'msp_merch_sales' );
    
    foreach ( $roles as $role ) {
        remove_role( $role );
    }
    
    // Remove options
    $options = array(
        'msp_db_version',
        'msp_plugin_status',
        'msp_settings',
        'msp_remove_data_on_uninstall',
        'merchmanager_onboarding_complete',
        'merchmanager_version',
    );
    
    foreach ( $options as $option ) {
        delete_option( $option );
    }
}

// Flush rewrite rules
flush_rewrite_rules();
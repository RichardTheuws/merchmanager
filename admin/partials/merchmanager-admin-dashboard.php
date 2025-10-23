<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/partials
 */
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <div class="msp-dashboard">
        <div class="msp-dashboard-header">
            <div class="msp-dashboard-welcome">
                <h2><?php _e( 'Welcome to Merchandise Sales Plugin', 'merchmanager' ); ?></h2>
                <p><?php _e( 'Manage your band\'s merchandise sales during tours and events.', 'merchmanager' ); ?></p>
            </div>
            
            <div class="msp-dashboard-stats">
                <div class="msp-stat-box">
                    <h3><?php _e( 'Total Sales', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_total_sales() ); ?></p>
                </div>
                
                <div class="msp-stat-box">
                    <h3><?php _e( 'Total Revenue', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_total_revenue() ); ?></p>
                </div>
                
                <div class="msp-stat-box">
                    <h3><?php _e( 'Active Tours', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_active_tours_count() ); ?></p>
                </div>
                
                <div class="msp-stat-box">
                    <h3><?php _e( 'Low Stock Items', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_low_stock_count() ); ?></p>
                </div>
            </div>
        </div>
        
        <div class="msp-dashboard-content">
            <div class="msp-dashboard-column">
                <div class="msp-dashboard-card">
                    <h3><?php _e( 'Recent Sales', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_recent_sales(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-sales' ) ); ?>" class="button"><?php _e( 'View All Sales', 'merchmanager' ); ?></a>
                    </div>
                </div>
                
                <div class="msp-dashboard-card">
                    <h3><?php _e( 'Top Selling Items', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_top_selling_items(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports' ) ); ?>" class="button"><?php _e( 'View Reports', 'merchmanager' ); ?></a>
                    </div>
                </div>
            </div>
            
            <div class="msp-dashboard-column">
                <div class="msp-dashboard-card">
                    <h3><?php _e( 'Upcoming Shows', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_upcoming_shows(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=msp_show' ) ); ?>" class="button"><?php _e( 'View All Shows', 'merchmanager' ); ?></a>
                    </div>
                </div>
                
                <div class="msp-dashboard-card">
                    <h3><?php _e( 'Low Stock Alerts', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_low_stock_alerts(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=msp_merchandise' ) ); ?>" class="button"><?php _e( 'Manage Inventory', 'merchmanager' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="msp-dashboard-footer">
            <div class="msp-dashboard-card">
                <h3><?php _e( 'Quick Links', 'merchmanager' ); ?></h3>
                <div class="msp-dashboard-card-content msp-quick-links">
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_band' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-groups"></span>
                        <span><?php _e( 'Add Band', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_tour' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <span><?php _e( 'Add Tour', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_show' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-tickets-alt"></span>
                        <span><?php _e( 'Add Show', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_merchandise' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-cart"></span>
                        <span><?php _e( 'Add Merchandise', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-sales' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-money-alt"></span>
                        <span><?php _e( 'Record Sale', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_sales_page' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-store"></span>
                        <span><?php _e( 'Generate Sales Page', 'merchmanager' ); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
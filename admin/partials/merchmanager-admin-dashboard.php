<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/models/class-merchmanager-band.php';
$bands_for_empty = Merchmanager_Band::get_all();
$has_bands = ! empty( $bands_for_empty );
?>
<div class="wrap">
	<header class="msp-page-header">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	</header>
	<div class="msp-page-content">
	<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Redirect param after demo load. ?>
	<?php if ( isset( $_GET['demo_loaded'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['demo_loaded'] ) ) ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Demo data loaded. You can now explore MerchManager.', 'merchmanager' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! $has_bands ) : ?>
		<div class="msp-empty-state notice notice-info">
			<p><strong><?php esc_html_e( 'No bands yet – create your first band', 'merchmanager' ); ?></strong></p>
			<p><?php esc_html_e( 'Add a band to start managing merchandise, tours, and sales.', 'merchmanager' ); ?></p>
			<p class="msp-empty-state-actions">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_band' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add your first band', 'merchmanager' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding' ) ); ?>" class="button"><?php esc_html_e( 'Run setup wizard', 'merchmanager' ); ?></a>
			</p>
		</div>
	<?php endif; ?>

    <div class="msp-dashboard">
        <div class="msp-dashboard-header">
            <div class="msp-dashboard-welcome">
                <h2><?php esc_html_e( 'Welcome to MerchManager', 'merchmanager' ); ?></h2>
                <p><?php esc_html_e( 'Manage your band\'s merchandise sales during tours and events.', 'merchmanager' ); ?></p>
            </div>
            
            <div class="msp-dashboard-stats">
                <div class="msp-stat-box">
                    <h3><?php esc_html_e( 'Total Sales', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_total_sales() ); ?></p>
                </div>
                
                <div class="msp-stat-box">
                    <h3><?php esc_html_e( 'Total Revenue', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_total_revenue() ); ?></p>
                </div>
                
                <div class="msp-stat-box">
                    <h3><?php esc_html_e( 'Active Tours', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $this->get_active_tours_count() ); ?></p>
                </div>
                
                <?php $low_stock_count = $this->get_low_stock_count(); ?>
                <div class="msp-stat-box <?php echo $low_stock_count > 0 ? 'msp-stat-box-warning' : ''; ?>">
                    <h3><?php esc_html_e( 'Low Stock Items', 'merchmanager' ); ?></h3>
                    <p class="msp-stat-value"><?php echo esc_html( $low_stock_count ); ?></p>
                    <?php if ( $low_stock_count > 0 ) : ?>
                        <p class="msp-stat-cta"><a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=inventory' ) ); ?>" class="button button-small"><?php esc_html_e( 'View for reorder', 'merchmanager' ); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="msp-dashboard-content">
            <div class="msp-dashboard-column">
                <div class="msp-dashboard-card">
                    <h3><?php esc_html_e( 'Recent Sales', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_recent_sales(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-sales' ) ); ?>" class="button"><?php esc_html_e( 'View All Sales', 'merchmanager' ); ?></a>
                    </div>
                </div>
                
                <div class="msp-dashboard-card">
                    <h3><?php esc_html_e( 'Top Selling Items', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_top_selling_items(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports' ) ); ?>" class="button"><?php esc_html_e( 'View Reports', 'merchmanager' ); ?></a>
                    </div>
                </div>
            </div>
            
            <div class="msp-dashboard-column">
                <div class="msp-dashboard-card">
                    <h3><?php esc_html_e( 'Upcoming Shows', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php $this->display_upcoming_shows(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=msp_show' ) ); ?>" class="button"><?php esc_html_e( 'View All Shows', 'merchmanager' ); ?></a>
                    </div>
                </div>
                
                <?php
                $services = $this->get_dashboard_services();
                $low_stock_items = $services['stock']->get_low_stock_items( array() );
                $has_low_stock = ! empty( $low_stock_items );
                ?>
                <div class="msp-dashboard-card <?php echo $has_low_stock ? 'msp-card-low-stock' : ''; ?>">
                    <h3><?php esc_html_e( 'Low Stock Alerts', 'merchmanager' ); ?></h3>
                    <div class="msp-dashboard-card-content">
                        <?php if ( $has_low_stock ) : ?>
                            <p class="msp-low-stock-notice"><?php echo esc_html( sprintf( /* translators: %d: number of items */ _n( '%d item needs reordering.', '%d items need reordering.', count( $low_stock_items ), 'merchmanager' ), count( $low_stock_items ) ) ); ?></p>
                        <?php endif; ?>
                        <?php $this->display_low_stock_alerts(); ?>
                    </div>
                    <div class="msp-dashboard-card-footer">
                        <?php if ( $has_low_stock ) : ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=inventory' ) ); ?>" class="button"><?php esc_html_e( 'View low stock for reorder', 'merchmanager' ); ?></a>
                        <?php else : ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=inventory' ) ); ?>" class="button"><?php esc_html_e( 'View inventory', 'merchmanager' ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="msp-dashboard-footer">
            <div class="msp-dashboard-card">
                <h3><?php esc_html_e( 'Quick Links', 'merchmanager' ); ?></h3>
                <div class="msp-dashboard-card-content msp-quick-links">
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_band' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-groups"></span>
                        <span><?php esc_html_e( 'Add Band', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_tour' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <span><?php esc_html_e( 'Add Tour', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_show' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-tickets-alt"></span>
                        <span><?php esc_html_e( 'Add Show', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_merchandise' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-cart"></span>
                        <span><?php esc_html_e( 'Add Merchandise', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-sales' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-money-alt"></span>
                        <span><?php esc_html_e( 'Record Sale', 'merchmanager' ); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_sales_page' ) ); ?>" class="msp-quick-link">
                        <span class="dashicons dashicons-store"></span>
                        <span><?php esc_html_e( 'Generate Sales Page', 'merchmanager' ); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'merchmanager-admin-footer.php'; ?>
</div>
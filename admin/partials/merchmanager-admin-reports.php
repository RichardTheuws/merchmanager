<?php
/**
 * Provide a reports view for the plugin
 *
 * This file is used to markup the reports page.
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

// Get current tab
$current_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'sales';

// Define tabs
$tabs = array(
    'sales'      => __( 'Sales Reports', 'merchmanager' ),
    'inventory'  => __( 'Inventory Management', 'merchmanager' ),
    'stock'      => __( 'Stock History', 'merchmanager' ),
    'alerts'     => __( 'Stock Alerts', 'merchmanager' ),
);

// Load services and models
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-band.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-merchandise.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/services/class-merchmanager-stock-service.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/services/class-merchmanager-sales-service.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/services/class-merchmanager-report-service.php';
$stock_service = new Merchmanager_Stock_Service();
$sales_service = new Merchmanager_Sales_Service();
$report_service = new Merchmanager_Report_Service();
$reports_bands = Merchmanager_Band::get_all();
?>

<div class="wrap">
	<header class="msp-page-header">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	</header>
	<div class="msp-page-content">
	<?php if ( empty( $reports_bands ) ) : ?>
		<div class="msp-empty-state notice notice-info">
			<p><strong><?php esc_html_e( 'No data yet – create your first band to see reports', 'merchmanager' ); ?></strong></p>
			<p><?php esc_html_e( 'Reports show sales, inventory, and stock history once you have bands and data.', 'merchmanager' ); ?></p>
			<p class="msp-empty-state-actions">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_band' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add your first band', 'merchmanager' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding' ) ); ?>" class="button"><?php esc_html_e( 'Run setup wizard', 'merchmanager' ); ?></a>
			</p>
		</div>
	<?php endif; ?>

    <nav class="nav-tab-wrapper">
        <?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'tab', $tab_key ) ); ?>" class="nav-tab <?php echo esc_attr( $current_tab === $tab_key ? 'nav-tab-active' : '' ); ?>">
                <?php echo esc_html( $tab_label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="msp-reports-content">
        <?php switch ( $current_tab ) :
            case 'inventory':
                // Inventory Management Tab
                $band_id = isset( $_GET['band_id'] ) ? intval( $_GET['band_id'] ) : 0;
                $category = isset( $_GET['category'] ) ? sanitize_key( wp_unslash( $_GET['category'] ) ) : '';
                
                // Get inventory statistics
                $stats = $stock_service->get_stock_statistics( $band_id );
                
                // Get low stock items
                $low_stock_args = array(
                    'band_id' => $band_id,
                    'category' => $category,
                );
                $low_stock_items = $stock_service->get_low_stock_items( $low_stock_args );
                
                // Get out of stock items
                $out_of_stock_items = $stock_service->get_out_of_stock_items( $low_stock_args );
                
                // Get bands for filter
                $bands = Merchmanager_Band::get_all();
                
                // Define categories
                $categories = array(
                    'apparel'   => __( 'Apparel', 'merchmanager' ),
                    'music'     => __( 'Music', 'merchmanager' ),
                    'accessory' => __( 'Accessory', 'merchmanager' ),
                    'poster'    => __( 'Poster', 'merchmanager' ),
                    'other'     => __( 'Other', 'merchmanager' ),
                );
                ?>
                
                <div class="msp-report-filters">
                    <h3><?php esc_html_e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="inventory">
                        
                        <label for="band_id"><?php esc_html_e( 'Band:', 'merchmanager' ); ?></label>
                        <select id="band_id" name="band_id">
                            <option value="0"><?php esc_html_e( 'All Bands', 'merchmanager' ); ?></option>
                            <?php foreach ( $bands as $band ) : ?>
                                <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $band_id, $band->get_id() ); ?>>
                                    <?php echo esc_html( $band->get_name() ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="category"><?php esc_html_e( 'Category:', 'merchmanager' ); ?></label>
                        <select id="category" name="category">
                            <option value=""><?php esc_html_e( 'All Categories', 'merchmanager' ); ?></option>
                            <?php foreach ( $categories as $cat_key => $cat_label ) : ?>
                                <option value="<?php echo esc_attr( $cat_key ); ?>" <?php selected( $category, $cat_key ); ?>>
                                    <?php echo esc_html( $cat_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="submit" class="button" value="<?php esc_attr_e( 'Apply Filters', 'merchmanager' ); ?>">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=inventory' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'merchmanager' ); ?></a>
                    </form>
                </div>
                
                <div class="msp-report-content">
                    <h2><?php esc_html_e( 'Inventory Overview', 'merchmanager' ); ?></h2>
                    
                    <div class="msp-inventory-stats">
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Total Items', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['total_items'] ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Total Stock', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['total_stock'] ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Total Value', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( '€' . number_format( $stats['total_value'], 2 ) ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Low Stock', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['low_stock_count'] ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Out of Stock', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['out_of_stock_count'] ); ?></p>
                        </div>
                    </div>
                    
                    <h3><?php esc_html_e( 'Low Stock Items', 'merchmanager' ); ?></h3>
                    <?php if ( ! empty( $low_stock_items ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'SKU', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Current Stock', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Threshold', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Actions', 'merchmanager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $low_stock_items as $item ) : ?>
                                    <?php
                                    $threshold = $item->get_low_stock_threshold();
                                    if ( ! $threshold ) {
                                        $options = get_option( 'msp_settings', array() );
                                        $threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->get_id() ) ); ?>">
                                                <?php echo esc_html( $item->get_name() ); ?>
                                            </a>
                                        </td>
                                        <td><?php echo esc_html( $item->get_sku() ); ?></td>
                                        <td><?php echo esc_html( $item->get_stock() ); ?></td>
                                        <td><?php echo esc_html( $threshold ); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->get_id() ) ); ?>" class="button"><?php esc_html_e( 'Manage', 'merchmanager' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No low stock items found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                    
                    <h3><?php esc_html_e( 'Out of Stock Items', 'merchmanager' ); ?></h3>
                    <?php if ( ! empty( $out_of_stock_items ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'SKU', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Current Stock', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Actions', 'merchmanager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $out_of_stock_items as $item ) : ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->get_id() ) ); ?>">
                                                <?php echo esc_html( $item->get_name() ); ?>
                                            </a>
                                        </td>
                                        <td><?php echo esc_html( $item->get_sku() ); ?></td>
                                        <td><?php echo esc_html( $item->get_stock() ); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->get_id() ) ); ?>" class="button"><?php esc_html_e( 'Manage', 'merchmanager' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No out of stock items found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php break;
            
            case 'stock':
                // Stock History Tab
                $merchandise_id = isset( $_GET['merchandise_id'] ) ? intval( $_GET['merchandise_id'] ) : 0;
                $user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0;
                $change_reason = isset( $_GET['change_reason'] ) ? sanitize_key( wp_unslash( $_GET['change_reason'] ) ) : '';
                $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
                $end_date = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';
                
                // Get stock log
                $log_args = array(
                    'merchandise_id' => $merchandise_id,
                    'user_id'        => $user_id,
                    'change_reason'  => $change_reason,
                    'start_date'     => $start_date,
                    'end_date'       => $end_date,
                    'limit'          => 50,
                );
                $stock_log = $stock_service->get_stock_log( $log_args );
                
                // Get merchandise for filter
                $merchandise = Merchmanager_Merchandise::get_all();
                
                // Get users
                $users = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
                
                // Define change reasons
                $change_reasons = array(
                    'sale'      => __( 'Sale', 'merchmanager' ),
                    'manual'    => __( 'Manual Adjustment', 'merchmanager' ),
                    'receiving' => __( 'Receiving', 'merchmanager' ),
                    'damage'    => __( 'Damage/Loss', 'merchmanager' ),
                    'transfer'  => __( 'Transfer', 'merchmanager' ),
                );
                ?>
                
                <div class="msp-report-filters">
                    <h3><?php esc_html_e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="stock">
                        
                        <label for="merchandise_id"><?php esc_html_e( 'Merchandise:', 'merchmanager' ); ?></label>
                        <select id="merchandise_id" name="merchandise_id">
                            <option value="0"><?php esc_html_e( 'All Items', 'merchmanager' ); ?></option>
                            <?php foreach ( $merchandise as $item ) : ?>
                                <option value="<?php echo esc_attr( $item->get_id() ); ?>" <?php selected( $merchandise_id, $item->get_id() ); ?>>
                                    <?php echo esc_html( $item->get_name() ); ?> (<?php echo esc_html( $item->get_sku() ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="user_id"><?php esc_html_e( 'User:', 'merchmanager' ); ?></label>
                        <select id="user_id" name="user_id">
                            <option value="0"><?php esc_html_e( 'All Users', 'merchmanager' ); ?></option>
                            <?php foreach ( $users as $user ) : ?>
                                <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $user_id, $user->ID ); ?>>
                                    <?php echo esc_html( $user->display_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="change_reason"><?php esc_html_e( 'Reason:', 'merchmanager' ); ?></label>
                        <select id="change_reason" name="change_reason">
                            <option value=""><?php esc_html_e( 'All Reasons', 'merchmanager' ); ?></option>
                            <?php foreach ( $change_reasons as $reason_key => $reason_label ) : ?>
                                <option value="<?php echo esc_attr( $reason_key ); ?>" <?php selected( $change_reason, $reason_key ); ?>>
                                    <?php echo esc_html( $reason_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="start_date"><?php esc_html_e( 'Start Date:', 'merchmanager' ); ?></label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>">
                        
                        <label for="end_date"><?php esc_html_e( 'End Date:', 'merchmanager' ); ?></label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>">
                        
                        <input type="submit" class="button" value="<?php esc_attr_e( 'Apply Filters', 'merchmanager' ); ?>">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=stock' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'merchmanager' ); ?></a>
                    </form>
                </div>
                
                <div class="msp-report-content">
                    <h2><?php esc_html_e( 'Stock History', 'merchmanager' ); ?></h2>
                    
                    <?php if ( ! empty( $stock_log ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Date', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Previous Stock', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'New Stock', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Change', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Reason', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'User', 'merchmanager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $stock_log as $log ) : ?>
                                    <?php $change = $log->new_stock - $log->previous_stock; ?>
                                    <tr>
                                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $log->created_at ) ) ); ?></td>
                                        <td><?php echo esc_html( $log->merchandise_name ); ?> (<?php echo esc_html( $log->merchandise_sku ); ?>)</td>
                                        <td><?php echo esc_html( $log->previous_stock ); ?></td>
                                        <td><?php echo esc_html( $log->new_stock ); ?></td>
                                        <td><?php echo esc_html( $change > 0 ? '+' . $change : $change ); ?></td>
                                        <td><?php echo esc_html( ucfirst( $log->change_reason ) ); ?></td>
                                        <td><?php echo esc_html( $log->user_name ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No stock history found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php break;
            
            case 'alerts':
                // Stock Alerts Tab
                $status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : 'active';
                
                // Get stock alerts
                $alerts = $stock_service->get_stock_alerts( $status );
                ?>
                
                <div class="msp-report-filters">
                    <h3><?php esc_html_e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="alerts">
                        
                        <label for="status"><?php esc_html_e( 'Status:', 'merchmanager' ); ?></label>
                        <select id="status" name="status">
                            <option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'merchmanager' ); ?></option>
                            <option value="resolved" <?php selected( $status, 'resolved' ); ?>><?php esc_html_e( 'Resolved', 'merchmanager' ); ?></option>
                            <option value="all" <?php selected( $status, 'all' ); ?>><?php esc_html_e( 'All', 'merchmanager' ); ?></option>
                        </select>
                        
                        <input type="submit" class="button" value="<?php esc_attr_e( 'Apply Filters', 'merchmanager' ); ?>">
                    </form>
                </div>
                
                <div class="msp-report-content">
                    <h2><?php esc_html_e( 'Stock Alerts', 'merchmanager' ); ?></h2>
                    
                    <?php if ( ! empty( $alerts ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Created', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'SKU', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Current Stock', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Threshold', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Actions', 'merchmanager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $alerts as $alert ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $alert->created_at ) ) ); ?></td>
                                        <td><?php echo esc_html( $alert->merchandise_name ); ?></td>
                                        <td><?php echo esc_html( $alert->merchandise_sku ); ?></td>
                                        <td><?php echo esc_html( $alert->current_stock ); ?></td>
                                        <td><?php echo esc_html( $alert->threshold ); ?></td>
                                        <td><?php echo esc_html( ucfirst( $alert->status ) ); ?></td>
                                        <td>
                                            <?php if ( $alert->status === 'active' ) : ?>
                                                <form method="post" style="display: inline;">
                                                    <?php wp_nonce_field( 'msp_resolve_alert_' . $alert->id, 'msp_alert_nonce' ); ?>
                                                    <input type="hidden" name="alert_id" value="<?php echo esc_attr( $alert->id ); ?>">
                                                    <input type="hidden" name="action" value="resolve_alert">
                                                    <button type="submit" class="button"><?php esc_html_e( 'Resolve', 'merchmanager' ); ?></button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="<?php echo esc_url( get_edit_post_link( $alert->merchandise_id ) ); ?>" class="button"><?php esc_html_e( 'Manage', 'merchmanager' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php esc_html_e( 'No stock alerts found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php break;
            
            default:
                // Sales Reports Tab (default)
                $band_id = isset( $_GET['band_id'] ) ? intval( $_GET['band_id'] ) : 0;
                $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : gmdate( 'Y-m-01' );
                $end_date = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : gmdate( 'Y-m-d' );
                $report_args = array(
                    'band_id'    => $band_id,
                    'start_date' => $start_date,
                    'end_date'   => $end_date,
                );
                $sales_report = $report_service->generate_sales_report( $report_args );
                $options = get_option( 'msp_settings', array() );
                $currency = isset( $options['currency'] ) ? $options['currency'] : 'EUR';
                $symbols = array( 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'CAD' => '$', 'AUD' => '$' );
                $currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '€';
                $bands = Merchmanager_Band::get_all();
                ?>
                <div class="msp-report-filters">
                    <h3><?php esc_html_e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get" style="display:flex;flex-wrap:wrap;align-items:center;gap:10px;margin-bottom:15px;">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="sales">
                        <label for="band_id_sales"><?php esc_html_e( 'Band:', 'merchmanager' ); ?></label>
                        <select id="band_id_sales" name="band_id">
                            <option value="0"><?php esc_html_e( 'All Bands', 'merchmanager' ); ?></option>
                            <?php foreach ( $bands as $band ) : ?>
                                <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $band_id, $band->get_id() ); ?>><?php echo esc_html( $band->get_name() ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="start_date_sales"><?php esc_html_e( 'Start Date:', 'merchmanager' ); ?></label>
                        <input type="date" id="start_date_sales" name="start_date" value="<?php echo esc_attr( $start_date ); ?>">
                        <label for="end_date_sales"><?php esc_html_e( 'End Date:', 'merchmanager' ); ?></label>
                        <input type="date" id="end_date_sales" name="end_date" value="<?php echo esc_attr( $end_date ); ?>">
                        <input type="submit" class="button" value="<?php esc_attr_e( 'Apply Filters', 'merchmanager' ); ?>">
                    </form>
                    <?php
                    $export_url = add_query_arg(
                        array(
                            'page'            => 'msp-reports',
                            'tab'             => 'sales',
                            'msp_export_csv'  => '1',
                            'band_id'         => $band_id,
                            'start_date'      => $start_date,
                            'end_date'        => $end_date,
                            '_wpnonce'        => wp_create_nonce( 'msp_export_sales_report' ),
                        ),
                        admin_url( 'admin.php' )
                    );
                    ?>
                    <p><a href="<?php echo esc_url( $export_url ); ?>" class="button"><?php esc_html_e( 'Export to CSV', 'merchmanager' ); ?></a></p>
                </div>
                <div class="msp-report-content">
                    <h2><?php esc_html_e( 'Sales Summary', 'merchmanager' ); ?></h2>
                    <div class="msp-inventory-stats" style="display:flex;flex-wrap:wrap;gap:15px;margin-bottom:24px;">
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Total Sales', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $sales_report['summary']['total_sales'] ); ?></p>
                        </div>
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Total Quantity', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $sales_report['summary']['total_quantity'] ); ?></p>
                        </div>
                        <div class="msp-stat-box">
                            <h3><?php esc_html_e( 'Total Revenue', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $currency_symbol . number_format( (float) $sales_report['summary']['total_amount'], 2 ) ); ?></p>
                        </div>
                    </div>
                    <?php if ( ! empty( $sales_report['top_merchandise'] ) ) : ?>
                        <h3><?php esc_html_e( 'Top Selling Items', 'merchmanager' ); ?></h3>
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Quantity Sold', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Revenue', 'merchmanager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $sales_report['top_merchandise'] as $item ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( isset( $item->merchandise_name ) ? $item->merchandise_name : __( 'Unknown', 'merchmanager' ) ); ?></td>
                                        <td><?php echo esc_html( isset( $item->total_quantity ) ? $item->total_quantity : 0 ); ?></td>
                                        <td><?php echo esc_html( $currency_symbol . number_format( (float) ( isset( $item->total_amount ) ? $item->total_amount : 0 ), 2 ) ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ( ! empty( $sales_report['sales_by_payment'] ) ) : ?>
                        <h3 style="margin-top:24px;"><?php esc_html_e( 'Sales by Payment Type', 'merchmanager' ); ?></h3>
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Payment Type', 'merchmanager' ); ?></th>
                                    <th><?php esc_html_e( 'Revenue', 'merchmanager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $sales_report['sales_by_payment'] as $item ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( ucfirst( $item->payment_type ?? '' ) ); ?></td>
                                        <td><?php echo esc_html( $currency_symbol . number_format( (float) ( isset( $item->total_amount ) ? $item->total_amount : 0 ), 2 ) ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if ( empty( $sales_report['top_merchandise'] ) && empty( $sales_report['sales_by_payment'] ) && 0 === (int) $sales_report['summary']['total_sales'] ) : ?>
                        <p><?php esc_html_e( 'No sales data found for the selected period.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                <?php break;
        endswitch; ?>
    </div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'merchmanager-admin-footer.php'; ?>
</div>

<?php
// Handle form submissions
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['action'] ) ) {
    if ( $_POST['action'] === 'resolve_alert' && isset( $_POST['alert_id'] ) ) {
        $alert_id = intval( $_POST['alert_id'] );
        
        // Verify nonce
        if ( ! isset( $_POST['msp_alert_nonce'] ) || ! wp_verify_nonce( $_POST['msp_alert_nonce'], 'msp_resolve_alert_' . $alert_id ) ) {
            wp_die( esc_html( __( 'Security check failed.', 'merchmanager' ) ) );
        }
        
        // Resolve alert
        $result = $stock_service->resolve_stock_alert( $alert_id );
        
        if ( is_wp_error( $result ) ) {
            echo '<div class="error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
        } else {
            echo '<div class="updated"><p>' . esc_html( __( 'Alert resolved successfully.', 'merchmanager' ) ) . '</p></div>';
        }
    }
}
?>
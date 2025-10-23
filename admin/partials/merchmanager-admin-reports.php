<?php
/**
 * Provide a reports view for the plugin
 *
 * This file is used to markup the reports page.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/partials
 */

// Get current tab
$current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'sales';

// Define tabs
$tabs = array(
    'sales'      => __( 'Sales Reports', 'merchmanager' ),
    'inventory'  => __( 'Inventory Management', 'merchmanager' ),
    'stock'      => __( 'Stock History', 'merchmanager' ),
    'alerts'     => __( 'Stock Alerts', 'merchmanager' ),
);

// Load stock service
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/services/class-merchmanager-stock-service.php';
$stock_service = new Merchmanager_Stock_Service();

// Load sales service
require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/services/class-merchmanager-sales-service.php';
$sales_service = new Merchmanager_Sales_Service();
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <nav class="nav-tab-wrapper">
        <?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'tab', $tab_key ) ); ?>" class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html( $tab_label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>
    
    <div class="msp-reports-content">
        <?php switch ( $current_tab ) :
            case 'inventory':
                // Inventory Management Tab
                $band_id = isset( $_GET['band_id'] ) ? intval( $_GET['band_id'] ) : 0;
                $category = isset( $_GET['category'] ) ? sanitize_key( $_GET['category'] ) : '';
                
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
                require_once plugin_dir_path( dirname( __FILE__ ) ) . '../../includes/models/class-merchmanager-band.php';
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
                    <h3><?php _e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="inventory">
                        
                        <label for="band_id"><?php _e( 'Band:', 'merchmanager' ); ?></label>
                        <select id="band_id" name="band_id">
                            <option value="0"><?php _e( 'All Bands', 'merchmanager' ); ?></option>
                            <?php foreach ( $bands as $band ) : ?>
                                <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $band_id, $band->get_id() ); ?>>
                                    <?php echo esc_html( $band->get_name() ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="category"><?php _e( 'Category:', 'merchmanager' ); ?></label>
                        <select id="category" name="category">
                            <option value=""><?php _e( 'All Categories', 'merchmanager' ); ?></option>
                            <?php foreach ( $categories as $cat_key => $cat_label ) : ?>
                                <option value="<?php echo esc_attr( $cat_key ); ?>" <?php selected( $category, $cat_key ); ?>>
                                    <?php echo esc_html( $cat_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <input type="submit" class="button" value="<?php _e( 'Apply Filters', 'merchmanager' ); ?>">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=inventory' ) ); ?>" class="button"><?php _e( 'Reset', 'merchmanager' ); ?></a>
                    </form>
                </div>
                
                <div class="msp-report-content">
                    <h2><?php _e( 'Inventory Overview', 'merchmanager' ); ?></h2>
                    
                    <div class="msp-inventory-stats">
                        <div class="msp-stat-box">
                            <h3><?php _e( 'Total Items', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['total_items'] ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php _e( 'Total Stock', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['total_stock'] ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php _e( 'Total Value', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( '€' . number_format( $stats['total_value'], 2 ) ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php _e( 'Low Stock', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['low_stock_count'] ); ?></p>
                        </div>
                        
                        <div class="msp-stat-box">
                            <h3><?php _e( 'Out of Stock', 'merchmanager' ); ?></h3>
                            <p class="msp-stat-value"><?php echo esc_html( $stats['out_of_stock_count'] ); ?></p>
                        </div>
                    </div>
                    
                    <h3><?php _e( 'Low Stock Items', 'merchmanager' ); ?></h3>
                    <?php if ( ! empty( $low_stock_items ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'SKU', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Current Stock', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Threshold', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Actions', 'merchmanager' ); ?></th>
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
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->get_id() ) ); ?>" class="button"><?php _e( 'Manage', 'merchmanager' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php _e( 'No low stock items found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                    
                    <h3><?php _e( 'Out of Stock Items', 'merchmanager' ); ?></h3>
                    <?php if ( ! empty( $out_of_stock_items ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'SKU', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Current Stock', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Actions', 'merchmanager' ); ?></th>
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
                                            <a href="<?php echo esc_url( get_edit_post_link( $item->get_id() ) ); ?>" class="button"><?php _e( 'Manage', 'merchmanager' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php _e( 'No out of stock items found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php break;
            
            case 'stock':
                // Stock History Tab
                $merchandise_id = isset( $_GET['merchandise_id'] ) ? intval( $_GET['merchandise_id'] ) : 0;
                $user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0;
                $change_reason = isset( $_GET['change_reason'] ) ? sanitize_key( $_GET['change_reason'] ) : '';
                $start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
                $end_date = isset( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';
                
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
                    <h3><?php _e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="stock">
                        
                        <label for="merchandise_id"><?php _e( 'Merchandise:', 'merchmanager' ); ?></label>
                        <select id="merchandise_id" name="merchandise_id">
                            <option value="0"><?php _e( 'All Items', 'merchmanager' ); ?></option>
                            <?php foreach ( $merchandise as $item ) : ?>
                                <option value="<?php echo esc_attr( $item->get_id() ); ?>" <?php selected( $merchandise_id, $item->get_id() ); ?>>
                                    <?php echo esc_html( $item->get_name() ); ?> (<?php echo esc_html( $item->get_sku() ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="user_id"><?php _e( 'User:', 'merchmanager' ); ?></label>
                        <select id="user_id" name="user_id">
                            <option value="0"><?php _e( 'All Users', 'merchmanager' ); ?></option>
                            <?php foreach ( $users as $user ) : ?>
                                <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $user_id, $user->ID ); ?>>
                                    <?php echo esc_html( $user->display_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="change_reason"><?php _e( 'Reason:', 'merchmanager' ); ?></label>
                        <select id="change_reason" name="change_reason">
                            <option value=""><?php _e( 'All Reasons', 'merchmanager' ); ?></option>
                            <?php foreach ( $change_reasons as $reason_key => $reason_label ) : ?>
                                <option value="<?php echo esc_attr( $reason_key ); ?>" <?php selected( $change_reason, $reason_key ); ?>>
                                    <?php echo esc_html( $reason_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="start_date"><?php _e( 'Start Date:', 'merchmanager' ); ?></label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>">
                        
                        <label for="end_date"><?php _e( 'End Date:', 'merchmanager' ); ?></label>
                        <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>">
                        
                        <input type="submit" class="button" value="<?php _e( 'Apply Filters', 'merchmanager' ); ?>">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=stock' ) ); ?>" class="button"><?php _e( 'Reset', 'merchmanager' ); ?></a>
                    </form>
                </div>
                
                <div class="msp-report-content">
                    <h2><?php _e( 'Stock History', 'merchmanager' ); ?></h2>
                    
                    <?php if ( ! empty( $stock_log ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Date', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Previous Stock', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'New Stock', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Change', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Reason', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'User', 'merchmanager' ); ?></th>
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
                        <p><?php _e( 'No stock history found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php break;
            
            case 'alerts':
                // Stock Alerts Tab
                $status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : 'active';
                
                // Get stock alerts
                $alerts = $stock_service->get_stock_alerts( $status );
                ?>
                
                <div class="msp-report-filters">
                    <h3><?php _e( 'Filters', 'merchmanager' ); ?></h3>
                    <form method="get">
                        <input type="hidden" name="page" value="msp-reports">
                        <input type="hidden" name="tab" value="alerts">
                        
                        <label for="status"><?php _e( 'Status:', 'merchmanager' ); ?></label>
                        <select id="status" name="status">
                            <option value="active" <?php selected( $status, 'active' ); ?>><?php _e( 'Active', 'merchmanager' ); ?></option>
                            <option value="resolved" <?php selected( $status, 'resolved' ); ?>><?php _e( 'Resolved', 'merchmanager' ); ?></option>
                            <option value="all" <?php selected( $status, 'all' ); ?>><?php _e( 'All', 'merchmanager' ); ?></option>
                        </select>
                        
                        <input type="submit" class="button" value="<?php _e( 'Apply Filters', 'merchmanager' ); ?>">
                    </form>
                </div>
                
                <div class="msp-report-content">
                    <h2><?php _e( 'Stock Alerts', 'merchmanager' ); ?></h2>
                    
                    <?php if ( ! empty( $alerts ) ) : ?>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Created', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Item', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'SKU', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Current Stock', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Threshold', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Status', 'merchmanager' ); ?></th>
                                    <th><?php _e( 'Actions', 'merchmanager' ); ?></th>
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
                                                    <button type="submit" class="button"><?php _e( 'Resolve', 'merchmanager' ); ?></button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="<?php echo esc_url( get_edit_post_link( $alert->merchandise_id ) ); ?>" class="button"><?php _e( 'Manage', 'merchmanager' ); ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php _e( 'No stock alerts found.', 'merchmanager' ); ?></p>
                    <?php endif; ?>
                </div>
                
                <?php break;
            
            default:
                // Sales Reports Tab (default)
                // This would contain sales reporting functionality
                ?>
                <div class="msp-report-content">
                    <h2><?php _e( 'Sales Reports', 'merchmanager' ); ?></h2>
                    <p><?php _e( 'Sales reporting functionality will be implemented here.', 'merchmanager' ); ?></p>
                </div>
                <?php break;
        endswitch; ?>
    </div>
</div>

<?php
// Handle form submissions
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['action'] ) ) {
    if ( $_POST['action'] === 'resolve_alert' && isset( $_POST['alert_id'] ) ) {
        $alert_id = intval( $_POST['alert_id'] );
        
        // Verify nonce
        if ( ! isset( $_POST['msp_alert_nonce'] ) || ! wp_verify_nonce( $_POST['msp_alert_nonce'], 'msp_resolve_alert_' . $alert_id ) ) {
            wp_die( __( 'Security check failed.', 'merchmanager' ) );
        }
        
        // Resolve alert
        $result = $stock_service->resolve_stock_alert( $alert_id );
        
        if ( is_wp_error( $result ) ) {
            echo '<div class="error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
        } else {
            echo '<div class="updated"><p>' . __( 'Alert resolved successfully.', 'merchmanager' ) . '</p></div>';
        }
    }
}
?>
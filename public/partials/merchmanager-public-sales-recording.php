<?php
/**
 * Provide a public-facing view for manual sales recording
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load sales recording service
require_once MERCHMANAGER_PLUGIN_DIR . 'includes/services/class-merchmanager-sales-recording-service.php';
$sales_recording_service = new Merchmanager_Sales_Recording_Service();

// Get sales session
$sales_session = $sales_recording_service->get_sales_session();
$sales_items = $sales_recording_service->get_sales_items();
$sales_total = $sales_recording_service->get_sales_total();

// Get bands for filter
require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-band.php';
$bands = Merchmanager_Band::get_all();

// Get shows for filter (optional)
$shows = array();
if ( isset( $_GET['band_id'] ) && absint( $_GET['band_id'] ) ) {
    $shows = get_posts( array(
        'post_type'      => 'msp_show',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'   => '_msp_show_band_id',
                'value' => absint( $_GET['band_id'] ),
            ),
        ),
    ) );
}

// Handle form submissions (with nonce verification)
if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
    if ( isset( $_POST['msp_add_to_sale'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_nonce'] ?? '' ) ), 'msp_add_to_sale' ) ) {
        $merchandise_id = isset( $_POST['merchandise_id'] ) ? absint( $_POST['merchandise_id'] ) : 0;
        $quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 1;

        $result = $sales_recording_service->add_to_sales_session( $merchandise_id, $quantity );

        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
        } else {
            wp_safe_redirect( add_query_arg( array( 'added' => $merchandise_id ), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ) ) );
            exit;
        }
    }

    if ( isset( $_POST['msp_record_sale'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_nonce'] ?? '' ) ), 'msp_record_sale' ) ) {
        $sale_data = array(
            'payment_type' => isset( $_POST['payment_type'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_type'] ) ) : 'cash',
            'show_id'      => isset( $_POST['show_id'] ) ? absint( $_POST['show_id'] ) : 0,
            'notes'        => isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '',
        );

        $result = $sales_recording_service->process_sale_recording( $sale_data );

        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
        } else {
            $success_message = $result['message'];
        }
    }

    if ( isset( $_POST['msp_update_quantity'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_nonce'] ?? '' ) ), 'msp_update_quantity' ) ) {
        $merchandise_id = isset( $_POST['merchandise_id'] ) ? absint( $_POST['merchandise_id'] ) : 0;
        $quantity = isset( $_POST['quantity'] ) ? absint( $_POST['quantity'] ) : 0;

        $result = $sales_recording_service->update_sales_item( $merchandise_id, $quantity );

        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
        }
    }

    if ( isset( $_POST['msp_remove_item'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_nonce'] ?? '' ) ), 'msp_remove_item' ) ) {
        $merchandise_id = isset( $_POST['merchandise_id'] ) ? absint( $_POST['merchandise_id'] ) : 0;

        $result = $sales_recording_service->remove_from_sales_session( $merchandise_id );

        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
        }
    }

    if ( isset( $_POST['msp_clear_sale'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['msp_nonce'] ?? '' ) ), 'msp_record_sale' ) ) {
        $sales_recording_service->clear_sales_session();
    }
}

// Get current band filter
$current_band_id = isset( $_GET['band_id'] ) ? intval( $_GET['band_id'] ) : 0;
$current_show_id = isset( $_GET['show_id'] ) ? intval( $_GET['show_id'] ) : 0;

// Get merchandise based on band filter
$merchandise_args = array(
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

if ( $current_band_id ) {
    $merchandise_args['meta_query'][] = array(
        'key'   => '_msp_merchandise_band_id',
        'value' => $current_band_id,
    );
}

$merchandise = get_posts( $merchandise_args );

// Get currency settings
$options = get_option( 'msp_settings', array() );
$currency = $options['currency'] ?? 'EUR';
$currency_symbols = array(
    'USD' => '$',
    'EUR' => '€',
    'GBP' => '£',
    'CAD' => '$',
    'AUD' => '$',
);
$currency_symbol = $currency_symbols[ $currency ] ?? '€';
?>

<div class="msp-sales-recording">
    <h1><?php esc_html_e( 'Manual Sales Recording', 'merchmanager' ); ?></h1>
    
    <?php if ( isset( $error_message ) ) : ?>
        <div class="msp-error">
            <p><?php echo esc_html( $error_message ); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ( isset( $success_message ) ) : ?>
        <div class="msp-success">
            <p><?php echo esc_html( $success_message ); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="msp-sales-filters">
        <form method="get" action="">
            <label for="band_id"><?php esc_html_e( 'Filter by Band:', 'merchmanager' ); ?></label>
            <select name="band_id" id="band_id" onchange="this.form.submit()">
                <option value="0"><?php esc_html_e( 'All Bands', 'merchmanager' ); ?></option>
                <?php foreach ( $bands as $band ) : ?>
                    <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $current_band_id, $band->get_id() ); ?>>
                        <?php echo esc_html( $band->get_name() ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <?php if ( ! empty( $shows ) ) : ?>
                <label for="show_id"><?php esc_html_e( 'Filter by Show:', 'merchmanager' ); ?></label>
                <select name="show_id" id="show_id" onchange="this.form.submit()">
                    <option value="0"><?php esc_html_e( 'All Shows', 'merchmanager' ); ?></option>
                    <?php foreach ( $shows as $show ) : ?>
                        <option value="<?php echo esc_attr( $show->ID ); ?>" <?php selected( $current_show_id, $show->ID ); ?>>
                            <?php echo esc_html( $show->post_title ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="msp-sales-content">
        <div class="msp-merchandise-list">
            <h2><?php esc_html_e( 'Available Merchandise', 'merchmanager' ); ?></h2>
            
            <?php if ( ! empty( $merchandise ) ) : ?>
                <?php foreach ( $merchandise as $item ) : ?>
                    <?php
                    $merch = new Merchmanager_Merchandise( $item->ID );
                    $stock = $merch->get_stock();
                    $price = $merch->get_price();
                    $size = $merch->get_size();
                    $color = $merch->get_color();
                    
                    // Skip out of stock items
                    if ( $stock <= 0 ) {
                        continue;
                    }
                    
                    $formatted_price = $currency_symbol . number_format( $price, 2 );
                    ?>
                    <div class="msp-merchandise-item">
                        <div class="msp-merchandise-info">
                            <?php if ( has_post_thumbnail( $item->ID ) ) : ?>
                                <div class="msp-merchandise-image">
                                    <?php echo get_the_post_thumbnail( $item->ID, 'thumbnail' ); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="msp-merchandise-details">
                                <h3><?php echo esc_html( $item->post_title ); ?></h3>
                                <p class="msp-merchandise-sku"><?php echo esc_html( $merch->get_sku() ); ?></p>
                                
                                <div class="msp-merchandise-meta">
                                    <?php if ( $size ) : ?>
                                        <span class="msp-merchandise-size"><?php echo esc_html( $size ); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if ( $color ) : ?>
                                        <span class="msp-merchandise-color"><?php echo esc_html( $color ); ?></span>
                                    <?php endif; ?>
                                    
                                    <span class="msp-merchandise-price"><?php echo esc_html( $formatted_price ); ?></span>
                                    <span class="msp-merchandise-stock"><?php echo esc_html( sprintf( /* translators: %1$d: stock quantity */ __( '%1$d in stock', 'merchmanager' ), $stock ) ); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <form method="post" action="" class="msp-add-to-sale-form">
                            <?php wp_nonce_field( 'msp_add_to_sale', 'msp_nonce' ); ?>
                            <input type="hidden" name="merchandise_id" value="<?php echo esc_attr( $item->ID ); ?>">
                            
                            <div class="msp-quantity-controls">
                                <label for="quantity_<?php echo esc_attr( $item->ID ); ?>"><?php esc_html_e( 'Quantity:', 'merchmanager' ); ?></label>
                                <input type="number" name="quantity" id="quantity_<?php echo esc_attr( $item->ID ); ?>" min="1" max="<?php echo esc_attr( $stock ); ?>" value="1">
                                <button type="submit" name="msp_add_to_sale" class="msp-button"><?php esc_html_e( 'Add to Sale', 'merchmanager' ); ?></button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p><?php esc_html_e( 'No merchandise available.', 'merchmanager' ); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="msp-sales-session">
            <h2><?php esc_html_e( 'Current Sale', 'merchmanager' ); ?></h2>
            
            <?php if ( ! empty( $sales_items ) ) : ?>
                <div class="msp-sale-items">
                    <table class="msp-sale-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
                                <th><?php esc_html_e( 'Price', 'merchmanager' ); ?></th>
                                <th><?php esc_html_e( 'Quantity', 'merchmanager' ); ?></th>
                                <th><?php esc_html_e( 'Subtotal', 'merchmanager' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'merchmanager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $sales_items as $item ) : ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html( $item['name'] ); ?></strong>
                                        <br><small><?php echo esc_html( $item['sku'] ); ?></small>
                                    </td>
                                    <td><?php echo esc_html( $currency_symbol . number_format( $item['price'], 2 ) ); ?></td>
                                    <td>
                                        <form method="post" action="" style="display: inline;">
                                            <?php wp_nonce_field( 'msp_update_quantity', 'msp_nonce' ); ?>
                                            <input type="hidden" name="merchandise_id" value="<?php echo esc_attr( $item['merchandise_id'] ); ?>">
                                            <input type="number" name="quantity" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="1" max="<?php echo esc_attr( $item['stock'] ); ?>" style="width: 60px;">
                                            <button type="submit" name="msp_update_quantity" class="msp-button-small"><?php esc_html_e( 'Update', 'merchmanager' ); ?></button>
                                        </form>
                                    </td>
                                    <td><?php echo esc_html( $currency_symbol . number_format( $item['subtotal'], 2 ) ); ?></td>
                                    <td>
                                        <form method="post" action="" style="display: inline;">
                                            <?php wp_nonce_field( 'msp_remove_item', 'msp_nonce' ); ?>
                                            <input type="hidden" name="merchandise_id" value="<?php echo esc_attr( $item['merchandise_id'] ); ?>">
                                            <button type="submit" name="msp_remove_item" class="msp-button-small msp-button-remove"><?php esc_html_e( 'Remove', 'merchmanager' ); ?></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right"><strong><?php esc_html_e( 'Total:', 'merchmanager' ); ?></strong></td>
                                <td><strong><?php echo esc_html( $currency_symbol . number_format( $sales_total, 2 ) ); ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="msp-sale-actions">
                    <form method="post" action="">
                        <?php wp_nonce_field( 'msp_record_sale', 'msp_nonce' ); ?>
                        
                        <div class="msp-sale-details">
                            <h3><?php esc_html_e( 'Sale Details', 'merchmanager' ); ?></h3>
                            
                            <div class="msp-form-group">
                                <label for="payment_type"><?php esc_html_e( 'Payment Type:', 'merchmanager' ); ?></label>
                                <select name="payment_type" id="payment_type" required>
                                    <option value="cash"><?php esc_html_e( 'Cash', 'merchmanager' ); ?></option>
                                    <option value="card"><?php esc_html_e( 'Card', 'merchmanager' ); ?></option>
                                    <option value="other"><?php esc_html_e( 'Other', 'merchmanager' ); ?></option>
                                </select>
                            </div>
                            
                            <?php if ( ! empty( $shows ) ) : ?>
                                <div class="msp-form-group">
                                    <label for="show_id"><?php esc_html_e( 'Show:', 'merchmanager' ); ?></label>
                                    <select name="show_id" id="show_id">
                                        <option value="0"><?php esc_html_e( 'Not associated with a show', 'merchmanager' ); ?></option>
                                        <?php foreach ( $shows as $show ) : ?>
                                            <option value="<?php echo esc_attr( $show->ID ); ?>" <?php selected( $current_show_id, $show->ID ); ?>>
                                                <?php echo esc_html( $show->post_title ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            
                            <div class="msp-form-group">
                                <label for="notes"><?php esc_html_e( 'Notes:', 'merchmanager' ); ?></label>
                                <textarea name="notes" id="notes" rows="3" placeholder="<?php echo esc_attr__( 'Optional notes about this sale...', 'merchmanager' ); ?>"></textarea>
                            </div>
                        </div>
                        
                        <div class="msp-action-buttons">
                            <button type="submit" name="msp_record_sale" class="msp-button msp-button-primary">
                                <?php esc_html_e( 'Record Sale', 'merchmanager' ); ?>
                            </button>
                            
                            <button type="submit" name="msp_clear_sale" class="msp-button msp-button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to clear the current sale?', 'merchmanager' ) ); ?>')">
                                <?php esc_html_e( 'Clear Sale', 'merchmanager' ); ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php else : ?>
                <p class="msp-no-items"><?php esc_html_e( 'No items in current sale. Add items from the merchandise list.', 'merchmanager' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
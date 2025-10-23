<?php
/**
 * Provide a public-facing view for the sales page
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/public/partials
 */

// Get sales page data
$sales_page_id = absint( $atts['id'] );
$sales_page = get_post( $sales_page_id );
$band_id = get_post_meta( $sales_page_id, '_msp_sales_page_band_id', true );
$show_id = get_post_meta( $sales_page_id, '_msp_sales_page_show_id', true );
$access_code = get_post_meta( $sales_page_id, '_msp_sales_page_access_code', true );
$merchandise_ids = get_post_meta( $sales_page_id, '_msp_sales_page_merchandise', true );

// Get band and show data
$band = get_post( $band_id );
$show = get_post( $show_id );
$show_date = get_post_meta( $show_id, '_msp_show_date', true );
$venue_name = get_post_meta( $show_id, '_msp_show_venue_name', true );
$venue_city = get_post_meta( $show_id, '_msp_show_venue_city', true );
$venue_state = get_post_meta( $show_id, '_msp_show_venue_state', true );

// Check if access code is required
$require_access_code = ! empty( $access_code );
$access_granted = false;

if ( $require_access_code ) {
    // Check if access code is in session
    if ( isset( $_SESSION['msp_access_code'][ $sales_page_id ] ) && $_SESSION['msp_access_code'][ $sales_page_id ] === $access_code ) {
        $access_granted = true;
    }
    
    // Check if access code is submitted
    if ( isset( $_POST['msp_access_code'] ) && $_POST['msp_access_code'] === $access_code ) {
        // Store access code in session
        if ( ! isset( $_SESSION['msp_access_code'] ) ) {
            $_SESSION['msp_access_code'] = array();
        }
        $_SESSION['msp_access_code'][ $sales_page_id ] = $access_code;
        $access_granted = true;
    }
}
?>

<div class="msp-sales-page">
    <div class="msp-sales-page-header">
        <?php if ( has_post_thumbnail( $band_id ) ) : ?>
            <div class="msp-band-logo">
                <?php echo get_the_post_thumbnail( $band_id, 'medium' ); ?>
            </div>
        <?php endif; ?>
        
        <h1 class="msp-sales-page-title"><?php echo esc_html( $sales_page->post_title ); ?></h1>
        
        <div class="msp-sales-page-info">
            <p class="msp-band-name"><?php echo esc_html( $band->post_title ); ?></p>
            
            <?php if ( $show_id ) : ?>
                <p class="msp-show-info">
                    <?php
                    // Format show date
                    $date_format = get_option( 'msp_settings' )['date_format'] ?? 'm/d/Y';
                    $time_format = get_option( 'msp_settings' )['time_format'] ?? 'g:i a';
                    $formatted_date = date_i18n( $date_format . ' ' . $time_format, strtotime( $show_date ) );
                    
                    echo esc_html( $show->post_title );
                    echo ' - ';
                    echo esc_html( $formatted_date );
                    echo ' - ';
                    echo esc_html( $venue_name );
                    if ( $venue_city ) {
                        echo ', ' . esc_html( $venue_city );
                    }
                    if ( $venue_state ) {
                        echo ', ' . esc_html( $venue_state );
                    }
                    ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ( $require_access_code && ! $access_granted ) : ?>
        <div class="msp-access-code-form">
            <h2><?php _e( 'Access Code Required', 'merchmanager' ); ?></h2>
            <p><?php _e( 'Please enter the access code to view this sales page.', 'merchmanager' ); ?></p>
            
            <form method="post" action="">
                <div class="msp-form-group">
                    <label for="msp_access_code"><?php _e( 'Access Code', 'merchmanager' ); ?></label>
                    <input type="text" name="msp_access_code" id="msp_access_code" required />
                </div>
                
                <div class="msp-form-group">
                    <button type="submit" class="msp-button"><?php _e( 'Submit', 'merchmanager' ); ?></button>
                </div>
            </form>
        </div>
    <?php else : ?>
        <div class="msp-sales-form">
            <h2><?php _e( 'Record Sale', 'merchmanager' ); ?></h2>
            
            <form method="post" action="" id="msp-sales-form">
                <input type="hidden" name="msp_sales_page_id" value="<?php echo esc_attr( $sales_page_id ); ?>" />
                <input type="hidden" name="msp_band_id" value="<?php echo esc_attr( $band_id ); ?>" />
                <input type="hidden" name="msp_show_id" value="<?php echo esc_attr( $show_id ); ?>" />
                
                <div class="msp-merchandise-list">
                    <?php
                    // Get merchandise items
                    if ( ! empty( $merchandise_ids ) && is_array( $merchandise_ids ) ) {
                        foreach ( $merchandise_ids as $merchandise_id ) {
                            $merchandise = get_post( $merchandise_id );
                            $price = get_post_meta( $merchandise_id, '_msp_merchandise_price', true );
                            $stock = get_post_meta( $merchandise_id, '_msp_merchandise_stock', true );
                            $size = get_post_meta( $merchandise_id, '_msp_merchandise_size', true );
                            $color = get_post_meta( $merchandise_id, '_msp_merchandise_color', true );
                            
                            // Skip if merchandise doesn't exist or is out of stock
                            if ( ! $merchandise || $stock <= 0 ) {
                                continue;
                            }
                            
                            // Format price
                            $currency = get_option( 'msp_settings' )['currency'] ?? 'USD';
                            $currency_symbols = array(
                                'USD' => '$',
                                'EUR' => '€',
                                'GBP' => '£',
                                'CAD' => '$',
                                'AUD' => '$',
                            );
                            $currency_symbol = $currency_symbols[ $currency ] ?? '$';
                            $formatted_price = $currency_symbol . number_format( $price, 2 );
                            ?>
                            <div class="msp-merchandise-item" data-id="<?php echo esc_attr( $merchandise_id ); ?>" data-price="<?php echo esc_attr( $price ); ?>">
                                <div class="msp-merchandise-info">
                                    <?php if ( has_post_thumbnail( $merchandise_id ) ) : ?>
                                        <div class="msp-merchandise-image">
                                            <?php echo get_the_post_thumbnail( $merchandise_id, 'thumbnail' ); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="msp-merchandise-details">
                                        <h3 class="msp-merchandise-title"><?php echo esc_html( $merchandise->post_title ); ?></h3>
                                        
                                        <div class="msp-merchandise-meta">
                                            <?php if ( $size ) : ?>
                                                <span class="msp-merchandise-size"><?php echo esc_html( $size ); ?></span>
                                            <?php endif; ?>
                                            
                                            <?php if ( $color ) : ?>
                                                <span class="msp-merchandise-color"><?php echo esc_html( $color ); ?></span>
                                            <?php endif; ?>
                                            
                                            <span class="msp-merchandise-price"><?php echo esc_html( $formatted_price ); ?></span>
                                            
                                            <span class="msp-merchandise-stock"><?php echo esc_html( sprintf( __( '%d in stock', 'merchmanager' ), $stock ) ); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="msp-merchandise-quantity">
                                    <label for="msp_quantity_<?php echo esc_attr( $merchandise_id ); ?>"><?php _e( 'Quantity', 'merchmanager' ); ?></label>
                                    <input type="number" name="msp_quantity[<?php echo esc_attr( $merchandise_id ); ?>]" id="msp_quantity_<?php echo esc_attr( $merchandise_id ); ?>" min="0" max="<?php echo esc_attr( $stock ); ?>" value="0" class="msp-quantity-input" />
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>' . __( 'No merchandise available.', 'merchmanager' ) . '</p>';
                    }
                    ?>
                </div>
                
                <div class="msp-sale-summary">
                    <h3><?php _e( 'Sale Summary', 'merchmanager' ); ?></h3>
                    
                    <div class="msp-sale-items">
                        <p class="msp-no-items"><?php _e( 'No items selected.', 'merchmanager' ); ?></p>
                        <ul class="msp-selected-items"></ul>
                    </div>
                    
                    <div class="msp-sale-total">
                        <p><?php _e( 'Total:', 'merchmanager' ); ?> <span class="msp-total-amount"><?php echo esc_html( $currency_symbol ); ?>0.00</span></p>
                    </div>
                    
                    <div class="msp-payment-type">
                        <label for="msp_payment_type"><?php _e( 'Payment Type', 'merchmanager' ); ?></label>
                        <select name="msp_payment_type" id="msp_payment_type">
                            <option value="cash"><?php _e( 'Cash', 'merchmanager' ); ?></option>
                            <option value="card"><?php _e( 'Card', 'merchmanager' ); ?></option>
                            <option value="other"><?php _e( 'Other', 'merchmanager' ); ?></option>
                        </select>
                    </div>
                    
                    <div class="msp-notes">
                        <label for="msp_notes"><?php _e( 'Notes', 'merchmanager' ); ?></label>
                        <textarea name="msp_notes" id="msp_notes" rows="3"></textarea>
                    </div>
                    
                    <div class="msp-submit">
                        <button type="submit" class="msp-button msp-record-sale" disabled><?php _e( 'Record Sale', 'merchmanager' ); ?></button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="msp-sale-confirmation" style="display: none;">
            <h2><?php _e( 'Sale Recorded', 'merchmanager' ); ?></h2>
            <p><?php _e( 'The sale has been recorded successfully.', 'merchmanager' ); ?></p>
            <p><a href="" class="msp-button msp-new-sale"><?php _e( 'Record Another Sale', 'merchmanager' ); ?></a></p>
        </div>
    <?php endif; ?>
</div>
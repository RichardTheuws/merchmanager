<?php
/**
 * The sales recording service class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 */

/**
 * The sales recording service class.
 *
 * This class handles manual sales recording operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Sales_Recording_Service {

    /**
     * Sales session key.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $sales_key    The sales session key.
     */
    private $sales_key = 'msp_sales';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( ! session_id() ) {
            session_start();
        }
    }

    /**
     * Get the sales session contents.
     *
     * @since    1.0.0
     * @return   array    The sales session contents.
     */
    public function get_sales_session() {
        return isset( $_SESSION[ $this->sales_key ] ) ? $_SESSION[ $this->sales_key ] : array();
    }

    /**
     * Add item to sales session.
     *
     * @since    1.0.0
     * @param    int       $merchandise_id    The merchandise ID.
     * @param    int       $quantity          The quantity to add.
     * @param    array     $variation         Optional variation data.
     * @return   bool|WP_Error                True on success, WP_Error on failure.
     */
    public function add_to_sales_session( $merchandise_id, $quantity = 1, $variation = array() ) {
        // Validate merchandise
        $merchandise = new Merchmanager_Merchandise( $merchandise_id );
        if ( ! $merchandise->get_id() ) {
            return new WP_Error( 'invalid_merchandise', __( 'Invalid merchandise ID.', 'merchmanager' ) );
        }

        // Check if merchandise is active
        if ( ! $merchandise->is_active() ) {
            return new WP_Error( 'inactive_merchandise', __( 'This merchandise is not available.', 'merchmanager' ) );
        }

        // Check stock
        $stock = $merchandise->get_stock();
        if ( $stock <= 0 ) {
            return new WP_Error( 'out_of_stock', __( 'This item is out of stock.', 'merchmanager' ) );
        }

        // Check if quantity exceeds stock
        $sales_session = $this->get_sales_session();
        $current_quantity = isset( $sales_session[ $merchandise_id ]['quantity'] ) ? $sales_session[ $merchandise_id ]['quantity'] : 0;
        
        if ( $current_quantity + $quantity > $stock ) {
            return new WP_Error( 'insufficient_stock', 
                sprintf( __( 'Only %d items available in stock.', 'merchmanager' ), $stock ) 
            );
        }

        // Initialize sales session if not exists
        if ( ! isset( $_SESSION[ $this->sales_key ] ) ) {
            $_SESSION[ $this->sales_key ] = array();
        }

        // Add item to sales session
        if ( isset( $_SESSION[ $this->sales_key ][ $merchandise_id ] ) ) {
            // Update existing item
            $_SESSION[ $this->sales_key ][ $merchandise_id ]['quantity'] += $quantity;
            if ( ! empty( $variation ) ) {
                $_SESSION[ $this->sales_key ][ $merchandise_id ]['variation'] = $variation;
            }
        } else {
            // Add new item
            $_SESSION[ $this->sales_key ][ $merchandise_id ] = array(
                'quantity'  => $quantity,
                'variation' => $variation,
                'added_at'  => current_time( 'mysql' ),
            );
        }

        return true;
    }

    /**
     * Update sales session item quantity.
     *
     * @since    1.0.0
     * @param    int       $merchandise_id    The merchandise ID.
     * @param    int       $quantity          The new quantity.
     * @return   bool|WP_Error                True on success, WP_Error on failure.
     */
    public function update_sales_item( $merchandise_id, $quantity ) {
        $sales_session = $this->get_sales_session();
        
        if ( ! isset( $sales_session[ $merchandise_id ] ) ) {
            return new WP_Error( 'item_not_found', __( 'Item not found in sales session.', 'merchmanager' ) );
        }

        // Validate merchandise
        $merchandise = new Merchmanager_Merchandise( $merchandise_id );
        if ( ! $merchandise->get_id() ) {
            return new WP_Error( 'invalid_merchandise', __( 'Invalid merchandise ID.', 'merchmanager' ) );
        }

        // Check stock
        $stock = $merchandise->get_stock();
        if ( $quantity > $stock ) {
            return new WP_Error( 'insufficient_stock', 
                sprintf( __( 'Only %d items available in stock.', 'merchmanager' ), $stock ) 
            );
        }

        if ( $quantity <= 0 ) {
            // Remove item if quantity is 0 or less
            $this->remove_from_sales_session( $merchandise_id );
        } else {
            // Update quantity
            $_SESSION[ $this->sales_key ][ $merchandise_id ]['quantity'] = $quantity;
        }

        return true;
    }

    /**
     * Remove item from sales session.
     *
     * @since    1.0.0
     * @param    int       $merchandise_id    The merchandise ID.
     * @return   bool                         True on success.
     */
    public function remove_from_sales_session( $merchandise_id ) {
        $sales_session = $this->get_sales_session();
        
        if ( isset( $sales_session[ $merchandise_id ] ) ) {
            unset( $_SESSION[ $this->sales_key ][ $merchandise_id ] );
            return true;
        }

        return false;
    }

    /**
     * Clear the sales session.
     *
     * @since    1.0.0
     * @return   bool    True on success.
     */
    public function clear_sales_session() {
        if ( isset( $_SESSION[ $this->sales_key ] ) ) {
            unset( $_SESSION[ $this->sales_key ] );
        }
        return true;
    }

    /**
     * Get sales session item count.
     *
     * @since    1.0.0
     * @return   int    The total number of items in sales session.
     */
    public function get_sales_item_count() {
        $sales_session = $this->get_sales_session();
        $count = 0;
        
        foreach ( $sales_session as $item ) {
            $count += $item['quantity'];
        }
        
        return $count;
    }

    /**
     * Get sales session total.
     *
     * @since    1.0.0
     * @return   float    The sales session total amount.
     */
    public function get_sales_total() {
        $sales_session = $this->get_sales_session();
        $total = 0;
        
        foreach ( $sales_session as $merchandise_id => $item ) {
            $merchandise = new Merchmanager_Merchandise( $merchandise_id );
            if ( $merchandise->get_id() ) {
                $price = $merchandise->get_price();
                $total += $price * $item['quantity'];
            }
        }
        
        return $total;
    }

    /**
     * Get sales session items with full details.
     *
     * @since    1.0.0
     * @return   array    Array of sales session items with merchandise details.
     */
    public function get_sales_items() {
        $sales_session = $this->get_sales_session();
        $items = array();
        
        foreach ( $sales_session as $merchandise_id => $sales_item ) {
            $merchandise = new Merchmanager_Merchandise( $merchandise_id );
            
            if ( $merchandise->get_id() ) {
                $items[] = array(
                    'merchandise_id' => $merchandise_id,
                    'name'           => $merchandise->get_name(),
                    'sku'            => $merchandise->get_sku(),
                    'price'          => $merchandise->get_price(),
                    'quantity'       => $sales_item['quantity'],
                    'variation'      => $sales_item['variation'] ?? array(),
                    'stock'          => $merchandise->get_stock(),
                    'image'          => get_the_post_thumbnail_url( $merchandise_id, 'thumbnail' ),
                    'permalink'      => get_permalink( $merchandise_id ),
                    'subtotal'       => $merchandise->get_price() * $sales_item['quantity'],
                );
            }
        }
        
        return $items;
    }

    /**
     * Validate sales session for recording.
     *
     * @since    1.0.0
     * @return   array    Array of validation results.
     */
    public function validate_sales_session() {
        $sales_session = $this->get_sales_session();
        $errors = array();
        $valid_items = array();
        
        if ( empty( $sales_session ) ) {
            $errors[] = __( 'No items selected for sale.', 'merchmanager' );
            return array( 'errors' => $errors, 'valid_items' => $valid_items );
        }
        
        foreach ( $sales_session as $merchandise_id => $sales_item ) {
            $merchandise = new Merchmanager_Merchandise( $merchandise_id );
            
            if ( ! $merchandise->get_id() ) {
                $errors[] = sprintf( __( 'Item #%d is no longer available.', 'merchmanager' ), $merchandise_id );
                continue;
            }
            
            if ( ! $merchandise->is_active() ) {
                $errors[] = sprintf( __( '%s is no longer available.', 'merchmanager' ), $merchandise->get_name() );
                continue;
            }
            
            $stock = $merchandise->get_stock();
            if ( $stock <= 0 ) {
                $errors[] = sprintf( __( '%s is out of stock.', 'merchmanager' ), $merchandise->get_name() );
                continue;
            }
            
            if ( $sales_item['quantity'] > $stock ) {
                $errors[] = sprintf( 
                    __( 'Only %d of %s available in stock.', 'merchmanager' ), 
                    $stock, 
                    $merchandise->get_name() 
                );
                continue;
            }
            
            // Item is valid
            $valid_items[ $merchandise_id ] = array(
                'quantity' => min( $sales_item['quantity'], $stock ),
                'variation' => $sales_item['variation'] ?? array(),
            );
        }
        
        return array( 'errors' => $errors, 'valid_items' => $valid_items );
    }

    /**
     * Process sales recording.
     *
     * @since    1.0.0
     * @param    array     $sale_data    Sale information including payment type, show ID, etc.
     * @return   array|WP_Error          Sale data on success, WP_Error on failure.
     */
    public function process_sale_recording( $sale_data = array() ) {
        // Validate sales session
        $validation = $this->validate_sales_session();
        
        if ( ! empty( $validation['errors'] ) ) {
            return new WP_Error( 'sales_validation', implode( ' ', $validation['errors'] ) );
        }
        
        if ( empty( $validation['valid_items'] ) ) {
            return new WP_Error( 'empty_sales', __( 'No valid items for sale.', 'merchmanager' ) );
        }
        
        // Create sale records
        $sales_service = new Merchmanager_Sales_Service();
        $sale_results = array();
        
        foreach ( $validation['valid_items'] as $merchandise_id => $item ) {
            $merchandise = new Merchmanager_Merchandise( $merchandise_id );
            
            $sale_result = $sales_service->record_sale( array(
                'merchandise_id' => $merchandise_id,
                'quantity'       => $item['quantity'],
                'price'          => $merchandise->get_price(),
                'payment_type'   => $sale_data['payment_type'] ?? 'cash',
                'show_id'        => $sale_data['show_id'] ?? 0,
                'band_id'        => $merchandise->get_band_id(), // Use the band ID from merchandise
                'notes'          => $sale_data['notes'] ?? '',
                'user_id'        => get_current_user_id(),
            ) );
            
            $sale_results[] = $sale_result;
        }
        
        // Clear sales session on successful recording
        $this->clear_sales_session();
        
        return array(
            'success' => true,
            'message' => __( 'Sale recorded successfully.', 'merchmanager' ),
            'results' => $sale_results,
            'total'   => $this->get_sales_total(),
        );
    }
}
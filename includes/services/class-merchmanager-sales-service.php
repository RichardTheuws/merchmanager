<?php
/**
 * The sales service class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 */

/**
 * The sales service class.
 *
 * This class handles sales operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Sales_Service {

    /**
     * Record a sale.
     *
     * @since    1.0.0
     * @param    array    $sale_data    The sale data.
     * @return   int|WP_Error           The sale ID on success, WP_Error on failure.
     */
    public function record_sale( $sale_data ) {
        global $wpdb;

        // Validate sale data
        $validation = $this->validate_sale_data( $sale_data );
        if ( is_wp_error( $validation ) ) {
            return $validation;
        }

        // Prepare sale data
        $data = array(
            'date'          => isset( $sale_data['date'] ) ? $sale_data['date'] : current_time( 'mysql' ),
            'merchandise_id' => $sale_data['merchandise_id'],
            'quantity'      => $sale_data['quantity'],
            'price'         => $sale_data['price'],
            'payment_type'  => $sale_data['payment_type'],
            'show_id'       => isset( $sale_data['show_id'] ) ? $sale_data['show_id'] : null,
            'sales_page_id' => isset( $sale_data['sales_page_id'] ) ? $sale_data['sales_page_id'] : null,
            'band_id'       => $sale_data['band_id'],
            'user_id'       => isset( $sale_data['user_id'] ) ? $sale_data['user_id'] : get_current_user_id(),
            'notes'         => isset( $sale_data['notes'] ) ? $sale_data['notes'] : '',
            'created_at'    => current_time( 'mysql' ),
            'updated_at'    => current_time( 'mysql' ),
        );

        // Filter sale data
        $data = apply_filters( 'msp_sale_data', $data );

        // Trigger action before sale is recorded
        do_action( 'msp_before_sale_recorded', $data );

        // Insert sale
        $table_name = $wpdb->prefix . 'msp_sales';
        $result = $wpdb->insert(
            $table_name,
            $data,
            array(
                '%s', // date
                '%d', // merchandise_id
                '%d', // quantity
                '%f', // price
                '%s', // payment_type
                '%d', // show_id
                '%d', // sales_page_id
                '%d', // band_id
                '%d', // user_id
                '%s', // notes
                '%s', // created_at
                '%s', // updated_at
            )
        );

        // Check for errors
        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Error recording sale.', 'merchmanager' ), $wpdb->last_error );
        }

        // Get sale ID
        $sale_id = $wpdb->insert_id;

        // Update merchandise stock
        $this->update_stock( $sale_data['merchandise_id'], -$sale_data['quantity'], 'sale', $sale_id );

        // Trigger action after sale is recorded
        do_action( 'msp_after_sale_recorded', $sale_id, $data );

        return $sale_id;
    }

    /**
     * Record multiple sales.
     *
     * @since    1.0.0
     * @param    array    $sales_data    Array of sale data.
     * @return   array                   Array of sale IDs or WP_Error objects.
     */
    public function record_multiple_sales( $sales_data ) {
        $results = array();

        foreach ( $sales_data as $sale_data ) {
            $results[] = $this->record_sale( $sale_data );
        }

        return $results;
    }

    /**
     * Delete a sale.
     *
     * @since    1.0.0
     * @param    int      $sale_id    The sale ID.
     * @return   bool|WP_Error        True on success, WP_Error on failure.
     */
    public function delete_sale( $sale_id ) {
        global $wpdb;

        // Get sale data
        $table_name = $wpdb->prefix . 'msp_sales';
        $sale = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $sale_id ) );

        if ( ! $sale ) {
            return new WP_Error( 'invalid_sale', __( 'Sale not found.', 'merchmanager' ) );
        }

        // Trigger action before sale is deleted
        do_action( 'msp_before_sale_delete', $sale_id );

        // Delete sale
        $result = $wpdb->delete(
            $table_name,
            array( 'id' => $sale_id ),
            array( '%d' )
        );

        // Check for errors
        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Error deleting sale.', 'merchmanager' ), $wpdb->last_error );
        }

        // Update merchandise stock (add back the quantity)
        $this->update_stock( $sale->merchandise_id, $sale->quantity, 'sale_deleted', $sale_id );

        // Trigger action after sale is deleted
        do_action( 'msp_after_sale_delete', $sale_id );

        return true;
    }

    /**
     * Get sales.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Array of sales data.
     */
    public function get_sales( $args = array() ) {
        global $wpdb;

        // Default arguments
        $default_args = array(
            'band_id'       => 0,
            'show_id'       => 0,
            'sales_page_id' => 0,
            'merchandise_id' => 0,
            'payment_type'  => '',
            'start_date'    => '',
            'end_date'      => '',
            'user_id'       => 0,
            'orderby'       => 'date',
            'order'         => 'DESC',
            'limit'         => 0,
            'offset'        => 0,
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Build query
        $table_name = $wpdb->prefix . 'msp_sales';
        $query = "SELECT * FROM $table_name WHERE 1=1";

        // Add filters
        if ( $args['band_id'] ) {
            $query .= $wpdb->prepare( " AND band_id = %d", $args['band_id'] );
        }
        if ( $args['show_id'] ) {
            $query .= $wpdb->prepare( " AND show_id = %d", $args['show_id'] );
        }
        if ( $args['sales_page_id'] ) {
            $query .= $wpdb->prepare( " AND sales_page_id = %d", $args['sales_page_id'] );
        }
        if ( $args['merchandise_id'] ) {
            $query .= $wpdb->prepare( " AND merchandise_id = %d", $args['merchandise_id'] );
        }
        if ( $args['payment_type'] ) {
            $query .= $wpdb->prepare( " AND payment_type = %s", $args['payment_type'] );
        }
        if ( $args['start_date'] ) {
            $query .= $wpdb->prepare( " AND date >= %s", $args['start_date'] );
        }
        if ( $args['end_date'] ) {
            $query .= $wpdb->prepare( " AND date <= %s", $args['end_date'] );
        }
        if ( $args['user_id'] ) {
            $query .= $wpdb->prepare( " AND user_id = %d", $args['user_id'] );
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

        // Get sales
        $sales = $wpdb->get_results( $query );

        return $sales;
    }

    /**
     * Get sales summary.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Sales summary data.
     */
    public function get_sales_summary( $args = array() ) {
        global $wpdb;

        // Default arguments
        $default_args = array(
            'band_id'       => 0,
            'show_id'       => 0,
            'sales_page_id' => 0,
            'merchandise_id' => 0,
            'payment_type'  => '',
            'start_date'    => '',
            'end_date'      => '',
            'user_id'       => 0,
            'group_by'      => '', // '', 'day', 'week', 'month', 'merchandise', 'payment_type'
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Build query
        $table_name = $wpdb->prefix . 'msp_sales';
        $query = "SELECT COUNT(*) as count, SUM(quantity) as total_quantity, SUM(price * quantity) as total_amount";

        // Add group by
        if ( $args['group_by'] ) {
            switch ( $args['group_by'] ) {
                case 'day':
                    $query .= ", DATE(date) as day";
                    break;
                case 'week':
                    $query .= ", YEARWEEK(date) as week";
                    break;
                case 'month':
                    $query .= ", DATE_FORMAT(date, '%Y-%m') as month";
                    break;
                case 'merchandise':
                    $query .= ", merchandise_id";
                    break;
                case 'payment_type':
                    $query .= ", payment_type";
                    break;
            }
        }

        $query .= " FROM $table_name WHERE 1=1";

        // Add filters
        if ( $args['band_id'] ) {
            $query .= $wpdb->prepare( " AND band_id = %d", $args['band_id'] );
        }
        if ( $args['show_id'] ) {
            $query .= $wpdb->prepare( " AND show_id = %d", $args['show_id'] );
        }
        if ( $args['sales_page_id'] ) {
            $query .= $wpdb->prepare( " AND sales_page_id = %d", $args['sales_page_id'] );
        }
        if ( $args['merchandise_id'] ) {
            $query .= $wpdb->prepare( " AND merchandise_id = %d", $args['merchandise_id'] );
        }
        if ( $args['payment_type'] ) {
            $query .= $wpdb->prepare( " AND payment_type = %s", $args['payment_type'] );
        }
        if ( $args['start_date'] ) {
            $query .= $wpdb->prepare( " AND date >= %s", $args['start_date'] );
        }
        if ( $args['end_date'] ) {
            $query .= $wpdb->prepare( " AND date <= %s", $args['end_date'] );
        }
        if ( $args['user_id'] ) {
            $query .= $wpdb->prepare( " AND user_id = %d", $args['user_id'] );
        }

        // Add group by
        if ( $args['group_by'] ) {
            switch ( $args['group_by'] ) {
                case 'day':
                    $query .= " GROUP BY day ORDER BY day ASC";
                    break;
                case 'week':
                    $query .= " GROUP BY week ORDER BY week ASC";
                    break;
                case 'month':
                    $query .= " GROUP BY month ORDER BY month ASC";
                    break;
                case 'merchandise':
                    $query .= " GROUP BY merchandise_id ORDER BY total_amount DESC";
                    break;
                case 'payment_type':
                    $query .= " GROUP BY payment_type ORDER BY total_amount DESC";
                    break;
            }
        }

        // Get summary
        $summary = $wpdb->get_results( $query );

        // If grouped by merchandise, add merchandise details
        if ( $args['group_by'] === 'merchandise' && ! empty( $summary ) ) {
            foreach ( $summary as &$item ) {
                if ( isset( $item->merchandise_id ) ) {
                    $merchandise = new Merchmanager_Merchandise( $item->merchandise_id );
                    $item->merchandise_name = $merchandise->get_name();
                    $item->merchandise_sku = $merchandise->get_sku();
                }
            }
        }

        return $summary;
    }

    /**
     * Get top selling merchandise.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Top selling merchandise data.
     */
    public function get_top_selling_merchandise( $args = array() ) {
        // Set group by to merchandise
        $args['group_by'] = 'merchandise';
        
        // Get sales summary
        $summary = $this->get_sales_summary( $args );
        
        return $summary;
    }

    /**
     * Get sales by payment type.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Sales by payment type data.
     */
    public function get_sales_by_payment_type( $args = array() ) {
        // Set group by to payment_type
        $args['group_by'] = 'payment_type';
        
        // Get sales summary
        $summary = $this->get_sales_summary( $args );
        
        return $summary;
    }

    /**
     * Get sales by date.
     *
     * @since    1.0.0
     * @param    array    $args    Query arguments.
     * @return   array             Sales by date data.
     */
    public function get_sales_by_date( $args = array() ) {
        // Set group by based on date range
        if ( ! isset( $args['group_by'] ) || ! in_array( $args['group_by'], array( 'day', 'week', 'month' ) ) ) {
            // Determine appropriate grouping based on date range
            if ( isset( $args['start_date'] ) && isset( $args['end_date'] ) ) {
                $start = strtotime( $args['start_date'] );
                $end = strtotime( $args['end_date'] );
                $diff_days = ( $end - $start ) / DAY_IN_SECONDS;
                
                if ( $diff_days <= 31 ) {
                    $args['group_by'] = 'day';
                } elseif ( $diff_days <= 90 ) {
                    $args['group_by'] = 'week';
                } else {
                    $args['group_by'] = 'month';
                }
            } else {
                // Default to month if no date range specified
                $args['group_by'] = 'month';
            }
        }
        
        // Get sales summary
        $summary = $this->get_sales_summary( $args );
        
        return $summary;
    }

    /**
     * Export sales to CSV.
     *
     * @since    1.0.0
     * @param    array     $args        Query arguments.
     * @param    string    $file_path   Path to save the CSV file.
     * @return   array                  Array with export results.
     */
    public function export_sales_to_csv( $args, $file_path ) {
        // Get sales
        $sales = $this->get_sales( $args );
        
        if ( empty( $sales ) ) {
            return array(
                'success' => false,
                'message' => __( 'No sales found.', 'merchmanager' ),
            );
        }
        
        // Get CSV delimiter
        $options = get_option( 'msp_settings', array() );
        $delimiter = isset( $options['csv_delimiter'] ) ? $options['csv_delimiter'] : ',';
        if ( $delimiter === '\t' ) {
            $delimiter = "\t";
        }
        
        // Open the file
        $file = fopen( $file_path, 'w' );
        if ( ! $file ) {
            return array(
                'success' => false,
                'message' => __( 'Could not create CSV file.', 'merchmanager' ),
            );
        }
        
        // Write header row
        $header = array(
            'ID',
            'Date',
            'Merchandise ID',
            'Merchandise Name',
            'Quantity',
            'Price',
            'Total',
            'Payment Type',
            'Show ID',
            'Show Name',
            'Sales Page ID',
            'Band ID',
            'Band Name',
            'User ID',
            'User Name',
            'Notes',
        );
        fputcsv( $file, $header, $delimiter );
        
        // Write data rows
        foreach ( $sales as $sale ) {
            // Get related data
            $merchandise = new Merchmanager_Merchandise( $sale->merchandise_id );
            $band = new Merchmanager_Band( $sale->band_id );
            $show = $sale->show_id ? new Merchmanager_Show( $sale->show_id ) : null;
            $user = $sale->user_id ? get_user_by( 'id', $sale->user_id ) : null;
            
            $row = array(
                $sale->id,
                $sale->date,
                $sale->merchandise_id,
                $merchandise->get_name(),
                $sale->quantity,
                $sale->price,
                $sale->price * $sale->quantity,
                $sale->payment_type,
                $sale->show_id,
                $show ? $show->get_name() : '',
                $sale->sales_page_id,
                $sale->band_id,
                $band->get_name(),
                $sale->user_id,
                $user ? $user->display_name : '',
                $sale->notes,
            );
            fputcsv( $file, $row, $delimiter );
        }
        
        // Close the file
        fclose( $file );
        
        return array(
            'success' => true,
            'message' => sprintf(
                __( 'Export completed: %d sales exported to %s.', 'merchmanager' ),
                count( $sales ),
                basename( $file_path )
            ),
            'file_path' => $file_path,
        );
    }

    /**
     * Validate sale data.
     *
     * @since    1.0.0
     * @param    array    $sale_data    The sale data.
     * @return   true|WP_Error          True on success, WP_Error on failure.
     */
    private function validate_sale_data( $sale_data ) {
        // Check required fields
        $required_fields = array( 'merchandise_id', 'quantity', 'price', 'payment_type', 'band_id' );
        foreach ( $required_fields as $field ) {
            if ( ! isset( $sale_data[ $field ] ) ) {
                return new WP_Error( 'missing_field', sprintf( __( 'Missing required field: %s', 'merchmanager' ), $field ) );
            }
        }

        // Validate merchandise ID
        $merchandise = new Merchmanager_Merchandise( $sale_data['merchandise_id'] );
        if ( ! $merchandise->get_id() ) {
            return new WP_Error( 'invalid_merchandise', __( 'Invalid merchandise ID.', 'merchmanager' ) );
        }

        // Validate band ID
        $band = new Merchmanager_Band( $sale_data['band_id'] );
        if ( ! $band->get_id() ) {
            return new WP_Error( 'invalid_band', __( 'Invalid band ID.', 'merchmanager' ) );
        }

        // Validate show ID if provided
        if ( isset( $sale_data['show_id'] ) && $sale_data['show_id'] ) {
            $show = new Merchmanager_Show( $sale_data['show_id'] );
            if ( ! $show->get_id() ) {
                return new WP_Error( 'invalid_show', __( 'Invalid show ID.', 'merchmanager' ) );
            }
        }

        // Validate sales page ID if provided
        if ( isset( $sale_data['sales_page_id'] ) && $sale_data['sales_page_id'] ) {
            $sales_page = new Merchmanager_Sales_Page( $sale_data['sales_page_id'] );
            if ( ! $sales_page->get_id() ) {
                return new WP_Error( 'invalid_sales_page', __( 'Invalid sales page ID.', 'merchmanager' ) );
            }
        }

        // Validate quantity
        if ( ! is_numeric( $sale_data['quantity'] ) || $sale_data['quantity'] <= 0 ) {
            return new WP_Error( 'invalid_quantity', __( 'Quantity must be a positive number.', 'merchmanager' ) );
        }

        // Validate price
        if ( ! is_numeric( $sale_data['price'] ) || $sale_data['price'] < 0 ) {
            return new WP_Error( 'invalid_price', __( 'Price must be a non-negative number.', 'merchmanager' ) );
        }

        // Validate stock
        $stock = $merchandise->get_stock();
        if ( $stock < $sale_data['quantity'] ) {
            return new WP_Error( 'insufficient_stock', __( 'Insufficient stock.', 'merchmanager' ) );
        }

        return true;
    }

    /**
     * Update merchandise stock.
     *
     * @since    1.0.0
     * @param    int       $merchandise_id    The merchandise ID.
     * @param    int       $quantity          The quantity to add or subtract.
     * @param    string    $reason            The reason for the stock change.
     * @param    int       $reference_id      The reference ID (e.g., sale ID).
     * @return   bool                         True on success, false on failure.
     */
    private function update_stock( $merchandise_id, $quantity, $reason, $reference_id = 0 ) {
        // Get merchandise
        $merchandise = new Merchmanager_Merchandise( $merchandise_id );
        if ( ! $merchandise->get_id() ) {
            return false;
        }

        // Update stock
        $notes = sprintf( __( 'Stock change due to %s (ID: %d)', 'merchmanager' ), $reason, $reference_id );
        $result = $merchandise->update_stock( $quantity, $reason, get_current_user_id(), $notes );

        return $result;
    }
}
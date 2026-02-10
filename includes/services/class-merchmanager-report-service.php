<?php
/**
 * The report service class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 */

/**
 * The report service class.
 *
 * This class handles report generation.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 * @author     Theuws Consulting
 */
class Merchmanager_Report_Service {

    /**
     * Sales service instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      Merchmanager_Sales_Service    $sales_service    Sales service instance.
     */
    private $sales_service;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->sales_service = new Merchmanager_Sales_Service();
    }

    /**
     * Generate a sales report.
     *
     * @since    1.0.0
     * @param    array    $args    Report arguments.
     * @return   array             Report data.
     */
    public function generate_sales_report( $args = array() ) {
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
            'group_by'      => 'day', // 'day', 'week', 'month', 'merchandise', 'payment_type'
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Get summary totals (no grouping: one row with global totals).
        $summary_args = array_merge( $args, array( 'group_by' => '' ) );
        $summary     = $this->sales_service->get_sales_summary( $summary_args );

        $total_sales    = 0;
        $total_quantity = 0;
        $total_amount   = 0.0;
        if ( ! empty( $summary ) && isset( $summary[0] ) ) {
            $total_sales    = (int) $summary[0]->count;
            $total_quantity = (int) $summary[0]->total_quantity;
            $total_amount   = (float) $summary[0]->total_amount;
        }

        // Reconciliatie-check (failsafe): compare with raw aggregation.
        $raw = $this->sales_service->get_sales_totals_raw( $args );
        if ( $raw !== null ) {
            $raw_count   = (int) $raw->count;
            $raw_qty     = (int) $raw->total_quantity;
            $raw_amount  = (float) $raw->total_amount;
            $amount_diff = abs( (float) $total_amount - $raw_amount );
            if ( $total_sales !== $raw_count || $total_quantity !== $raw_qty || $amount_diff > 0.01 ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
                    error_log( '[MerchManager] Report integrity check failed: summary vs raw mismatch. Filter: ' . wp_json_encode( $args ) );
                }
                return array(
                    'integrity_error'   => true,
                    'integrity_message' => __( 'Data consistency check failed. Please try again or contact support.', 'merchmanager' ),
                    'summary'           => array(
                        'total_sales'    => 0,
                        'total_quantity' => 0,
                        'total_amount'   => 0.0,
                    ),
                    'sales_by_date'     => array(),
                    'top_merchandise'   => array(),
                    'sales_by_payment'  => array(),
                );
            }
        }

        // Get sales by date (grouped).
        $sales_by_date = $this->sales_service->get_sales_by_date( $args );

        // Get top selling merchandise
        $top_merchandise = $this->sales_service->get_top_selling_merchandise( $args );

        // Get sales by payment type
        $sales_by_payment = $this->sales_service->get_sales_by_payment_type( $args );

        $report = array(
            'summary'           => array(
                'total_sales'    => $total_sales,
                'total_quantity' => $total_quantity,
                'total_amount'   => $total_amount,
            ),
            'sales_by_date'     => $sales_by_date,
            'top_merchandise'   => $top_merchandise,
            'sales_by_payment'  => $sales_by_payment,
        );

        return $report;
    }

    /**
     * Generate an inventory report.
     *
     * @since    1.0.0
     * @param    array    $args    Report arguments.
     * @return   array             Report data.
     */
    public function generate_inventory_report( $args = array() ) {
        // Default arguments
        $default_args = array(
            'band_id'           => 0,
            'category'          => '',
            'low_stock_only'    => false,
            'include_inactive'  => false,
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Prepare query arguments
        $query_args = array(
            'post_type'      => 'msp_merchandise',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(),
        );

        // Add band filter
        if ( $args['band_id'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_band_id',
                'value' => $args['band_id'],
            );
        }

        // Add category filter
        if ( $args['category'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_category',
                'value' => $args['category'],
            );
        }

        // Add active filter
        if ( ! $args['include_inactive'] ) {
            $query_args['meta_query'][] = array(
                'key'   => '_msp_merchandise_active',
                'value' => '1',
            );
        }

        // Get merchandise
        $merchandise_posts = get_posts( $query_args );
        $merchandise_items = array();

        foreach ( $merchandise_posts as $post ) {
            $merchandise = new Merchmanager_Merchandise( $post->ID );
            
            // Skip if low stock only and not low stock
            if ( $args['low_stock_only'] ) {
                $stock = $merchandise->get_stock();
                $threshold = $merchandise->get_low_stock_threshold();
                
                if ( ! $threshold ) {
                    // Use default threshold from settings
                    $options = get_option( 'msp_settings', array() );
                    $threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;
                }
                
                if ( $stock > $threshold ) {
                    continue;
                }
            }
            
            // Add merchandise data
            $merchandise_items[] = array(
                'id'                => $merchandise->get_id(),
                'name'              => $merchandise->get_name(),
                'sku'               => $merchandise->get_sku(),
                'price'             => $merchandise->get_price(),
                'stock'             => $merchandise->get_stock(),
                'low_stock_threshold' => $merchandise->get_low_stock_threshold(),
                'category'          => $merchandise->get_category(),
                'size'              => $merchandise->get_size(),
                'color'             => $merchandise->get_color(),
                'active'            => $merchandise->is_active(),
            );
        }

        // Calculate totals
        $total_items = count( $merchandise_items );
        $total_stock = 0;
        $total_value = 0;
        $low_stock_count = 0;
        $out_of_stock_count = 0;

        foreach ( $merchandise_items as $item ) {
            $total_stock += $item['stock'];
            $total_value += $item['stock'] * $item['price'];
            
            if ( $item['stock'] <= $item['low_stock_threshold'] && $item['stock'] > 0 ) {
                $low_stock_count++;
            }
            
            if ( $item['stock'] <= 0 ) {
                $out_of_stock_count++;
            }
        }

        // Prepare report data
        $report = array(
            'summary' => array(
                'total_items' => $total_items,
                'total_stock' => $total_stock,
                'total_value' => $total_value,
                'low_stock_count' => $low_stock_count,
                'out_of_stock_count' => $out_of_stock_count,
            ),
            'items' => $merchandise_items,
        );

        return $report;
    }

    /**
     * Generate a tour report.
     *
     * @since    1.0.0
     * @param    int      $tour_id    The tour ID.
     * @return   array                Report data.
     */
    public function generate_tour_report( $tour_id ) {
        // Get tour
        $tour = new Merchmanager_Tour( $tour_id );
        if ( ! $tour->get_id() ) {
            return array(
                'error' => __( 'Invalid tour ID.', 'merchmanager' ),
            );
        }

        // Get band
        $band = $tour->get_band();
        if ( ! $band ) {
            return array(
                'error' => __( 'Invalid band ID.', 'merchmanager' ),
            );
        }

        // Get shows
        $shows = $tour->get_shows();
        $show_data = array();
        $total_sales = 0;
        $total_amount = 0;

        foreach ( $shows as $show ) {
            // Get sales for this show
            $sales_args = array(
                'show_id' => $show->get_id(),
            );
            $sales_summary = $this->sales_service->get_sales_summary( $sales_args );
            
            $show_sales = 0;
            $show_amount = 0;
            
            if ( ! empty( $sales_summary ) && isset( $sales_summary[0] ) ) {
                $show_sales = $sales_summary[0]->count;
                $show_amount = $sales_summary[0]->total_amount;
            }
            
            $total_sales += $show_sales;
            $total_amount += $show_amount;
            
            // Add show data
            $show_data[] = array(
                'id'            => $show->get_id(),
                'name'          => $show->get_name(),
                'date'          => $show->get_date(),
                'venue_name'    => $show->get_venue_name(),
                'venue_city'    => $show->get_venue_city(),
                'venue_state'   => $show->get_venue_state(),
                'sales_count'   => $show_sales,
                'sales_amount'  => $show_amount,
            );
        }

        // Get top selling merchandise for this tour
        $merchandise_args = array(
            'band_id' => $band->get_id(),
            'group_by' => 'merchandise',
        );
        
        // If tour has shows, add show IDs to args
        if ( ! empty( $shows ) ) {
            $show_ids = array();
            foreach ( $shows as $show ) {
                $show_ids[] = $show->get_id();
            }
            
            $merchandise_args['show_ids'] = $show_ids;
        }
        
        $top_merchandise = $this->sales_service->get_top_selling_merchandise( $merchandise_args );

        // Prepare report data
        $report = array(
            'tour' => array(
                'id'            => $tour->get_id(),
                'name'          => $tour->get_name(),
                'start_date'    => $tour->get_start_date(),
                'end_date'      => $tour->get_end_date(),
                'status'        => $tour->get_status(),
            ),
            'band' => array(
                'id'            => $band->get_id(),
                'name'          => $band->get_name(),
            ),
            'summary' => array(
                'total_shows'   => count( $shows ),
                'total_sales'   => $total_sales,
                'total_amount'  => $total_amount,
            ),
            'shows' => $show_data,
            'top_merchandise' => $top_merchandise,
        );

        return $report;
    }

    /**
     * Export report to CSV.
     *
     * @since    1.0.0
     * @param    array     $report      Report data.
     * @param    string    $report_type Report type ('sales', 'inventory', 'tour').
     * @param    string    $file_path   Path to save the CSV file.
     * @return   array                  Array with export results.
     */
    public function export_report_to_csv( $report, $report_type, $file_path ) {
        // Get CSV delimiter
        $options = get_option( 'msp_settings', array() );
        $delimiter = isset( $options['csv_delimiter'] ) ? $options['csv_delimiter'] : ',';
        if ( $delimiter === '\t' ) {
            $delimiter = "\t";
        }
        
        // Open the file (CSV export requires stream for fputcsv).
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        $file = fopen( $file_path, 'w' );
        if ( ! $file ) {
            return array(
                'success' => false,
                'message' => __( 'Could not create CSV file.', 'merchmanager' ),
            );
        }
        
        // Export based on report type
        switch ( $report_type ) {
            case 'sales':
                $this->export_sales_report_to_csv( $report, $file, $delimiter );
                break;
                
            case 'inventory':
                $this->export_inventory_report_to_csv( $report, $file, $delimiter );
                break;
                
            case 'tour':
                $this->export_tour_report_to_csv( $report, $file, $delimiter );
                break;
                
            default:
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Close handle before returning error; path from validated temp file.
                fclose( $file );
                return array(
                    'success' => false,
                    'message' => __( 'Invalid report type.', 'merchmanager' ),
                );
        }
        
        // Close the file
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
        fclose( $file );
        
        return array(
            'success' => true,
            'message' => sprintf(
                /* translators: %1$s: filename */
                __( 'Report exported to %1$s.', 'merchmanager' ),
                basename( $file_path )
            ),
            'file_path' => $file_path,
        );
    }

    /**
     * Export sales report to CSV.
     *
     * @since    1.0.0
     * @param    array     $report     Report data.
     * @param    resource  $file       File handle.
     * @param    string    $delimiter  CSV delimiter.
     * @return   void
     */
    private function export_sales_report_to_csv( $report, $file, $delimiter ) {
        // Write summary
        fputcsv( $file, array( __( 'Sales Report Summary', 'merchmanager' ) ), $delimiter );
        fputcsv( $file, array(
            __( 'Total Sales', 'merchmanager' ),
            __( 'Total Quantity', 'merchmanager' ),
            __( 'Total Amount', 'merchmanager' ),
        ), $delimiter );
        fputcsv( $file, array(
            $report['summary']['total_sales'],
            $report['summary']['total_quantity'],
            $report['summary']['total_amount'],
        ), $delimiter );
        fputcsv( $file, array(), $delimiter ); // Empty line
        
        // Write top merchandise
        if ( ! empty( $report['top_merchandise'] ) ) {
            fputcsv( $file, array( __( 'Top Selling Merchandise', 'merchmanager' ) ), $delimiter );
            fputcsv( $file, array(
                __( 'Merchandise ID', 'merchmanager' ),
                __( 'Merchandise Name', 'merchmanager' ),
                __( 'Quantity Sold', 'merchmanager' ),
                __( 'Total Amount', 'merchmanager' ),
            ), $delimiter );
            
            foreach ( $report['top_merchandise'] as $item ) {
                fputcsv( $file, array(
                    $item->merchandise_id,
                    isset( $item->merchandise_name ) ? $item->merchandise_name : '',
                    $item->total_quantity,
                    $item->total_amount,
                ), $delimiter );
            }
            
            fputcsv( $file, array(), $delimiter ); // Empty line
        }
        
        // Write sales by payment type
        if ( ! empty( $report['sales_by_payment'] ) ) {
            fputcsv( $file, array( __( 'Sales by Payment Type', 'merchmanager' ) ), $delimiter );
            fputcsv( $file, array(
                __( 'Payment Type', 'merchmanager' ),
                __( 'Number of Sales', 'merchmanager' ),
                __( 'Total Amount', 'merchmanager' ),
            ), $delimiter );
            
            foreach ( $report['sales_by_payment'] as $item ) {
                fputcsv( $file, array(
                    $item->payment_type,
                    $item->count,
                    $item->total_amount,
                ), $delimiter );
            }
            
            fputcsv( $file, array(), $delimiter ); // Empty line
        }
        
        // Write sales by date
        if ( ! empty( $report['sales_by_date'] ) ) {
            fputcsv( $file, array( __( 'Sales by Date', 'merchmanager' ) ), $delimiter );
            
            // Determine date format based on grouping
            $date_header = __( 'Date', 'merchmanager' );
            if ( isset( $report['sales_by_date'][0]->week ) ) {
                $date_header = __( 'Week', 'merchmanager' );
            } elseif ( isset( $report['sales_by_date'][0]->month ) ) {
                $date_header = __( 'Month', 'merchmanager' );
            }
            
            fputcsv( $file, array(
                $date_header,
                __( 'Number of Sales', 'merchmanager' ),
                __( 'Total Quantity', 'merchmanager' ),
                __( 'Total Amount', 'merchmanager' ),
            ), $delimiter );
            
            foreach ( $report['sales_by_date'] as $item ) {
                $date_value = '';
                if ( isset( $item->day ) ) {
                    $date_value = $item->day;
                } elseif ( isset( $item->week ) ) {
                    $date_value = $item->week;
                } elseif ( isset( $item->month ) ) {
                    $date_value = $item->month;
                }
                
                fputcsv( $file, array(
                    $date_value,
                    $item->count,
                    $item->total_quantity,
                    $item->total_amount,
                ), $delimiter );
            }
        }
    }

    /**
     * Export inventory report to CSV.
     *
     * @since    1.0.0
     * @param    array     $report     Report data.
     * @param    resource  $file       File handle.
     * @param    string    $delimiter  CSV delimiter.
     * @return   void
     */
    private function export_inventory_report_to_csv( $report, $file, $delimiter ) {
        // Write summary
        fputcsv( $file, array( __( 'Inventory Report Summary', 'merchmanager' ) ), $delimiter );
        fputcsv( $file, array(
            __( 'Total Items', 'merchmanager' ),
            __( 'Total Stock', 'merchmanager' ),
            __( 'Total Value', 'merchmanager' ),
            __( 'Low Stock Items', 'merchmanager' ),
            __( 'Out of Stock Items', 'merchmanager' ),
        ), $delimiter );
        fputcsv( $file, array(
            $report['summary']['total_items'],
            $report['summary']['total_stock'],
            $report['summary']['total_value'],
            $report['summary']['low_stock_count'],
            $report['summary']['out_of_stock_count'],
        ), $delimiter );
        fputcsv( $file, array(), $delimiter ); // Empty line
        
        // Write items
        if ( ! empty( $report['items'] ) ) {
            fputcsv( $file, array( __( 'Inventory Items', 'merchmanager' ) ), $delimiter );
            fputcsv( $file, array(
                __( 'ID', 'merchmanager' ),
                __( 'Name', 'merchmanager' ),
                __( 'SKU', 'merchmanager' ),
                __( 'Category', 'merchmanager' ),
                __( 'Size', 'merchmanager' ),
                __( 'Color', 'merchmanager' ),
                __( 'Price', 'merchmanager' ),
                __( 'Stock', 'merchmanager' ),
                __( 'Low Stock Threshold', 'merchmanager' ),
                __( 'Value', 'merchmanager' ),
                __( 'Status', 'merchmanager' ),
            ), $delimiter );
            
            foreach ( $report['items'] as $item ) {
                fputcsv( $file, array(
                    $item['id'],
                    $item['name'],
                    $item['sku'],
                    $item['category'],
                    $item['size'],
                    $item['color'],
                    $item['price'],
                    $item['stock'],
                    $item['low_stock_threshold'],
                    $item['stock'] * $item['price'],
                    $item['active'] ? __( 'Active', 'merchmanager' ) : __( 'Inactive', 'merchmanager' ),
                ), $delimiter );
            }
        }
    }

    /**
     * Export tour report to CSV.
     *
     * @since    1.0.0
     * @param    array     $report     Report data.
     * @param    resource  $file       File handle.
     * @param    string    $delimiter  CSV delimiter.
     * @return   void
     */
    private function export_tour_report_to_csv( $report, $file, $delimiter ) {
        // Write tour info
        fputcsv( $file, array( __( 'Tour Report', 'merchmanager' ) ), $delimiter );
        fputcsv( $file, array(
            __( 'Tour Name', 'merchmanager' ),
            $report['tour']['name'],
        ), $delimiter );
        fputcsv( $file, array(
            __( 'Band Name', 'merchmanager' ),
            $report['band']['name'],
        ), $delimiter );
        fputcsv( $file, array(
            __( 'Start Date', 'merchmanager' ),
            $report['tour']['start_date'],
        ), $delimiter );
        fputcsv( $file, array(
            __( 'End Date', 'merchmanager' ),
            $report['tour']['end_date'],
        ), $delimiter );
        fputcsv( $file, array(
            __( 'Status', 'merchmanager' ),
            $report['tour']['status'],
        ), $delimiter );
        fputcsv( $file, array(), $delimiter ); // Empty line
        
        // Write summary
        fputcsv( $file, array( __( 'Tour Summary', 'merchmanager' ) ), $delimiter );
        fputcsv( $file, array(
            __( 'Total Shows', 'merchmanager' ),
            __( 'Total Sales', 'merchmanager' ),
            __( 'Total Amount', 'merchmanager' ),
        ), $delimiter );
        fputcsv( $file, array(
            $report['summary']['total_shows'],
            $report['summary']['total_sales'],
            $report['summary']['total_amount'],
        ), $delimiter );
        fputcsv( $file, array(), $delimiter ); // Empty line
        
        // Write shows
        if ( ! empty( $report['shows'] ) ) {
            fputcsv( $file, array( __( 'Shows', 'merchmanager' ) ), $delimiter );
            fputcsv( $file, array(
                __( 'ID', 'merchmanager' ),
                __( 'Name', 'merchmanager' ),
                __( 'Date', 'merchmanager' ),
                __( 'Venue', 'merchmanager' ),
                __( 'City', 'merchmanager' ),
                __( 'State', 'merchmanager' ),
                __( 'Sales Count', 'merchmanager' ),
                __( 'Sales Amount', 'merchmanager' ),
            ), $delimiter );
            
            foreach ( $report['shows'] as $show ) {
                fputcsv( $file, array(
                    $show['id'],
                    $show['name'],
                    $show['date'],
                    $show['venue_name'],
                    $show['venue_city'],
                    $show['venue_state'],
                    $show['sales_count'],
                    $show['sales_amount'],
                ), $delimiter );
            }
            
            fputcsv( $file, array(), $delimiter ); // Empty line
        }
        
        // Write top merchandise
        if ( ! empty( $report['top_merchandise'] ) ) {
            fputcsv( $file, array( __( 'Top Selling Merchandise', 'merchmanager' ) ), $delimiter );
            fputcsv( $file, array(
                __( 'Merchandise ID', 'merchmanager' ),
                __( 'Merchandise Name', 'merchmanager' ),
                __( 'Quantity Sold', 'merchmanager' ),
                __( 'Total Amount', 'merchmanager' ),
            ), $delimiter );
            
            foreach ( $report['top_merchandise'] as $item ) {
                fputcsv( $file, array(
                    $item->merchandise_id,
                    isset( $item->merchandise_name ) ? $item->merchandise_name : '',
                    $item->total_quantity,
                    $item->total_amount,
                ), $delimiter );
            }
        }
    }
}
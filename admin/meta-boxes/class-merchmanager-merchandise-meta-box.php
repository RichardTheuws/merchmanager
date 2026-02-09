<?php
/**
 * The merchandise meta box class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 */

/**
 * The merchandise meta box class.
 *
 * Defines the meta box for the merchandise post type.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 * @author     Theuws Consulting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Merchmanager_Merchandise_Meta_Box {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_msp_merchandise', array( $this, 'save_meta_boxes' ), 10, 2 );
    }

    /**
     * Add meta boxes.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'msp_merchandise_details',
            __( 'Merchandise Details', 'merchmanager' ),
            array( $this, 'render_details_meta_box' ),
            'msp_merchandise',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_merchandise_pricing',
            __( 'Pricing and Inventory', 'merchmanager' ),
            array( $this, 'render_pricing_meta_box' ),
            'msp_merchandise',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_merchandise_attributes',
            __( 'Attributes', 'merchmanager' ),
            array( $this, 'render_attributes_meta_box' ),
            'msp_merchandise',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_merchandise_supplier',
            __( 'Supplier Information', 'merchmanager' ),
            array( $this, 'render_supplier_meta_box' ),
            'msp_merchandise',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_merchandise_sales',
            __( 'Sales History', 'merchmanager' ),
            array( $this, 'render_sales_meta_box' ),
            'msp_merchandise',
            'normal',
            'high'
        );
    }

    /**
     * Render the details meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_details_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'msp_merchandise_meta_box', 'msp_merchandise_meta_box_nonce' );

        // Get merchandise data
        $merchandise = new Merchmanager_Merchandise( $post->ID );
        $band_id = $merchandise->get_band_id();
        $sku = $merchandise->get_sku();
        $category = $merchandise->get_category();
        $active = $merchandise->is_active();

        // Get bands
        $bands = Merchmanager_Band::get_all();

        // Get categories
        $categories = array(
            'apparel'   => __( 'Apparel', 'merchmanager' ),
            'music'     => __( 'Music', 'merchmanager' ),
            'accessory' => __( 'Accessory', 'merchmanager' ),
            'poster'    => __( 'Poster', 'merchmanager' ),
            'other'     => __( 'Other', 'merchmanager' ),
        );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_merchandise_band_id">
                <?php esc_html_e( 'Band', 'merchmanager' ); ?>
            </label>
            <select id="msp_merchandise_band_id" name="msp_merchandise_band_id" class="regular-text">
                <option value=""><?php esc_html_e( 'Select a band...', 'merchmanager' ); ?></option>
                <?php foreach ( $bands as $band ) : ?>
                    <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $band_id, $band->get_id() ); ?>>
                        <?php echo esc_html( $band->get_name() ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_sku">
                <?php esc_html_e( 'SKU', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_merchandise_sku" name="msp_merchandise_sku" value="<?php echo esc_attr( $sku ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_category">
                <?php esc_html_e( 'Category', 'merchmanager' ); ?>
            </label>
            <select id="msp_merchandise_category" name="msp_merchandise_category" class="regular-text">
                <option value=""><?php esc_html_e( 'Select a category...', 'merchmanager' ); ?></option>
                <?php foreach ( $categories as $category_key => $category_label ) : ?>
                    <option value="<?php echo esc_attr( $category_key ); ?>" <?php selected( $category, $category_key ); ?>>
                        <?php echo esc_html( $category_label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_active">
                <?php esc_html_e( 'Active', 'merchmanager' ); ?>
            </label>
            <input type="checkbox" id="msp_merchandise_active" name="msp_merchandise_active" value="1" <?php checked( $active, true ); ?>>
            <span class="description"><?php esc_html_e( 'Inactive merchandise will not be available for sales.', 'merchmanager' ); ?></span>
        </div>
        <?php
    }

    /**
     * Render the pricing meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_pricing_meta_box( $post ) {
        // Get merchandise data
        $merchandise = new Merchmanager_Merchandise( $post->ID );
        $price = $merchandise->get_price();
        $cost = $merchandise->get_cost();
        $stock = $merchandise->get_stock();
        $low_stock_threshold = $merchandise->get_low_stock_threshold();

        // Get currency symbol
        $options = get_option( 'msp_settings', array() );
        $currency = isset( $options['currency'] ) ? $options['currency'] : 'USD';
        $currency_symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => '$',
            'AUD' => '$',
        );
        $currency_symbol = isset( $currency_symbols[ $currency ] ) ? $currency_symbols[ $currency ] : '$';

        // Get default low stock threshold
        $default_threshold = isset( $options['low_stock_threshold'] ) ? $options['low_stock_threshold'] : 5;

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_merchandise_price">
                <?php esc_html_e( 'Price', 'merchmanager' ); ?>
            </label>
            <div class="msp-price-field">
                <span class="msp-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
                <input type="number" id="msp_merchandise_price" name="msp_merchandise_price" value="<?php echo esc_attr( $price ); ?>" class="regular-text" step="0.01" min="0">
            </div>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_cost">
                <?php esc_html_e( 'Cost', 'merchmanager' ); ?>
            </label>
            <div class="msp-price-field">
                <span class="msp-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
                <input type="number" id="msp_merchandise_cost" name="msp_merchandise_cost" value="<?php echo esc_attr( $cost ); ?>" class="regular-text" step="0.01" min="0">
            </div>
            <p class="description"><?php esc_html_e( 'Cost per unit (for profit calculations).', 'merchmanager' ); ?></p>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_stock">
                <?php esc_html_e( 'Stock', 'merchmanager' ); ?>
            </label>
            <input type="number" id="msp_merchandise_stock" name="msp_merchandise_stock" value="<?php echo esc_attr( $stock ); ?>" class="regular-text" min="0">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_low_stock_threshold">
                <?php esc_html_e( 'Low Stock Threshold', 'merchmanager' ); ?>
            </label>
            <input type="number" id="msp_merchandise_low_stock_threshold" name="msp_merchandise_low_stock_threshold" value="<?php echo esc_attr( $low_stock_threshold ? $low_stock_threshold : $default_threshold ); ?>" class="regular-text" min="0">
            <p class="description"><?php esc_html_e( 'When stock reaches this amount, a low stock alert will be triggered.', 'merchmanager' ); ?></p>
        </div>

        <div class="msp-meta-box-field">
            <h3><?php esc_html_e( 'Stock Management', 'merchmanager' ); ?></h3>
            <p>
                <label for="msp_merchandise_stock_adjustment">
                    <?php esc_html_e( 'Adjust Stock', 'merchmanager' ); ?>
                </label>
                <input type="number" id="msp_merchandise_stock_adjustment" name="msp_merchandise_stock_adjustment" value="0" class="regular-text">
                <span class="description"><?php esc_html_e( 'Enter a positive number to add stock or a negative number to remove stock.', 'merchmanager' ); ?></span>
            </p>
            <p>
                <label for="msp_merchandise_stock_notes">
                    <?php esc_html_e( 'Notes', 'merchmanager' ); ?>
                </label>
                <input type="text" id="msp_merchandise_stock_notes" name="msp_merchandise_stock_notes" value="" class="regular-text">
                <span class="description"><?php esc_html_e( 'Optional notes about this stock adjustment.', 'merchmanager' ); ?></span>
            </p>
        </div>
        <?php
    }

    /**
     * Render the attributes meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_attributes_meta_box( $post ) {
        // Get merchandise data
        $merchandise = new Merchmanager_Merchandise( $post->ID );
        $size = $merchandise->get_size();
        $color = $merchandise->get_color();

        // Define sizes
        $sizes = array(
            ''      => __( 'N/A', 'merchmanager' ),
            'xs'    => __( 'XS', 'merchmanager' ),
            's'     => __( 'S', 'merchmanager' ),
            'm'     => __( 'M', 'merchmanager' ),
            'l'     => __( 'L', 'merchmanager' ),
            'xl'    => __( 'XL', 'merchmanager' ),
            '2xl'   => __( '2XL', 'merchmanager' ),
            '3xl'   => __( '3XL', 'merchmanager' ),
            '4xl'   => __( '4XL', 'merchmanager' ),
            '5xl'   => __( '5XL', 'merchmanager' ),
            'one_size' => __( 'One Size', 'merchmanager' ),
        );

        // Define colors
        $colors = array(
            ''          => __( 'N/A', 'merchmanager' ),
            'black'     => __( 'Black', 'merchmanager' ),
            'white'     => __( 'White', 'merchmanager' ),
            'red'       => __( 'Red', 'merchmanager' ),
            'blue'      => __( 'Blue', 'merchmanager' ),
            'green'     => __( 'Green', 'merchmanager' ),
            'yellow'    => __( 'Yellow', 'merchmanager' ),
            'orange'    => __( 'Orange', 'merchmanager' ),
            'purple'    => __( 'Purple', 'merchmanager' ),
            'pink'      => __( 'Pink', 'merchmanager' ),
            'gray'      => __( 'Gray', 'merchmanager' ),
            'brown'     => __( 'Brown', 'merchmanager' ),
            'multi'     => __( 'Multi-color', 'merchmanager' ),
        );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_merchandise_size">
                <?php esc_html_e( 'Size', 'merchmanager' ); ?>
            </label>
            <select id="msp_merchandise_size" name="msp_merchandise_size" class="regular-text">
                <?php foreach ( $sizes as $size_key => $size_label ) : ?>
                    <option value="<?php echo esc_attr( $size_key ); ?>" <?php selected( $size, $size_key ); ?>>
                        <?php echo esc_html( $size_label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_merchandise_color">
                <?php esc_html_e( 'Color', 'merchmanager' ); ?>
            </label>
            <select id="msp_merchandise_color" name="msp_merchandise_color" class="regular-text">
                <?php foreach ( $colors as $color_key => $color_label ) : ?>
                    <option value="<?php echo esc_attr( $color_key ); ?>" <?php selected( $color, $color_key ); ?>>
                        <?php echo esc_html( $color_label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

    /**
     * Render the supplier meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_supplier_meta_box( $post ) {
        // Get merchandise data
        $merchandise = new Merchmanager_Merchandise( $post->ID );
        $supplier = $merchandise->get_supplier();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_merchandise_supplier">
                <?php esc_html_e( 'Supplier Information', 'merchmanager' ); ?>
            </label>
            <textarea id="msp_merchandise_supplier" name="msp_merchandise_supplier" class="large-text" rows="5"><?php echo esc_textarea( $supplier ); ?></textarea>
            <p class="description"><?php esc_html_e( 'Enter supplier name, contact information, and any other relevant details.', 'merchmanager' ); ?></p>
        </div>
        <?php
    }

    /**
     * Render the sales meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_sales_meta_box( $post ) {
        global $wpdb;

        // Get merchandise data
        $merchandise = new Merchmanager_Merchandise( $post->ID );

        // Get sales data (WP 6.2+ %i for table identifier).
        $sales_table = $wpdb->prefix . 'msp_sales';
        $sales = $wpdb->get_results( $wpdb->prepare(
            'SELECT * FROM %i WHERE merchandise_id = %d ORDER BY date DESC LIMIT 10',
            $sales_table,
            $post->ID
        ) );

        // Get total sales
        $total_sales = $wpdb->get_var( $wpdb->prepare(
            'SELECT COUNT(*) FROM %i WHERE merchandise_id = %d',
            $sales_table,
            $post->ID
        ) );

        $total_quantity = $wpdb->get_var( $wpdb->prepare(
            'SELECT SUM(quantity) FROM %i WHERE merchandise_id = %d',
            $sales_table,
            $post->ID
        ) );

        $total_amount = $wpdb->get_var( $wpdb->prepare(
            'SELECT SUM(price * quantity) FROM %i WHERE merchandise_id = %d',
            $sales_table,
            $post->ID
        ) );

        // Get currency symbol
        $options = get_option( 'msp_settings', array() );
        $currency = isset( $options['currency'] ) ? $options['currency'] : 'USD';
        $currency_symbols = array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => '$',
            'AUD' => '$',
        );
        $currency_symbol = isset( $currency_symbols[ $currency ] ) ? $currency_symbols[ $currency ] : '$';

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <h3><?php esc_html_e( 'Sales Summary', 'merchmanager' ); ?></h3>
            <p>
                <strong><?php esc_html_e( 'Total Sales:', 'merchmanager' ); ?></strong>
                <?php echo esc_html( $total_sales ); ?>
            </p>
            <p>
                <strong><?php esc_html_e( 'Total Quantity:', 'merchmanager' ); ?></strong>
                <?php echo esc_html( $total_quantity ); ?>
            </p>
            <p>
                <strong><?php esc_html_e( 'Total Amount:', 'merchmanager' ); ?></strong>
                <?php echo esc_html( $currency_symbol . number_format( $total_amount, 2 ) ); ?>
            </p>
        </div>

        <div class="msp-meta-box-field">
            <h3><?php esc_html_e( 'Recent Sales', 'merchmanager' ); ?></h3>
            <?php if ( empty( $sales ) ) : ?>
                <p><?php esc_html_e( 'No sales recorded for this merchandise.', 'merchmanager' ); ?></p>
            <?php else : ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Show', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Price', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Total', 'merchmanager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $sales as $sale ) : ?>
                            <?php
                            $show = $sale->show_id ? new Merchmanager_Show( $sale->show_id ) : null;
                            ?>
                            <tr>
                                <td>
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $sale->date ) ) ); ?>
                                </td>
                                <td>
                                    <?php if ( $show ) : ?>
                                        <a href="<?php echo esc_url( get_edit_post_link( $sale->show_id ) ); ?>">
                                            <?php echo esc_html( $show->get_name() ); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php esc_html_e( 'N/A', 'merchmanager' ); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $sale->quantity ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $currency_symbol . number_format( $sale->price, 2 ) ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $currency_symbol . number_format( $sale->price * $sale->quantity, 2 ) ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ( $total_sales > 10 ) : ?>
                    <p>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=sales&merchandise_id=' . $post->ID ) ); ?>" class="button"><?php esc_html_e( 'View All Sales', 'merchmanager' ); ?></a>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="msp-meta-box-field">
            <h3><?php esc_html_e( 'Stock History', 'merchmanager' ); ?></h3>
            <?php
            // Get stock log (WP 6.2+ %i for table identifier).
            $stock_log_table = $wpdb->prefix . 'msp_stock_log';
            $stock_log = $wpdb->get_results( $wpdb->prepare(
                'SELECT * FROM %i WHERE merchandise_id = %d ORDER BY created_at DESC LIMIT 10',
                $stock_log_table,
                $post->ID
            ) );
            ?>

            <?php if ( empty( $stock_log ) ) : ?>
                <p><?php esc_html_e( 'No stock history recorded for this merchandise.', 'merchmanager' ); ?></p>
            <?php else : ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Previous Stock', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'New Stock', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Change', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Reason', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'User', 'merchmanager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $stock_log as $log ) : ?>
                            <?php
                            $user = $log->user_id ? get_user_by( 'id', $log->user_id ) : null;
                            $change = $log->new_stock - $log->previous_stock;
                            ?>
                            <tr>
                                <td>
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $log->created_at ) ) ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $log->previous_stock ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $log->new_stock ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $change > 0 ? '+' . $change : $change ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( ucfirst( $log->change_reason ) ); ?>
                                </td>
                                <td>
                                    <?php echo $user ? esc_html( $user->display_name ) : esc_html( __( 'System', 'merchmanager' ) ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @param    WP_Post   $post       The post object.
     */
    public function save_meta_boxes( $post_id, $post ) {
        // Check if nonce is set
        if ( ! isset( $_POST['msp_merchandise_meta_box_nonce'] ) ) {
            return;
        }

        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['msp_merchandise_meta_box_nonce'], 'msp_merchandise_meta_box' ) ) {
            return;
        }

        // Check if autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Get merchandise
        $merchandise = new Merchmanager_Merchandise( $post_id );

        // Save band ID
        if ( isset( $_POST['msp_merchandise_band_id'] ) ) {
            $merchandise->set_band_id( intval( $_POST['msp_merchandise_band_id'] ) );
        }

        // Save SKU
        if ( isset( $_POST['msp_merchandise_sku'] ) ) {
            $merchandise->set_sku( sanitize_text_field( $_POST['msp_merchandise_sku'] ) );
        }

        // Save category
        if ( isset( $_POST['msp_merchandise_category'] ) ) {
            $merchandise->set_category( sanitize_key( $_POST['msp_merchandise_category'] ) );
        }

        // Save active status
        $merchandise->set_active( isset( $_POST['msp_merchandise_active'] ) );

        // Save price
        if ( isset( $_POST['msp_merchandise_price'] ) ) {
            $merchandise->set_price( floatval( $_POST['msp_merchandise_price'] ) );
        }

        // Save cost
        if ( isset( $_POST['msp_merchandise_cost'] ) ) {
            $merchandise->set_cost( floatval( $_POST['msp_merchandise_cost'] ) );
        }

        // Save stock
        if ( isset( $_POST['msp_merchandise_stock'] ) ) {
            $merchandise->set_stock( intval( $_POST['msp_merchandise_stock'] ) );
        }

        // Save low stock threshold
        if ( isset( $_POST['msp_merchandise_low_stock_threshold'] ) ) {
            $merchandise->set_low_stock_threshold( intval( $_POST['msp_merchandise_low_stock_threshold'] ) );
        }

        // Save size
        if ( isset( $_POST['msp_merchandise_size'] ) ) {
            $merchandise->set_size( sanitize_key( $_POST['msp_merchandise_size'] ) );
        }

        // Save color
        if ( isset( $_POST['msp_merchandise_color'] ) ) {
            $merchandise->set_color( sanitize_key( $_POST['msp_merchandise_color'] ) );
        }

        // Save supplier
        if ( isset( $_POST['msp_merchandise_supplier'] ) ) {
            $merchandise->set_supplier( sanitize_textarea_field( $_POST['msp_merchandise_supplier'] ) );
        }

        // Save merchandise
        $merchandise->save();

        // Handle stock adjustment
        if ( isset( $_POST['msp_merchandise_stock_adjustment'] ) && intval( $_POST['msp_merchandise_stock_adjustment'] ) !== 0 ) {
            $adjustment = intval( $_POST['msp_merchandise_stock_adjustment'] );
            $notes = isset( $_POST['msp_merchandise_stock_notes'] ) ? sanitize_text_field( $_POST['msp_merchandise_stock_notes'] ) : '';
            
            $merchandise->update_stock( $adjustment, 'manual', get_current_user_id(), $notes );
        }
    }
}
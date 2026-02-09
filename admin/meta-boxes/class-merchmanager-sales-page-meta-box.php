<?php
/**
 * The sales page meta box class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 */

/**
 * The sales page meta box class.
 *
 * Defines the meta box for the sales page post type.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 * @author     Theuws Consulting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Merchmanager_Sales_Page_Meta_Box {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_msp_sales_page', array( $this, 'save_meta_boxes' ), 10, 2 );
        add_action( 'admin_init', array( $this, 'handle_show_id_parameter' ) );
    }

    /**
     * Handle show ID parameter in URL.
     *
     * @since    1.0.0
     */
    public function handle_show_id_parameter() {
        global $pagenow;

        // Check if we're on the new sales page page with a show_id parameter
        if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'msp_sales_page' === $_GET['post_type'] && isset( $_GET['show_id'] ) ) {
            // Store the show ID in a transient
            set_transient( 'msp_new_sales_page_show_id', intval( $_GET['show_id'] ), 60 * 60 );
        }
    }

    /**
     * Add meta boxes.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'msp_sales_page_details',
            __( 'Sales Page Details', 'merchmanager' ),
            array( $this, 'render_details_meta_box' ),
            'msp_sales_page',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_sales_page_merchandise',
            __( 'Merchandise', 'merchmanager' ),
            array( $this, 'render_merchandise_meta_box' ),
            'msp_sales_page',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_sales_page_access',
            __( 'Access Settings', 'merchmanager' ),
            array( $this, 'render_access_meta_box' ),
            'msp_sales_page',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_sales_page_sales',
            __( 'Sales', 'merchmanager' ),
            array( $this, 'render_sales_meta_box' ),
            'msp_sales_page',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_sales_page_shortcode',
            __( 'Shortcode', 'merchmanager' ),
            array( $this, 'render_shortcode_meta_box' ),
            'msp_sales_page',
            'side',
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
        wp_nonce_field( 'msp_sales_page_meta_box', 'msp_sales_page_meta_box_nonce' );

        // Get sales page data
        $sales_page = new Merchmanager_Sales_Page( $post->ID );
        $show_id = $sales_page->get_show_id();
        $band_id = $sales_page->get_band_id();

        // If this is a new sales page and we have a show ID in the transient, use it
        if ( empty( $show_id ) && 'auto-draft' === $post->post_status ) {
            $show_id = get_transient( 'msp_new_sales_page_show_id' );
            if ( $show_id ) {
                delete_transient( 'msp_new_sales_page_show_id' );
                
                // Get band ID from show
                $show = new Merchmanager_Show( $show_id );
                $band = $show->get_band();
                if ( $band ) {
                    $band_id = $band->get_id();
                }
            }
        }

        // Get shows
        $shows = Merchmanager_Show::get_all( array(
            'orderby'  => 'meta_value',
            'meta_key' => '_msp_show_date',
            'order'    => 'DESC',
        ) );

        // Get bands
        $bands = Merchmanager_Band::get_all();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_sales_page_show_id">
                <?php esc_html_e( 'Show', 'merchmanager' ); ?>
            </label>
            <select id="msp_sales_page_show_id" name="msp_sales_page_show_id" class="regular-text">
                <option value=""><?php esc_html_e( 'Select a show...', 'merchmanager' ); ?></option>
                <?php foreach ( $shows as $show ) : ?>
                    <option value="<?php echo esc_attr( $show->get_id() ); ?>" <?php selected( $show_id, $show->get_id() ); ?> data-band-id="<?php echo esc_attr( $show->get_band() ? $show->get_band()->get_id() : '' ); ?>">
                        <?php echo esc_html( $show->get_name() ); ?> - 
                        <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $show->get_date() ) ) ); ?>
                        <?php
                        $band = $show->get_band();
                        if ( $band ) {
                            echo ' (' . esc_html( $band->get_name() ) . ')';
                        }
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php esc_html_e( 'Select the show this sales page is for.', 'merchmanager' ); ?></p>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_sales_page_band_id">
                <?php esc_html_e( 'Band', 'merchmanager' ); ?>
            </label>
            <select id="msp_sales_page_band_id" name="msp_sales_page_band_id" class="regular-text">
                <option value=""><?php esc_html_e( 'Select a band...', 'merchmanager' ); ?></option>
                <?php foreach ( $bands as $band ) : ?>
                    <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $band_id, $band->get_id() ); ?>>
                        <?php echo esc_html( $band->get_name() ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php esc_html_e( 'Select the band this sales page is for.', 'merchmanager' ); ?></p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Update band ID when show is selected
            $('#msp_sales_page_show_id').on('change', function() {
                var bandId = $(this).find('option:selected').data('band-id');
                if (bandId) {
                    $('#msp_sales_page_band_id').val(bandId);
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Render the merchandise meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_merchandise_meta_box( $post ) {
        // Get sales page data
        $sales_page = new Merchmanager_Sales_Page( $post->ID );
        $band_id = $sales_page->get_band_id();
        $merchandise_ids = $sales_page->get_merchandise();

        // Get merchandise
        $merchandise_items = array();
        if ( $band_id ) {
            $args = array(
                'meta_query' => array(
                    array(
                        'key'   => '_msp_merchandise_band_id',
                        'value' => $band_id,
                    ),
                    array(
                        'key'   => '_msp_merchandise_active',
                        'value' => '1',
                    ),
                ),
            );
            $merchandise_items = Merchmanager_Merchandise::get_all( $args );
        }

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
            <p class="description">
                <?php esc_html_e( 'Select the merchandise items to include in this sales page.', 'merchmanager' ); ?>
            </p>
        </div>

        <?php if ( empty( $merchandise_items ) ) : ?>
            <div class="msp-meta-box-field">
                <p>
                    <?php if ( $band_id ) : ?>
                        <?php esc_html_e( 'No merchandise items found for this band.', 'merchmanager' ); ?>
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_merchandise&band_id=' . $band_id ) ); ?>" class="button"><?php esc_html_e( 'Add Merchandise', 'merchmanager' ); ?></a>
                    <?php else : ?>
                        <?php esc_html_e( 'Please select a band first.', 'merchmanager' ); ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php else : ?>
            <div class="msp-meta-box-field">
                <table class="widefat">
                    <thead>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" id="msp-select-all-merchandise">
                            </th>
                            <th><?php esc_html_e( 'Name', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'SKU', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Price', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Stock', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Category', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Size', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Color', 'merchmanager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $merchandise_items as $merchandise ) : ?>
                            <tr>
                                <td class="check-column">
                                    <input type="checkbox" name="msp_sales_page_merchandise[]" value="<?php echo esc_attr( $merchandise->get_id() ); ?>" <?php checked( in_array( $merchandise->get_id(), $merchandise_ids ) ); ?>>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( get_edit_post_link( $merchandise->get_id() ) ); ?>">
                                        <?php echo esc_html( $merchandise->get_name() ); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo esc_html( $merchandise->get_sku() ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $currency_symbol . number_format( $merchandise->get_price(), 2 ) ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $merchandise->get_stock() ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( ucfirst( $merchandise->get_category() ) ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( strtoupper( $merchandise->get_size() ) ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( ucfirst( $merchandise->get_color() ) ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
            jQuery(document).ready(function($) {
                // Select all merchandise
                $('#msp-select-all-merchandise').on('change', function() {
                    $('input[name="msp_sales_page_merchandise[]"]').prop('checked', $(this).prop('checked'));
                });
            });
            </script>
        <?php endif; ?>
        <?php
    }

    /**
     * Render the access meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_access_meta_box( $post ) {
        // Get sales page data
        $sales_page = new Merchmanager_Sales_Page( $post->ID );
        $access_code = $sales_page->get_access_code();
        $status = $sales_page->get_status();
        $expiry_date = $sales_page->get_expiry_date();

        // Get default expiry date
        $options = get_option( 'msp_settings', array() );
        $default_expiry_days = isset( $options['sales_page_expiry'] ) ? $options['sales_page_expiry'] : 7;
        $default_expiry_date = gmdate( 'Y-m-d\TH:i', strtotime( '+' . $default_expiry_days . ' days' ) );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_sales_page_status">
                <?php esc_html_e( 'Status', 'merchmanager' ); ?>
            </label>
            <select id="msp_sales_page_status" name="msp_sales_page_status" class="regular-text">
                <option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'merchmanager' ); ?></option>
                <option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'merchmanager' ); ?></option>
            </select>
            <p class="description"><?php esc_html_e( 'Inactive sales pages cannot be accessed.', 'merchmanager' ); ?></p>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_sales_page_access_code">
                <?php esc_html_e( 'Access Code', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_sales_page_access_code" name="msp_sales_page_access_code" value="<?php echo esc_attr( $access_code ); ?>" class="regular-text">
            <button type="button" id="msp-generate-access-code" class="button"><?php esc_html_e( 'Generate', 'merchmanager' ); ?></button>
            <p class="description"><?php esc_html_e( 'If set, users will need to enter this code to access the sales page. Leave blank for public access.', 'merchmanager' ); ?></p>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_sales_page_expiry_date">
                <?php esc_html_e( 'Expiry Date', 'merchmanager' ); ?>
            </label>
            <input type="datetime-local" id="msp_sales_page_expiry_date" name="msp_sales_page_expiry_date" value="<?php echo esc_attr( $expiry_date ? gmdate( 'Y-m-d\TH:i', strtotime( $expiry_date ) ) : $default_expiry_date ); ?>" class="regular-text">
            <p class="description"><?php esc_html_e( 'The sales page will become inactive after this date.', 'merchmanager' ); ?></p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Generate access code
            $('#msp-generate-access-code').on('click', function() {
                var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                var code = '';
                for (var i = 0; i < 6; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                $('#msp_sales_page_access_code').val(code);
            });
        });
        </script>
        <?php
    }

    /**
     * Render the sales meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_sales_meta_box( $post ) {
        // Get sales page data
        $sales_page = new Merchmanager_Sales_Page( $post->ID );
        $sales = $sales_page->get_sales();
        $total_amount = $sales_page->get_total_sales_amount();

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
                <?php echo esc_html( count( $sales ) ); ?>
            </p>
            <p>
                <strong><?php esc_html_e( 'Total Amount:', 'merchmanager' ); ?></strong>
                <?php echo esc_html( $currency_symbol . number_format( $total_amount, 2 ) ); ?>
            </p>
        </div>

        <div class="msp-meta-box-field">
            <h3><?php esc_html_e( 'Sales', 'merchmanager' ); ?></h3>
            <?php if ( empty( $sales ) ) : ?>
                <p><?php esc_html_e( 'No sales recorded for this sales page.', 'merchmanager' ); ?></p>
            <?php else : ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Date', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Merchandise', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Price', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Total', 'merchmanager' ); ?></th>
                            <th><?php esc_html_e( 'Payment Type', 'merchmanager' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $sales as $sale ) : ?>
                            <?php
                            $merchandise = new Merchmanager_Merchandise( $sale->merchandise_id );
                            ?>
                            <tr>
                                <td>
                                    <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $sale->date ) ) ); ?>
                                </td>
                                <td>
                                    <?php echo esc_html( $merchandise->get_name() ); ?>
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
                                <td>
                                    <?php echo esc_html( ucfirst( $sale->payment_type ) ); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="msp-meta-box-field">
            <p>
                <a href="<?php echo esc_url( $sales_page->get_url() ); ?>" class="button button-primary" target="_blank"><?php esc_html_e( 'View Sales Page', 'merchmanager' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=sales&sales_page_id=' . $post->ID ) ); ?>" class="button"><?php esc_html_e( 'View Sales Report', 'merchmanager' ); ?></a>
            </p>
        </div>
        <?php
    }

    /**
     * Render the shortcode meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_shortcode_meta_box( $post ) {
        // Get sales page data
        $sales_page = new Merchmanager_Sales_Page( $post->ID );
        $shortcode = $sales_page->get_shortcode();
        $url = $sales_page->get_url();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <p>
                <strong><?php esc_html_e( 'Shortcode:', 'merchmanager' ); ?></strong>
                <code><?php echo esc_html( $shortcode ); ?></code>
            </p>
            <p class="description"><?php esc_html_e( 'Use this shortcode to display the sales page on any post or page.', 'merchmanager' ); ?></p>
        </div>

        <div class="msp-meta-box-field">
            <p>
                <strong><?php esc_html_e( 'URL:', 'merchmanager' ); ?></strong><br>
                <a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( $url ); ?></a>
            </p>
            <p class="description"><?php esc_html_e( 'Direct link to the sales page.', 'merchmanager' ); ?></p>
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
        if ( ! isset( $_POST['msp_sales_page_meta_box_nonce'] ) ) {
            return;
        }

        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['msp_sales_page_meta_box_nonce'], 'msp_sales_page_meta_box' ) ) {
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

        // Get sales page
        $sales_page = new Merchmanager_Sales_Page( $post_id );

        // Save show ID
        if ( isset( $_POST['msp_sales_page_show_id'] ) ) {
            $sales_page->set_show_id( intval( $_POST['msp_sales_page_show_id'] ) );
        }

        // Save band ID
        if ( isset( $_POST['msp_sales_page_band_id'] ) ) {
            $sales_page->set_band_id( intval( $_POST['msp_sales_page_band_id'] ) );
        }

        // Save merchandise
        if ( isset( $_POST['msp_sales_page_merchandise'] ) && is_array( $_POST['msp_sales_page_merchandise'] ) ) {
            $merchandise_ids = array_map( 'intval', $_POST['msp_sales_page_merchandise'] );
            $sales_page->set_merchandise( $merchandise_ids );
        } else {
            $sales_page->set_merchandise( array() );
        }

        // Save status
        if ( isset( $_POST['msp_sales_page_status'] ) ) {
            $sales_page->set_status( sanitize_key( $_POST['msp_sales_page_status'] ) );
        }

        // Save access code
        if ( isset( $_POST['msp_sales_page_access_code'] ) ) {
            $sales_page->set_access_code( sanitize_text_field( $_POST['msp_sales_page_access_code'] ) );
        }

        // Save expiry date
        if ( isset( $_POST['msp_sales_page_expiry_date'] ) && ! empty( $_POST['msp_sales_page_expiry_date'] ) ) {
            $sales_page->set_expiry_date( sanitize_text_field( $_POST['msp_sales_page_expiry_date'] ) );
        }

        // Save sales page
        $sales_page->save();
    }
}
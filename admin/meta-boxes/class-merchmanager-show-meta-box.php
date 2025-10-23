<?php
/**
 * The show meta box class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 */

/**
 * The show meta box class.
 *
 * Defines the meta box for the show post type.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Show_Meta_Box {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_msp_show', array( $this, 'save_meta_boxes' ), 10, 2 );
        add_action( 'admin_init', array( $this, 'handle_tour_id_parameter' ) );
    }

    /**
     * Handle tour ID parameter in URL.
     *
     * @since    1.0.0
     */
    public function handle_tour_id_parameter() {
        global $pagenow;

        // Check if we're on the new show page with a tour_id parameter
        if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'msp_show' === $_GET['post_type'] && isset( $_GET['tour_id'] ) ) {
            // Store the tour ID in a transient
            set_transient( 'msp_new_show_tour_id', intval( $_GET['tour_id'] ), 60 * 60 );
        }
    }

    /**
     * Add meta boxes.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'msp_show_details',
            __( 'Show Details', 'merchmanager' ),
            array( $this, 'render_details_meta_box' ),
            'msp_show',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_show_venue',
            __( 'Venue Information', 'merchmanager' ),
            array( $this, 'render_venue_meta_box' ),
            'msp_show',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_show_sales',
            __( 'Sales', 'merchmanager' ),
            array( $this, 'render_sales_meta_box' ),
            'msp_show',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_show_sales_page',
            __( 'Sales Page', 'merchmanager' ),
            array( $this, 'render_sales_page_meta_box' ),
            'msp_show',
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
        wp_nonce_field( 'msp_show_meta_box', 'msp_show_meta_box_nonce' );

        // Get show data
        $show = new Merchmanager_Show( $post->ID );
        $tour_id = $show->get_tour_id();
        $date = $show->get_date();
        $notes = $show->get_notes();

        // If this is a new show and we have a tour ID in the transient, use it
        if ( empty( $tour_id ) && 'auto-draft' === $post->post_status ) {
            $tour_id = get_transient( 'msp_new_show_tour_id' );
            if ( $tour_id ) {
                delete_transient( 'msp_new_show_tour_id' );
            }
        }

        // Get tours
        $tours = Merchmanager_Tour::get_all();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_show_tour_id">
                <?php _e( 'Tour', 'merchmanager' ); ?>
            </label>
            <select id="msp_show_tour_id" name="msp_show_tour_id" class="regular-text">
                <option value=""><?php _e( 'Select a tour...', 'merchmanager' ); ?></option>
                <?php foreach ( $tours as $tour ) : ?>
                    <option value="<?php echo esc_attr( $tour->get_id() ); ?>" <?php selected( $tour_id, $tour->get_id() ); ?>>
                        <?php echo esc_html( $tour->get_name() ); ?>
                        <?php
                        $band = $tour->get_band();
                        if ( $band ) {
                            echo ' (' . esc_html( $band->get_name() ) . ')';
                        }
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_date">
                <?php _e( 'Date and Time', 'merchmanager' ); ?>
            </label>
            <input type="datetime-local" id="msp_show_date" name="msp_show_date" value="<?php echo esc_attr( $date ? date( 'Y-m-d\TH:i', strtotime( $date ) ) : '' ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_notes">
                <?php _e( 'Notes', 'merchmanager' ); ?>
            </label>
            <textarea id="msp_show_notes" name="msp_show_notes" class="large-text" rows="5"><?php echo esc_textarea( $notes ); ?></textarea>
        </div>
        <?php
    }

    /**
     * Render the venue meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_venue_meta_box( $post ) {
        // Get show data
        $show = new Merchmanager_Show( $post->ID );
        $venue_name = $show->get_venue_name();
        $venue_address = $show->get_venue_address();
        $venue_city = $show->get_venue_city();
        $venue_state = $show->get_venue_state();
        $venue_country = $show->get_venue_country();
        $venue_postal_code = $show->get_venue_postal_code();
        $venue_contact = $show->get_venue_contact();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_show_venue_name">
                <?php _e( 'Venue Name', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_show_venue_name" name="msp_show_venue_name" value="<?php echo esc_attr( $venue_name ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_venue_address">
                <?php _e( 'Address', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_show_venue_address" name="msp_show_venue_address" value="<?php echo esc_attr( $venue_address ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_venue_city">
                <?php _e( 'City', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_show_venue_city" name="msp_show_venue_city" value="<?php echo esc_attr( $venue_city ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_venue_state">
                <?php _e( 'State/Province', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_show_venue_state" name="msp_show_venue_state" value="<?php echo esc_attr( $venue_state ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_venue_country">
                <?php _e( 'Country', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_show_venue_country" name="msp_show_venue_country" value="<?php echo esc_attr( $venue_country ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_venue_postal_code">
                <?php _e( 'Postal Code', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_show_venue_postal_code" name="msp_show_venue_postal_code" value="<?php echo esc_attr( $venue_postal_code ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_show_venue_contact">
                <?php _e( 'Venue Contact', 'merchmanager' ); ?>
            </label>
            <textarea id="msp_show_venue_contact" name="msp_show_venue_contact" class="large-text" rows="3"><?php echo esc_textarea( $venue_contact ); ?></textarea>
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
        // Get show data
        $show = new Merchmanager_Show( $post->ID );
        $sales = $show->get_sales();
        $total_amount = $show->get_total_sales_amount();

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
            <h3><?php _e( 'Sales Summary', 'merchmanager' ); ?></h3>
            <p>
                <strong><?php _e( 'Total Sales:', 'merchmanager' ); ?></strong>
                <?php echo esc_html( count( $sales ) ); ?>
            </p>
            <p>
                <strong><?php _e( 'Total Amount:', 'merchmanager' ); ?></strong>
                <?php echo esc_html( $currency_symbol . number_format( $total_amount, 2 ) ); ?>
            </p>
        </div>

        <div class="msp-meta-box-field">
            <h3><?php _e( 'Sales', 'merchmanager' ); ?></h3>
            <?php if ( empty( $sales ) ) : ?>
                <p><?php _e( 'No sales recorded for this show.', 'merchmanager' ); ?></p>
            <?php else : ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e( 'Date', 'merchmanager' ); ?></th>
                            <th><?php _e( 'Merchandise', 'merchmanager' ); ?></th>
                            <th><?php _e( 'Quantity', 'merchmanager' ); ?></th>
                            <th><?php _e( 'Price', 'merchmanager' ); ?></th>
                            <th><?php _e( 'Total', 'merchmanager' ); ?></th>
                            <th><?php _e( 'Payment Type', 'merchmanager' ); ?></th>
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
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-sales&show_id=' . $post->ID ) ); ?>" class="button button-primary"><?php _e( 'Record Sale', 'merchmanager' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports&tab=sales&show_id=' . $post->ID ) ); ?>" class="button"><?php _e( 'View Sales Report', 'merchmanager' ); ?></a>
            </p>
        </div>
        <?php
    }

    /**
     * Render the sales page meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_sales_page_meta_box( $post ) {
        // Get show data
        $show = new Merchmanager_Show( $post->ID );
        $sales_page = $show->get_sales_page();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <?php if ( $sales_page ) : ?>
                <h3><?php _e( 'Sales Page', 'merchmanager' ); ?></h3>
                <p>
                    <strong><?php _e( 'Title:', 'merchmanager' ); ?></strong>
                    <?php echo esc_html( $sales_page->get_name() ); ?>
                </p>
                <p>
                    <strong><?php _e( 'Status:', 'merchmanager' ); ?></strong>
                    <?php echo esc_html( ucfirst( $sales_page->get_status() ) ); ?>
                </p>
                <p>
                    <strong><?php _e( 'URL:', 'merchmanager' ); ?></strong>
                    <a href="<?php echo esc_url( $sales_page->get_url() ); ?>" target="_blank"><?php echo esc_html( $sales_page->get_url() ); ?></a>
                </p>
                <p>
                    <strong><?php _e( 'Shortcode:', 'merchmanager' ); ?></strong>
                    <code><?php echo esc_html( $sales_page->get_shortcode() ); ?></code>
                </p>
                <?php if ( $sales_page->get_access_code() ) : ?>
                    <p>
                        <strong><?php _e( 'Access Code:', 'merchmanager' ); ?></strong>
                        <?php echo esc_html( $sales_page->get_access_code() ); ?>
                    </p>
                <?php endif; ?>
                <?php if ( $sales_page->get_expiry_date() ) : ?>
                    <p>
                        <strong><?php _e( 'Expiry Date:', 'merchmanager' ); ?></strong>
                        <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $sales_page->get_expiry_date() ) ) ); ?>
                    </p>
                <?php endif; ?>
                <p>
                    <a href="<?php echo esc_url( get_edit_post_link( $sales_page->get_id() ) ); ?>" class="button"><?php _e( 'Edit Sales Page', 'merchmanager' ); ?></a>
                </p>
            <?php else : ?>
                <p><?php _e( 'No sales page has been created for this show.', 'merchmanager' ); ?></p>
                <p>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_sales_page&show_id=' . $post->ID ) ); ?>" class="button button-primary"><?php _e( 'Create Sales Page', 'merchmanager' ); ?></a>
                </p>
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
        if ( ! isset( $_POST['msp_show_meta_box_nonce'] ) ) {
            return;
        }

        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['msp_show_meta_box_nonce'], 'msp_show_meta_box' ) ) {
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

        // Get show
        $show = new Merchmanager_Show( $post_id );

        // Save tour ID
        if ( isset( $_POST['msp_show_tour_id'] ) ) {
            $show->set_tour_id( intval( $_POST['msp_show_tour_id'] ) );
        }

        // Save date
        if ( isset( $_POST['msp_show_date'] ) && ! empty( $_POST['msp_show_date'] ) ) {
            $show->set_date( sanitize_text_field( $_POST['msp_show_date'] ) );
        }

        // Save venue information
        if ( isset( $_POST['msp_show_venue_name'] ) ) {
            $show->set_venue_name( sanitize_text_field( $_POST['msp_show_venue_name'] ) );
        }

        if ( isset( $_POST['msp_show_venue_address'] ) ) {
            $show->set_venue_address( sanitize_text_field( $_POST['msp_show_venue_address'] ) );
        }

        if ( isset( $_POST['msp_show_venue_city'] ) ) {
            $show->set_venue_city( sanitize_text_field( $_POST['msp_show_venue_city'] ) );
        }

        if ( isset( $_POST['msp_show_venue_state'] ) ) {
            $show->set_venue_state( sanitize_text_field( $_POST['msp_show_venue_state'] ) );
        }

        if ( isset( $_POST['msp_show_venue_country'] ) ) {
            $show->set_venue_country( sanitize_text_field( $_POST['msp_show_venue_country'] ) );
        }

        if ( isset( $_POST['msp_show_venue_postal_code'] ) ) {
            $show->set_venue_postal_code( sanitize_text_field( $_POST['msp_show_venue_postal_code'] ) );
        }

        if ( isset( $_POST['msp_show_venue_contact'] ) ) {
            $show->set_venue_contact( sanitize_textarea_field( $_POST['msp_show_venue_contact'] ) );
        }

        // Save notes
        if ( isset( $_POST['msp_show_notes'] ) ) {
            $show->set_notes( sanitize_textarea_field( $_POST['msp_show_notes'] ) );
        }

        // Save show
        $show->save();
    }
}
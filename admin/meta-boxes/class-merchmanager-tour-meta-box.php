<?php
/**
 * The tour meta box class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 */

/**
 * The tour meta box class.
 *
 * Defines the meta box for the tour post type.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 * @author     Theuws Consulting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Merchmanager_Tour_Meta_Box {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_msp_tour', array( $this, 'save_meta_boxes' ), 10, 2 );
        add_action( 'admin_notices', array( $this, 'show_import_notice' ) );
    }

    /**
     * Show admin notice after CSV import.
     *
     * @since    1.0.3
     */
    public function show_import_notice() {
        $screen = get_current_screen();
        if ( ! $screen || 'msp_tour' !== $screen->post_type || 'post' !== $screen->base ) {
            return;
        }
        if ( isset( $_GET['msp_import_error'] ) && 'no_file' === sanitize_text_field( wp_unslash( $_GET['msp_import_error'] ) ) ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Please select a CSV file to import.', 'merchmanager' ) . '</p></div>';
        }
        if ( isset( $_GET['msp_import_result'] ) && isset( $_GET['msp_import_skipped'] ) ) {
            $imported = absint( $_GET['msp_import_result'] );
            $skipped = absint( $_GET['msp_import_skipped'] );
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf(
                /* translators: 1: number of shows imported, 2: number of shows skipped */
                __( 'Import completed: %1$d shows imported, %2$d skipped.', 'merchmanager' ),
                $imported,
                $skipped
            ) ) . '</p></div>';
        }
    }

    /**
     * Add meta boxes.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'msp_tour_details',
            __( 'Tour Details', 'merchmanager' ),
            array( $this, 'render_details_meta_box' ),
            'msp_tour',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_tour_shows',
            __( 'Shows', 'merchmanager' ),
            array( $this, 'render_shows_meta_box' ),
            'msp_tour',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_tour_import_export',
            __( 'Import/Export Shows', 'merchmanager' ),
            array( $this, 'render_import_export_meta_box' ),
            'msp_tour',
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
        wp_nonce_field( 'msp_tour_meta_box', 'msp_tour_meta_box_nonce' );

        // Get tour data
        $tour = new Merchmanager_Tour( $post->ID );
        $band_id = $tour->get_band_id();
        $start_date = $tour->get_start_date();
        $end_date = $tour->get_end_date();
        $status = $tour->get_status();
        $notes = $tour->get_notes();

        // Get bands
        $bands = Merchmanager_Band::get_all();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_tour_band_id">
                <?php esc_html_e( 'Band', 'merchmanager' ); ?>
            </label>
            <select id="msp_tour_band_id" name="msp_tour_band_id" class="regular-text">
                <option value=""><?php esc_html_e( 'Select a band...', 'merchmanager' ); ?></option>
                <?php foreach ( $bands as $band ) : ?>
                    <option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $band_id, $band->get_id() ); ?>>
                        <?php echo esc_html( $band->get_name() ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_tour_start_date">
                <?php esc_html_e( 'Start Date', 'merchmanager' ); ?>
            </label>
            <input type="date" id="msp_tour_start_date" name="msp_tour_start_date" value="<?php echo esc_attr( $start_date ? gmdate( 'Y-m-d', strtotime( $start_date ) ) : '' ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_tour_end_date">
                <?php esc_html_e( 'End Date', 'merchmanager' ); ?>
            </label>
            <input type="date" id="msp_tour_end_date" name="msp_tour_end_date" value="<?php echo esc_attr( $end_date ? gmdate( 'Y-m-d', strtotime( $end_date ) ) : '' ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_tour_status">
                <?php esc_html_e( 'Status', 'merchmanager' ); ?>
            </label>
            <select id="msp_tour_status" name="msp_tour_status" class="regular-text">
                <option value="upcoming" <?php selected( $status, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming', 'merchmanager' ); ?></option>
                <option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'merchmanager' ); ?></option>
                <option value="completed" <?php selected( $status, 'completed' ); ?>><?php esc_html_e( 'Completed', 'merchmanager' ); ?></option>
                <option value="cancelled" <?php selected( $status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'merchmanager' ); ?></option>
            </select>
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_tour_notes">
                <?php esc_html_e( 'Notes', 'merchmanager' ); ?>
            </label>
            <textarea id="msp_tour_notes" name="msp_tour_notes" class="large-text" rows="5"><?php echo esc_textarea( $notes ); ?></textarea>
        </div>
        <?php
    }

    /**
     * Render the shows meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_shows_meta_box( $post ) {
        // Get tour data
        $tour = new Merchmanager_Tour( $post->ID );
        $shows = $tour->get_shows();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <p class="description">
                <?php esc_html_e( 'Manage shows for this tour.', 'merchmanager' ); ?>
            </p>
        </div>

        <table class="widefat" id="msp-tour-shows-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Date', 'merchmanager' ); ?></th>
                    <th><?php esc_html_e( 'Venue', 'merchmanager' ); ?></th>
                    <th><?php esc_html_e( 'City', 'merchmanager' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'merchmanager' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $shows ) ) : ?>
                    <tr class="msp-no-shows">
                        <td colspan="4"><?php esc_html_e( 'No shows added to this tour.', 'merchmanager' ); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $shows as $show ) : ?>
                        <tr>
                            <td>
                                <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $show->get_date() ) ) ); ?>
                            </td>
                            <td>
                                <?php echo esc_html( $show->get_venue_name() ); ?>
                            </td>
                            <td>
                                <?php echo esc_html( $show->get_venue_city() ); ?>
                                <?php if ( $show->get_venue_state() ) : ?>
                                    , <?php echo esc_html( $show->get_venue_state() ); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url( get_edit_post_link( $show->get_id() ) ); ?>" class="button"><?php esc_html_e( 'Edit', 'merchmanager' ); ?></a>
                                <a href="<?php echo esc_url( get_delete_post_link( $show->get_id() ) ); ?>" class="button"><?php esc_html_e( 'Delete', 'merchmanager' ); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_show&tour_id=' . $post->ID ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add Show', 'merchmanager' ); ?></a>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php
    }

    /**
     * Render the import/export meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_import_export_meta_box( $post ) {
        // Get tour data
        $tour = new Merchmanager_Tour( $post->ID );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <p class="description">
                <?php esc_html_e( 'Import shows from a CSV file or export shows to a CSV file.', 'merchmanager' ); ?>
            </p>
        </div>

        <div class="msp-meta-box-field">
            <h4><?php esc_html_e( 'Import Shows', 'merchmanager' ); ?></h4>
            <p class="description">
                <?php esc_html_e( 'Upload a CSV file with show data. The CSV file should have the following columns:', 'merchmanager' ); ?>
            </p>
            <ul class="msp-csv-columns">
                <li><strong><?php esc_html_e( 'Name', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The name of the show (required)', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Date', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The date of the show in YYYY-MM-DD format (required)', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue Name', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The name of the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue Address', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The address of the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue City', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The city of the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue State', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The state/province of the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue Country', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The country of the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue Postal Code', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'The postal code of the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Venue Contact', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'Contact information for the venue', 'merchmanager' ); ?></li>
                <li><strong><?php esc_html_e( 'Notes', 'merchmanager' ); ?></strong> - <?php esc_html_e( 'Additional notes for the show', 'merchmanager' ); ?></li>
            </ul>

            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="msp_import_shows">
                <input type="hidden" name="tour_id" value="<?php echo esc_attr( $post->ID ); ?>">
                <?php wp_nonce_field( 'msp_import_shows', 'msp_import_shows_nonce' ); ?>

                <div class="msp-import-mapping">
                    <h4><?php esc_html_e( 'Column Mapping', 'merchmanager' ); ?></h4>
                    <p class="description">
                        <?php esc_html_e( 'Map the columns in your CSV file to the fields in the plugin.', 'merchmanager' ); ?>
                    </p>

                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Field', 'merchmanager' ); ?></th>
                                <th><?php esc_html_e( 'CSV Column', 'merchmanager' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php esc_html_e( 'Name', 'merchmanager' ); ?> *</td>
                                <td>
                                    <input type="number" name="msp_import_mapping[name]" value="0" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Date', 'merchmanager' ); ?> *</td>
                                <td>
                                    <input type="number" name="msp_import_mapping[date]" value="1" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue Name', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_name]" value="2" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue Address', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_address]" value="3" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue City', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_city]" value="4" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue State', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_state]" value="5" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue Country', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_country]" value="6" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue Postal Code', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_postal_code]" value="7" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Venue Contact', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[venue_contact]" value="8" min="0" class="small-text">
                                </td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e( 'Notes', 'merchmanager' ); ?></td>
                                <td>
                                    <input type="number" name="msp_import_mapping[notes]" value="9" min="0" class="small-text">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="description">
                        <?php esc_html_e( '* Required fields', 'merchmanager' ); ?>
                    </p>
                </div>

                <div class="msp-import-file">
                    <label for="msp_import_file"><?php esc_html_e( 'CSV File', 'merchmanager' ); ?></label>
                    <input type="file" id="msp_import_file" name="msp_import_file" accept=".csv">
                </div>

                <p>
                    <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Import Shows', 'merchmanager' ); ?>">
                </p>
            </form>
        </div>

        <div class="msp-meta-box-field">
            <h4><?php esc_html_e( 'Export Shows', 'merchmanager' ); ?></h4>
            <p class="description">
                <?php esc_html_e( 'Export all shows for this tour to a CSV file.', 'merchmanager' ); ?>
            </p>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="msp_export_shows">
                <input type="hidden" name="tour_id" value="<?php echo esc_attr( $post->ID ); ?>">
                <?php wp_nonce_field( 'msp_export_shows', 'msp_export_shows_nonce' ); ?>

                <p>
                    <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Export Shows', 'merchmanager' ); ?>">
                </p>
            </form>
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
        if ( ! isset( $_POST['msp_tour_meta_box_nonce'] ) ) {
            return;
        }

        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['msp_tour_meta_box_nonce'], 'msp_tour_meta_box' ) ) {
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

        // Get tour
        $tour = new Merchmanager_Tour( $post_id );

        // Save band ID
        if ( isset( $_POST['msp_tour_band_id'] ) ) {
            $tour->set_band_id( intval( $_POST['msp_tour_band_id'] ) );
        }

        // Save start date
        if ( isset( $_POST['msp_tour_start_date'] ) && ! empty( $_POST['msp_tour_start_date'] ) ) {
            $tour->set_start_date( sanitize_text_field( $_POST['msp_tour_start_date'] ) );
        }

        // Save end date
        if ( isset( $_POST['msp_tour_end_date'] ) && ! empty( $_POST['msp_tour_end_date'] ) ) {
            $tour->set_end_date( sanitize_text_field( $_POST['msp_tour_end_date'] ) );
        }

        // Save status
        if ( isset( $_POST['msp_tour_status'] ) ) {
            $tour->set_status( sanitize_key( $_POST['msp_tour_status'] ) );
        }

        // Save notes
        if ( isset( $_POST['msp_tour_notes'] ) ) {
            $tour->set_notes( sanitize_textarea_field( $_POST['msp_tour_notes'] ) );
        }

        // Save tour
        $tour->save();
    }
}
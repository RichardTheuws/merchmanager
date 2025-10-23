<?php
/**
 * The band meta box class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 */

/**
 * The band meta box class.
 *
 * Defines the meta box for the band post type.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Band_Meta_Box {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_msp_band', array( $this, 'save_meta_boxes' ), 10, 2 );
    }

    /**
     * Add meta boxes.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'msp_band_details',
            __( 'Band Details', 'merchmanager' ),
            array( $this, 'render_details_meta_box' ),
            'msp_band',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_band_contact',
            __( 'Contact Information', 'merchmanager' ),
            array( $this, 'render_contact_meta_box' ),
            'msp_band',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_band_social_media',
            __( 'Social Media', 'merchmanager' ),
            array( $this, 'render_social_media_meta_box' ),
            'msp_band',
            'normal',
            'high'
        );

        add_meta_box(
            'msp_band_users',
            __( 'Associated Users', 'merchmanager' ),
            array( $this, 'render_users_meta_box' ),
            'msp_band',
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
        wp_nonce_field( 'msp_band_meta_box', 'msp_band_meta_box_nonce' );

        // Get band data
        $band = new Merchmanager_Band( $post->ID );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <p class="description">
                <?php _e( 'Enter the basic details for this band.', 'merchmanager' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Render the contact meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_contact_meta_box( $post ) {
        // Get band data
        $band = new Merchmanager_Band( $post->ID );
        $contact_name = $band->get_contact_name();
        $contact_email = $band->get_contact_email();
        $contact_phone = $band->get_contact_phone();
        $website = $band->get_website();

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <label for="msp_band_contact_name">
                <?php _e( 'Contact Name', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_band_contact_name" name="msp_band_contact_name" value="<?php echo esc_attr( $contact_name ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_band_contact_email">
                <?php _e( 'Contact Email', 'merchmanager' ); ?>
            </label>
            <input type="email" id="msp_band_contact_email" name="msp_band_contact_email" value="<?php echo esc_attr( $contact_email ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_band_contact_phone">
                <?php _e( 'Contact Phone', 'merchmanager' ); ?>
            </label>
            <input type="text" id="msp_band_contact_phone" name="msp_band_contact_phone" value="<?php echo esc_attr( $contact_phone ); ?>" class="regular-text">
        </div>

        <div class="msp-meta-box-field">
            <label for="msp_band_website">
                <?php _e( 'Website', 'merchmanager' ); ?>
            </label>
            <input type="url" id="msp_band_website" name="msp_band_website" value="<?php echo esc_attr( $website ); ?>" class="regular-text">
        </div>
        <?php
    }

    /**
     * Render the social media meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_social_media_meta_box( $post ) {
        // Get band data
        $band = new Merchmanager_Band( $post->ID );
        $social_media = $band->get_social_media();

        // Define social media platforms
        $platforms = array(
            'facebook'  => __( 'Facebook', 'merchmanager' ),
            'twitter'   => __( 'Twitter', 'merchmanager' ),
            'instagram' => __( 'Instagram', 'merchmanager' ),
            'youtube'   => __( 'YouTube', 'merchmanager' ),
            'spotify'   => __( 'Spotify', 'merchmanager' ),
            'apple'     => __( 'Apple Music', 'merchmanager' ),
            'bandcamp'  => __( 'Bandcamp', 'merchmanager' ),
            'soundcloud' => __( 'SoundCloud', 'merchmanager' ),
        );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <p class="description">
                <?php _e( 'Enter the social media links for this band.', 'merchmanager' ); ?>
            </p>
        </div>

        <?php foreach ( $platforms as $platform => $label ) : ?>
            <div class="msp-meta-box-field">
                <label for="msp_band_social_media_<?php echo esc_attr( $platform ); ?>">
                    <?php echo esc_html( $label ); ?>
                </label>
                <input type="url" id="msp_band_social_media_<?php echo esc_attr( $platform ); ?>" name="msp_band_social_media[<?php echo esc_attr( $platform ); ?>]" value="<?php echo isset( $social_media[ $platform ] ) ? esc_attr( $social_media[ $platform ] ) : ''; ?>" class="regular-text">
            </div>
        <?php endforeach; ?>

        <div class="msp-meta-box-field">
            <label for="msp_band_social_media_other">
                <?php _e( 'Other', 'merchmanager' ); ?>
            </label>
            <input type="url" id="msp_band_social_media_other" name="msp_band_social_media[other]" value="<?php echo isset( $social_media['other'] ) ? esc_attr( $social_media['other'] ) : ''; ?>" class="regular-text">
        </div>
        <?php
    }

    /**
     * Render the users meta box.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_users_meta_box( $post ) {
        // Get band data
        $band = new Merchmanager_Band( $post->ID );
        $associated_users = $band->get_users();

        // Get all users
        $all_users = get_users( array(
            'role__in' => array( 'administrator', 'msp_management', 'msp_tour_management', 'msp_merch_sales' ),
        ) );

        // Define user roles
        $roles = array(
            'manager'       => __( 'Manager', 'merchmanager' ),
            'tour_manager'  => __( 'Tour Manager', 'merchmanager' ),
            'merch_manager' => __( 'Merchandise Manager', 'merchmanager' ),
            'member'        => __( 'Band Member', 'merchmanager' ),
        );

        // Render fields
        ?>
        <div class="msp-meta-box-field">
            <p class="description">
                <?php _e( 'Associate users with this band. These users will have access to manage this band\'s data.', 'merchmanager' ); ?>
            </p>
        </div>

        <table class="widefat" id="msp-band-users-table">
            <thead>
                <tr>
                    <th><?php _e( 'User', 'merchmanager' ); ?></th>
                    <th><?php _e( 'Role', 'merchmanager' ); ?></th>
                    <th><?php _e( 'Actions', 'merchmanager' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( empty( $associated_users ) ) : ?>
                    <tr class="msp-no-users">
                        <td colspan="3"><?php _e( 'No users associated with this band.', 'merchmanager' ); ?></td>
                    </tr>
                <?php else : ?>
                    <?php foreach ( $associated_users as $user ) : ?>
                        <tr>
                            <td>
                                <?php echo esc_html( $user->display_name ); ?>
                                <input type="hidden" name="msp_band_users[]" value="<?php echo esc_attr( $user->ID ); ?>">
                            </td>
                            <td>
                                <select name="msp_band_user_roles[<?php echo esc_attr( $user->ID ); ?>]">
                                    <?php foreach ( $roles as $role_key => $role_label ) : ?>
                                        <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( get_user_meta( $user->ID, '_msp_band_' . $post->ID . '_role', true ), $role_key ); ?>>
                                            <?php echo esc_html( $role_label ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="button msp-remove-user"><?php _e( 'Remove', 'merchmanager' ); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <select id="msp-add-user-select">
                            <option value=""><?php _e( 'Select a user...', 'merchmanager' ); ?></option>
                            <?php foreach ( $all_users as $user ) : ?>
                                <?php
                                // Skip users already associated with this band
                                $is_associated = false;
                                foreach ( $associated_users as $associated_user ) {
                                    if ( $associated_user->ID === $user->ID ) {
                                        $is_associated = true;
                                        break;
                                    }
                                }
                                if ( $is_associated ) {
                                    continue;
                                }
                                ?>
                                <option value="<?php echo esc_attr( $user->ID ); ?>" data-name="<?php echo esc_attr( $user->display_name ); ?>">
                                    <?php echo esc_html( $user->display_name ); ?> (<?php echo esc_html( $user->user_email ); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select id="msp-add-user-role">
                            <?php foreach ( $roles as $role_key => $role_label ) : ?>
                                <option value="<?php echo esc_attr( $role_key ); ?>">
                                    <?php echo esc_html( $role_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="button button-primary" id="msp-add-user"><?php _e( 'Add User', 'merchmanager' ); ?></button>
                    </td>
                </tr>
            </tfoot>
        </table>

        <script>
        jQuery(document).ready(function($) {
            // Add user
            $('#msp-add-user').on('click', function() {
                var userId = $('#msp-add-user-select').val();
                var userName = $('#msp-add-user-select option:selected').data('name');
                var userRole = $('#msp-add-user-role').val();
                
                if (!userId) {
                    return;
                }
                
                // Remove "no users" row if present
                $('.msp-no-users').remove();
                
                // Add user row
                var html = '<tr>';
                html += '<td>' + userName + '<input type="hidden" name="msp_band_users[]" value="' + userId + '"></td>';
                html += '<td><select name="msp_band_user_roles[' + userId + ']">';
                <?php foreach ( $roles as $role_key => $role_label ) : ?>
                    html += '<option value="<?php echo esc_attr( $role_key ); ?>"';
                    if (userRole === '<?php echo esc_attr( $role_key ); ?>') {
                        html += ' selected';
                    }
                    html += '><?php echo esc_html( $role_label ); ?></option>';
                <?php endforeach; ?>
                html += '</select></td>';
                html += '<td><button type="button" class="button msp-remove-user"><?php _e( 'Remove', 'merchmanager' ); ?></button></td>';
                html += '</tr>';
                
                $('#msp-band-users-table tbody').append(html);
                
                // Reset select
                $('#msp-add-user-select').val('');
            });
            
            // Remove user
            $(document).on('click', '.msp-remove-user', function() {
                $(this).closest('tr').remove();
                
                // Add "no users" row if no users left
                if ($('#msp-band-users-table tbody tr').length === 0) {
                    var html = '<tr class="msp-no-users">';
                    html += '<td colspan="3"><?php _e( 'No users associated with this band.', 'merchmanager' ); ?></td>';
                    html += '</tr>';
                    
                    $('#msp-band-users-table tbody').append(html);
                }
            });
        });
        </script>
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
        if ( ! isset( $_POST['msp_band_meta_box_nonce'] ) ) {
            return;
        }

        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['msp_band_meta_box_nonce'], 'msp_band_meta_box' ) ) {
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

        // Get band
        $band = new Merchmanager_Band( $post_id );

        // Save contact information
        if ( isset( $_POST['msp_band_contact_name'] ) ) {
            $band->set_contact_name( sanitize_text_field( $_POST['msp_band_contact_name'] ) );
        }

        if ( isset( $_POST['msp_band_contact_email'] ) ) {
            $band->set_contact_email( sanitize_email( $_POST['msp_band_contact_email'] ) );
        }

        if ( isset( $_POST['msp_band_contact_phone'] ) ) {
            $band->set_contact_phone( sanitize_text_field( $_POST['msp_band_contact_phone'] ) );
        }

        if ( isset( $_POST['msp_band_website'] ) ) {
            $band->set_website( esc_url_raw( $_POST['msp_band_website'] ) );
        }

        // Save social media
        if ( isset( $_POST['msp_band_social_media'] ) && is_array( $_POST['msp_band_social_media'] ) ) {
            $social_media = array();
            foreach ( $_POST['msp_band_social_media'] as $platform => $url ) {
                if ( ! empty( $url ) ) {
                    $social_media[ sanitize_key( $platform ) ] = esc_url_raw( $url );
                }
            }
            $band->set_social_media( $social_media );
        }

        // Save band
        $band->save();

        // Save associated users
        $current_users = $band->get_users();
        $current_user_ids = array();
        foreach ( $current_users as $user ) {
            $current_user_ids[] = $user->ID;
        }

        $new_user_ids = isset( $_POST['msp_band_users'] ) ? array_map( 'intval', $_POST['msp_band_users'] ) : array();
        $user_roles = isset( $_POST['msp_band_user_roles'] ) ? $_POST['msp_band_user_roles'] : array();

        // Remove users no longer associated
        foreach ( $current_user_ids as $user_id ) {
            if ( ! in_array( $user_id, $new_user_ids ) ) {
                $band->remove_user( $user_id );
            }
        }

        // Add or update users
        foreach ( $new_user_ids as $user_id ) {
            $role = isset( $user_roles[ $user_id ] ) ? sanitize_key( $user_roles[ $user_id ] ) : 'member';
            $band->add_user( $user_id, $role );
        }
    }
}
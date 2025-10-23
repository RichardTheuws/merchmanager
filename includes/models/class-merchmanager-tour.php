<?php
/**
 * The tour model class.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 */

/**
 * The tour model class.
 *
 * This class represents a tour entity and provides methods for CRUD operations.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/models
 * @author     Your Name <email@example.com>
 */
class Merchmanager_Tour {

    /**
     * The ID of the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $id    The ID of the tour.
     */
    private $id;

    /**
     * The name of the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $name    The name of the tour.
     */
    private $name;

    /**
     * The description of the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $description    The description of the tour.
     */
    private $description;

    /**
     * The ID of the band associated with the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $band_id    The ID of the band associated with the tour.
     */
    private $band_id;

    /**
     * The start date of the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $start_date    The start date of the tour.
     */
    private $start_date;

    /**
     * The end date of the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $end_date    The end date of the tour.
     */
    private $end_date;

    /**
     * The status of the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $status    The status of the tour.
     */
    private $status;

    /**
     * Additional notes for the tour.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $notes    Additional notes for the tour.
     */
    private $notes;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    int       $id             The ID of the tour.
     * @param    string    $name           The name of the tour.
     * @param    string    $description    The description of the tour.
     */
    public function __construct( $id = 0, $name = '', $description = '' ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->status = 'upcoming';

        // If ID is provided, load the tour data
        if ( $id > 0 ) {
            $this->load();
        }
    }

    /**
     * Load tour data from the database.
     *
     * @since    1.0.0
     * @return   bool    True if data was loaded, false otherwise.
     */
    public function load() {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Get post
        $post = get_post( $this->id );
        if ( ! $post || 'msp_tour' !== $post->post_type ) {
            return false;
        }

        // Set properties
        $this->name = $post->post_title;
        $this->description = $post->post_content;
        $this->band_id = get_post_meta( $this->id, '_msp_tour_band_id', true );
        $this->start_date = get_post_meta( $this->id, '_msp_tour_start_date', true );
        $this->end_date = get_post_meta( $this->id, '_msp_tour_end_date', true );
        $this->status = get_post_meta( $this->id, '_msp_tour_status', true );
        $this->notes = get_post_meta( $this->id, '_msp_tour_notes', true );

        return true;
    }

    /**
     * Save tour data to the database.
     *
     * @since    1.0.0
     * @return   int|WP_Error    The tour ID on success, WP_Error on failure.
     */
    public function save() {
        // Prepare post data
        $post_data = array(
            'post_title'   => $this->name,
            'post_content' => $this->description,
            'post_status'  => 'publish',
            'post_type'    => 'msp_tour',
        );

        // Filter post data
        $post_data = apply_filters( 'msp_tour_data', $post_data, $this );

        // Insert or update post
        if ( $this->id > 0 ) {
            $post_data['ID'] = $this->id;
            $result = wp_update_post( $post_data, true );
        } else {
            $result = wp_insert_post( $post_data, true );
        }

        // Check for errors
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        // Set ID if new post
        $this->id = $result;

        // Save meta data
        update_post_meta( $this->id, '_msp_tour_band_id', $this->band_id );
        update_post_meta( $this->id, '_msp_tour_start_date', $this->start_date );
        update_post_meta( $this->id, '_msp_tour_end_date', $this->end_date );
        update_post_meta( $this->id, '_msp_tour_status', $this->status );
        update_post_meta( $this->id, '_msp_tour_notes', $this->notes );

        // Trigger action after save
        do_action( 'msp_after_tour_save', $this->id, $post_data );

        return $this->id;
    }

    /**
     * Delete the tour from the database.
     *
     * @since    1.0.0
     * @param    bool    $force_delete    Whether to bypass trash and force deletion.
     * @return   bool|WP_Error            True on success, false or WP_Error on failure.
     */
    public function delete( $force_delete = false ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return false;
        }

        // Trigger action before delete
        do_action( 'msp_before_tour_delete', $this->id );

        // Delete post
        $result = wp_delete_post( $this->id, $force_delete );

        // Check for errors
        if ( ! $result ) {
            return false;
        }

        // Trigger action after delete
        do_action( 'msp_after_tour_delete', $this->id );

        // Reset ID
        $this->id = 0;

        return true;
    }

    /**
     * Get all tours.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Tour objects.
     */
    public static function get_all( $args = array() ) {
        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_tour',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Filter arguments
        $args = apply_filters( 'msp_tour_query_args', $args );

        // Get posts
        $posts = get_posts( $args );

        // Create tour objects
        $tours = array();
        foreach ( $posts as $post ) {
            $tours[] = new self( $post->ID );
        }

        return $tours;
    }

    /**
     * Get tour by ID.
     *
     * @since    1.0.0
     * @param    int       $id    The tour ID.
     * @return   Merchmanager_Tour|false    Tour object on success, false on failure.
     */
    public static function get_by_id( $id ) {
        $tour = new self( $id );
        return $tour->id > 0 ? $tour : false;
    }

    /**
     * Get shows for this tour.
     *
     * @since    1.0.0
     * @param    array    $args    Additional arguments for get_posts.
     * @return   array             Array of Merchmanager_Show objects.
     */
    public function get_shows( $args = array() ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array();
        }

        // Default arguments
        $default_args = array(
            'post_type'      => 'msp_show',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => '_msp_show_tour_id',
                    'value' => $this->id,
                ),
            ),
            'meta_key'       => '_msp_show_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
        );

        // Merge arguments
        $args = wp_parse_args( $args, $default_args );

        // Get posts
        $posts = get_posts( $args );

        // Create show objects
        $shows = array();
        foreach ( $posts as $post ) {
            $shows[] = new Merchmanager_Show( $post->ID );
        }

        return $shows;
    }

    /**
     * Get the band associated with this tour.
     *
     * @since    1.0.0
     * @return   Merchmanager_Band|false    Band object on success, false on failure.
     */
    public function get_band() {
        // Check if band ID is valid
        if ( ! $this->band_id ) {
            return false;
        }

        return new Merchmanager_Band( $this->band_id );
    }

    /**
     * Import shows from CSV.
     *
     * @since    1.0.0
     * @param    string    $file_path    Path to the CSV file.
     * @param    array     $mapping      Mapping of CSV columns to show fields.
     * @return   array                   Array with import results.
     */
    public function import_shows_from_csv( $file_path, $mapping ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid tour ID.', 'merchmanager' ),
            );
        }

        // Check if file exists
        if ( ! file_exists( $file_path ) ) {
            return array(
                'success' => false,
                'message' => __( 'CSV file not found.', 'merchmanager' ),
            );
        }

        // Open the file
        $file = fopen( $file_path, 'r' );
        if ( ! $file ) {
            return array(
                'success' => false,
                'message' => __( 'Could not open CSV file.', 'merchmanager' ),
            );
        }

        // Get CSV delimiter
        $options = get_option( 'msp_settings', array() );
        $delimiter = isset( $options['csv_delimiter'] ) ? $options['csv_delimiter'] : ',';
        if ( $delimiter === '\t' ) {
            $delimiter = "\t";
        }

        // Read header row
        $header = fgetcsv( $file, 0, $delimiter );
        if ( ! $header ) {
            fclose( $file );
            return array(
                'success' => false,
                'message' => __( 'Could not read CSV header.', 'merchmanager' ),
            );
        }

        // Initialize results
        $results = array(
            'success'   => true,
            'imported'  => 0,
            'skipped'   => 0,
            'errors'    => array(),
        );

        // Process rows
        while ( ( $row = fgetcsv( $file, 0, $delimiter ) ) !== false ) {
            // Skip empty rows
            if ( count( $row ) <= 1 && empty( $row[0] ) ) {
                continue;
            }

            // Create data array from mapping
            $data = array();
            foreach ( $mapping as $field => $column ) {
                if ( isset( $header[ $column ] ) && isset( $row[ $column ] ) ) {
                    $data[ $field ] = $row[ $column ];
                }
            }

            // Check required fields
            if ( empty( $data['name'] ) || empty( $data['date'] ) ) {
                $results['skipped']++;
                $results['errors'][] = sprintf(
                    __( 'Row skipped: Missing required fields (name or date). Data: %s', 'merchmanager' ),
                    json_encode( $data )
                );
                continue;
            }

            // Create show
            $show = new Merchmanager_Show();
            $show->set_name( $data['name'] );
            $show->set_tour_id( $this->id );
            $show->set_date( $data['date'] );

            // Set optional fields
            if ( ! empty( $data['venue_name'] ) ) {
                $show->set_venue_name( $data['venue_name'] );
            }
            if ( ! empty( $data['venue_address'] ) ) {
                $show->set_venue_address( $data['venue_address'] );
            }
            if ( ! empty( $data['venue_city'] ) ) {
                $show->set_venue_city( $data['venue_city'] );
            }
            if ( ! empty( $data['venue_state'] ) ) {
                $show->set_venue_state( $data['venue_state'] );
            }
            if ( ! empty( $data['venue_country'] ) ) {
                $show->set_venue_country( $data['venue_country'] );
            }
            if ( ! empty( $data['venue_postal_code'] ) ) {
                $show->set_venue_postal_code( $data['venue_postal_code'] );
            }
            if ( ! empty( $data['venue_contact'] ) ) {
                $show->set_venue_contact( $data['venue_contact'] );
            }
            if ( ! empty( $data['notes'] ) ) {
                $show->set_notes( $data['notes'] );
            }

            // Save show
            $result = $show->save();
            if ( is_wp_error( $result ) ) {
                $results['skipped']++;
                $results['errors'][] = sprintf(
                    __( 'Error saving show: %s', 'merchmanager' ),
                    $result->get_error_message()
                );
            } else {
                $results['imported']++;
            }
        }

        // Close the file
        fclose( $file );

        // Add summary message
        $results['message'] = sprintf(
            __( 'Import completed: %d shows imported, %d skipped.', 'merchmanager' ),
            $results['imported'],
            $results['skipped']
        );

        return $results;
    }

    /**
     * Export shows to CSV.
     *
     * @since    1.0.0
     * @param    string    $file_path    Path to save the CSV file.
     * @return   array                   Array with export results.
     */
    public function export_shows_to_csv( $file_path ) {
        // Check if ID is valid
        if ( $this->id <= 0 ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid tour ID.', 'merchmanager' ),
            );
        }

        // Get shows
        $shows = $this->get_shows();
        if ( empty( $shows ) ) {
            return array(
                'success' => false,
                'message' => __( 'No shows found for this tour.', 'merchmanager' ),
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
            'Name',
            'Date',
            'Venue Name',
            'Venue Address',
            'Venue City',
            'Venue State',
            'Venue Country',
            'Venue Postal Code',
            'Venue Contact',
            'Notes',
        );
        fputcsv( $file, $header, $delimiter );

        // Write data rows
        foreach ( $shows as $show ) {
            $row = array(
                $show->get_id(),
                $show->get_name(),
                $show->get_date(),
                $show->get_venue_name(),
                $show->get_venue_address(),
                $show->get_venue_city(),
                $show->get_venue_state(),
                $show->get_venue_country(),
                $show->get_venue_postal_code(),
                $show->get_venue_contact(),
                $show->get_notes(),
            );
            fputcsv( $file, $row, $delimiter );
        }

        // Close the file
        fclose( $file );

        return array(
            'success' => true,
            'message' => sprintf(
                __( 'Export completed: %d shows exported to %s.', 'merchmanager' ),
                count( $shows ),
                basename( $file_path )
            ),
            'file_path' => $file_path,
        );
    }

    /**
     * Get the ID of the tour.
     *
     * @since    1.0.0
     * @return   int    The ID of the tour.
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Get the name of the tour.
     *
     * @since    1.0.0
     * @return   string    The name of the tour.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the tour.
     *
     * @since    1.0.0
     * @param    string    $name    The name of the tour.
     * @return   void
     */
    public function set_name( $name ) {
        $this->name = $name;
    }

    /**
     * Get the description of the tour.
     *
     * @since    1.0.0
     * @return   string    The description of the tour.
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set the description of the tour.
     *
     * @since    1.0.0
     * @param    string    $description    The description of the tour.
     * @return   void
     */
    public function set_description( $description ) {
        $this->description = $description;
    }

    /**
     * Get the ID of the band associated with the tour.
     *
     * @since    1.0.0
     * @return   int    The ID of the band associated with the tour.
     */
    public function get_band_id() {
        return $this->band_id;
    }

    /**
     * Set the ID of the band associated with the tour.
     *
     * @since    1.0.0
     * @param    int    $band_id    The ID of the band associated with the tour.
     * @return   void
     */
    public function set_band_id( $band_id ) {
        $this->band_id = $band_id;
    }

    /**
     * Get the start date of the tour.
     *
     * @since    1.0.0
     * @return   string    The start date of the tour.
     */
    public function get_start_date() {
        return $this->start_date;
    }

    /**
     * Set the start date of the tour.
     *
     * @since    1.0.0
     * @param    string    $start_date    The start date of the tour.
     * @return   void
     */
    public function set_start_date( $start_date ) {
        $this->start_date = $start_date;
    }

    /**
     * Get the end date of the tour.
     *
     * @since    1.0.0
     * @return   string    The end date of the tour.
     */
    public function get_end_date() {
        return $this->end_date;
    }

    /**
     * Set the end date of the tour.
     *
     * @since    1.0.0
     * @param    string    $end_date    The end date of the tour.
     * @return   void
     */
    public function set_end_date( $end_date ) {
        $this->end_date = $end_date;
    }

    /**
     * Get the status of the tour.
     *
     * @since    1.0.0
     * @return   string    The status of the tour.
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * Set the status of the tour.
     *
     * @since    1.0.0
     * @param    string    $status    The status of the tour.
     * @return   void
     */
    public function set_status( $status ) {
        $this->status = $status;
    }

    /**
     * Get additional notes for the tour.
     *
     * @since    1.0.0
     * @return   string    Additional notes for the tour.
     */
    public function get_notes() {
        return $this->notes;
    }

    /**
     * Set additional notes for the tour.
     *
     * @since    1.0.0
     * @param    string    $notes    Additional notes for the tour.
     * @return   void
     */
    public function set_notes( $notes ) {
        $this->notes = $notes;
    }
}
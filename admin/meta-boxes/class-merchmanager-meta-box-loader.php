<?php
/**
 * The meta box loader class.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 */

/**
 * The meta box loader class.
 *
 * Initializes all meta boxes.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/meta-boxes
 * @author     Theuws Consulting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Merchmanager_Meta_Box_Loader {

    /**
     * The array of meta box classes.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $meta_boxes    The array of meta box classes.
     */
    protected $meta_boxes = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->initialize_meta_boxes();
    }

    /**
     * Load the required dependencies.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Load model classes (required by meta boxes)
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-band.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-tour.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-show.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-merchandise.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '../includes/models/class-merchmanager-sales-page.php';
        // Load meta box classes
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'meta-boxes/class-merchmanager-band-meta-box.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'meta-boxes/class-merchmanager-tour-meta-box.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'meta-boxes/class-merchmanager-show-meta-box.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'meta-boxes/class-merchmanager-merchandise-meta-box.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'meta-boxes/class-merchmanager-sales-page-meta-box.php';
    }

    /**
     * Initialize meta boxes.
     *
     * @since    1.0.0
     * @access   private
     */
    private function initialize_meta_boxes() {
        // Initialize meta box classes
        $this->meta_boxes['band'] = new Merchmanager_Band_Meta_Box();
        $this->meta_boxes['tour'] = new Merchmanager_Tour_Meta_Box();
        $this->meta_boxes['show'] = new Merchmanager_Show_Meta_Box();
        $this->meta_boxes['merchandise'] = new Merchmanager_Merchandise_Meta_Box();
        $this->meta_boxes['sales_page'] = new Merchmanager_Sales_Page_Meta_Box();
    }
}
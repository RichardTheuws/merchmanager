<?php
/**
 * The demo service class.
 *
 * Creates complete demo data for showcasing MerchManager functionality.
 *
 * @link       https://theuws.com
 * @since      1.0.3
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 */

/**
 * The demo service class.
 *
 * This class creates band, tour, shows, merchandise, sales page and sample sales
 * for demonstration purposes.
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes/services
 * @author     Theuws Consulting
 */
class Merchmanager_Demo_Service {

	/**
	 * Option key for tracking if demo data has been loaded.
	 *
	 * @since 1.0.3
	 * @var string
	 */
	const DEMO_LOADED_OPTION = 'merchmanager_demo_data_loaded';

	/**
	 * Create demo data: band, tour, shows, merchandise, sales page and sample sales.
	 *
	 * @since 1.0.3
	 * @return array{band_id: int, tour_id: int, show_ids: int[], merchandise_ids: int[], sales_page_id: int, sales_count: int}|WP_Error
	 */
	public function create_demo_data() {
		// Load required models
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-band.php';
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-tour.php';
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-show.php';
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-merchandise.php';
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/models/class-merchmanager-sales-page.php';

		// 1. Create Band
		$band = new Merchmanager_Band( 0, __( 'Demo Band', 'merchmanager' ), __( 'Demo band for showcasing MerchManager.', 'merchmanager' ) );
		$band_id = $band->save();
		if ( is_wp_error( $band_id ) ) {
			return $band_id;
		}
		$this->attach_demo_image( $band_id, 'demoband', 400, 400 );

		// 2. Create Tour
		$today    = strtotime( 'today' );
		$start    = gmdate( 'Y-m-d', $today );
		$end      = gmdate( 'Y-m-d', strtotime( '+30 days', $today ) );
		$tour     = new Merchmanager_Tour( 0, __( 'European Tour 2026', 'merchmanager' ), '' );
		$tour->set_band_id( $band_id );
		$tour->set_start_date( $start );
		$tour->set_end_date( $end );
		$tour->set_status( 'active' );
		$tour_id = $tour->save();
		if ( is_wp_error( $tour_id ) ) {
			return $tour_id;
		}
		$this->attach_demo_image( $tour_id, 'demotour', 600, 400 );

		// 3. Create Shows (Amsterdam today, Rotterdam +7d, Berlin +14d)
		$show_configs = array(
			array(
				'name'    => __( 'Amsterdam Show', 'merchmanager' ),
				'date'    => gmdate( 'Y-m-d H:i:s', $today ),
				'city'    => 'Amsterdam',
				'country' => 'Netherlands',
			),
			array(
				'name'    => __( 'Rotterdam Show', 'merchmanager' ),
				'date'    => gmdate( 'Y-m-d H:i:s', strtotime( '+7 days', $today ) ),
				'city'    => 'Rotterdam',
				'country' => 'Netherlands',
			),
			array(
				'name'    => __( 'Berlin Show', 'merchmanager' ),
				'date'    => gmdate( 'Y-m-d H:i:s', strtotime( '+14 days', $today ) ),
				'city'    => 'Berlin',
				'country' => 'Germany',
			),
		);
		$show_ids = array();
		foreach ( $show_configs as $config ) {
			$show = new Merchmanager_Show( 0, $config['name'], '' );
			$show->set_tour_id( $tour_id );
			$show->set_date( $config['date'] );
			$show->set_venue_name( $config['name'] . ' Venue' );
			$show->set_venue_city( $config['city'] );
			$show->set_venue_country( $config['country'] );
			$sid = $show->save();
			if ( ! is_wp_error( $sid ) ) {
				$show_ids[] = $sid;
				$this->attach_demo_image( $sid, 'demoshow' . $sid, 600, 400 );
			}
		}

		// 4. Create Merchandise (T-shirt €25, Hoodie €45, CD €12, Poster €8, Cap €15)
		$merch_configs = array(
			array( 'name' => __( 'T-shirt', 'merchmanager' ), 'price' => 25, 'sku' => 'DEMO-TS', 'category' => 'apparel', 'image_seed' => 'tshirt' ),
			array( 'name' => __( 'Hoodie', 'merchmanager' ), 'price' => 45, 'sku' => 'DEMO-HD', 'category' => 'apparel', 'image_seed' => 'hoodie' ),
			array( 'name' => __( 'CD', 'merchmanager' ), 'price' => 12, 'sku' => 'DEMO-CD', 'category' => 'music', 'image_seed' => 'cd' ),
			array( 'name' => __( 'Poster', 'merchmanager' ), 'price' => 8, 'sku' => 'DEMO-PS', 'category' => 'poster', 'image_seed' => 'poster' ),
			array( 'name' => __( 'Cap', 'merchmanager' ), 'price' => 15, 'sku' => 'DEMO-CP', 'category' => 'accessory', 'image_seed' => 'cap' ),
		);
		$merchandise_ids = array();
		foreach ( $merch_configs as $config ) {
			$merch = new Merchmanager_Merchandise( 0, $config['name'], '' );
			$merch->set_band_id( $band_id );
			$merch->set_price( $config['price'] );
			$merch->set_sku( $config['sku'] );
			$merch->set_stock( 50 );
			$merch->set_active( true );
			if ( ! empty( $config['category'] ) ) {
				$merch->set_category( $config['category'] );
			}
			$mid = $merch->save();
			if ( ! is_wp_error( $mid ) ) {
				$merchandise_ids[] = $mid;
				$this->attach_demo_image( $mid, 'demo' . ( $config['image_seed'] ?? $config['sku'] ), 400, 400 );
			}
		}

		// 5. Create Sales Page (access code DEMO2026)
		$first_show_id = ! empty( $show_ids ) ? $show_ids[0] : 0;
		$sales_page    = new Merchmanager_Sales_Page( 0, __( 'Demo Sales Page', 'merchmanager' ) );
		$sales_page->set_band_id( $band_id );
		$sales_page->set_show_id( $first_show_id );
		$sales_page->set_access_code( 'DEMO2026' );
		$sales_page->set_status( 'active' );
		$sales_page->set_merchandise( $merchandise_ids );
		$sales_page_id = $sales_page->save();
		if ( is_wp_error( $sales_page_id ) ) {
			return $sales_page_id;
		}

		// 6. Insert 15–25 sample sales across shows (cash/card)
		$sales_count = $this->insert_demo_sales( $band_id, $show_ids, $merchandise_ids );

		update_option( self::DEMO_LOADED_OPTION, true );

		return array(
			'band_id'         => $band_id,
			'tour_id'         => $tour_id,
			'show_ids'        => $show_ids,
			'merchandise_ids' => $merchandise_ids,
			'sales_page_id'   => $sales_page_id,
			'sales_count'     => $sales_count,
		);
	}

	/**
	 * Insert 15–25 demo sales across shows.
	 *
	 * @since 1.0.3
	 * @param int   $band_id         Band ID.
	 * @param int[] $show_ids        Show IDs.
	 * @param int[] $merchandise_ids Merchandise IDs.
	 * @return int Number of sales inserted.
	 */
	private function insert_demo_sales( $band_id, $show_ids, $merchandise_ids ) {
		global $wpdb;
		$table   = $wpdb->prefix . 'msp_sales';
		$now     = current_time( 'mysql' );
		$payment = array( 'cash', 'card' );
		$count   = 0;
		$target  = wp_rand( 15, 25 );

		if ( empty( $show_ids ) || empty( $merchandise_ids ) ) {
			return 0;
		}

		while ( $count < $target ) {
			$show_id   = $show_ids[ array_rand( $show_ids ) ];
			$merch_id  = $merchandise_ids[ array_rand( $merchandise_ids ) ];
			$price     = (float) get_post_meta( $merch_id, '_msp_merchandise_price', true );
			$quantity  = wp_rand( 1, 4 );
			$show_date = get_post_meta( $show_id, '_msp_show_date', true );
			if ( ! $show_date ) {
				$show_date = $now;
			}

			$wpdb->insert(
				$table,
				array(
					'date'           => $show_date,
					'merchandise_id' => $merch_id,
					'quantity'       => $quantity,
					'price'          => $price,
					'payment_type'   => $payment[ array_rand( $payment ) ],
					'show_id'        => $show_id,
					'band_id'        => $band_id,
					'created_at'     => $now,
					'updated_at'     => $now,
				),
				array( '%s', '%d', '%d', '%f', '%s', '%d', '%d', '%s', '%s' )
			);

			if ( $wpdb->insert_id ) {
				$stock = get_post_meta( $merch_id, '_msp_merchandise_stock', true );
				if ( '' !== $stock ) {
					$previous = (int) $stock;
					$new      = max( 0, $previous - $quantity );
					update_post_meta( $merch_id, '_msp_merchandise_stock', $new );
					$wpdb->insert(
						$wpdb->prefix . 'msp_stock_log',
						array(
							'merchandise_id' => $merch_id,
							'previous_stock' => $previous,
							'new_stock'      => $new,
							'change_reason'  => 'sale',
							'created_at'     => $now,
						),
						array( '%d', '%d', '%d', '%s', '%s' )
					);
				}
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Attach a royalty-free demo image to a post (band, tour, show, merchandise).
	 * Uses Picsum Photos (Lorem Picsum) – images from Unsplash, free for commercial use.
	 *
	 * @since 1.0.3
	 * @param int    $post_id Post ID to attach image to.
	 * @param string $seed    Seed for reproducible image (e.g. 'demoband', 'tshirt').
	 * @param int    $width   Image width (default 400).
	 * @param int    $height  Image height (default 400).
	 * @return int|false Attachment ID on success, false on failure.
	 */
	private function attach_demo_image( $post_id, $seed, $width = 400, $height = 400 ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$url = sprintf( 'https://picsum.photos/seed/%s/%d/%d', sanitize_key( $seed ), (int) $width, (int) $height );

		$attachment_id = media_sideload_image( $url, $post_id, null, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return false;
		}

		if ( $attachment_id && is_numeric( $attachment_id ) ) {
			set_post_thumbnail( $post_id, (int) $attachment_id );
			return (int) $attachment_id;
		}

		return false;
	}

	/**
	 * Check if demo data has been loaded.
	 *
	 * @since 1.0.3
	 * @return bool
	 */
	public static function is_demo_loaded() {
		return (bool) get_option( self::DEMO_LOADED_OPTION, false );
	}
}

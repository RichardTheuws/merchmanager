<?php
/**
 * Admin sales recording page
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load sales recording service
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/services/class-merchmanager-sales-recording-service.php';
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/models/class-merchmanager-band.php';
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/models/class-merchmanager-merchandise.php';

$sales_recording_service = new Merchmanager_Sales_Recording_Service();
$sales_items = $sales_recording_service->get_sales_items();
$sales_total = $sales_recording_service->get_sales_total();
$bands = Merchmanager_Band::get_all();

// Get shows for filter
$shows = array();
if ( isset( $_GET['band_id'] ) && $_GET['band_id'] ) {
	$shows = get_posts( array(
		'post_type'      => 'msp_show',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'   => '_msp_show_band_id',
				'value' => intval( $_GET['band_id'] ),
			),
		),
	) );
}

// Handle form submissions
if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	if ( isset( $_POST['msp_add_to_sale'] ) && wp_verify_nonce( $_POST['msp_nonce'] ?? '', 'msp_add_to_sale' ) ) {
		$merchandise_id = intval( $_POST['merchandise_id'] ?? 0 );
		$quantity = intval( $_POST['quantity'] ?? 1 );
		$result = $sales_recording_service->add_to_sales_session( $merchandise_id, $quantity );
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
		} else {
			wp_safe_redirect( add_query_arg( array( 'added' => $merchandise_id ), wp_get_referer() ?: admin_url( 'admin.php?page=msp-sales' ) ) );
			exit;
		}
	}
	if ( isset( $_POST['msp_record_sale'] ) && wp_verify_nonce( $_POST['msp_nonce'] ?? '', 'msp_record_sale' ) ) {
		$sale_data = array(
			'payment_type' => sanitize_text_field( $_POST['payment_type'] ?? 'cash' ),
			'show_id'      => isset( $_POST['show_id'] ) ? intval( $_POST['show_id'] ) : 0,
			'notes'        => sanitize_textarea_field( $_POST['notes'] ?? '' ),
		);
		$result = $sales_recording_service->process_sale_recording( $sale_data );
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
		} else {
			$success_message = $result['message'];
		}
	}
	if ( isset( $_POST['msp_update_quantity'] ) && wp_verify_nonce( $_POST['msp_nonce'] ?? '', 'msp_update_quantity' ) ) {
		$merchandise_id = intval( $_POST['merchandise_id'] ?? 0 );
		$quantity = intval( $_POST['quantity'] ?? 0 );
		$result = $sales_recording_service->update_sales_item( $merchandise_id, $quantity );
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
		}
	}
	if ( isset( $_POST['msp_remove_item'] ) && wp_verify_nonce( $_POST['msp_nonce'] ?? '', 'msp_remove_item' ) ) {
		$merchandise_id = intval( $_POST['merchandise_id'] ?? 0 );
		$sales_recording_service->remove_from_sales_session( $merchandise_id );
	}
	if ( isset( $_POST['msp_clear_sale'] ) && wp_verify_nonce( $_POST['msp_nonce'] ?? '', 'msp_clear_sale' ) ) {
		$sales_recording_service->clear_sales_session();
	}
}

$current_band_id = isset( $_GET['band_id'] ) ? intval( $_GET['band_id'] ) : 0;
$current_show_id = isset( $_GET['show_id'] ) ? intval( $_GET['show_id'] ) : 0;

$merchandise_args = array(
	'post_type'      => 'msp_merchandise',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'meta_query'     => array(
		array(
			'key'   => '_msp_merchandise_active',
			'value' => '1',
		),
	),
);
if ( $current_band_id ) {
	$merchandise_args['meta_query'][] = array(
		'key'   => '_msp_merchandise_band_id',
		'value' => $current_band_id,
	);
}
$merchandise = get_posts( $merchandise_args );

$options = get_option( 'msp_settings', array() );
$currency = isset( $options['currency'] ) ? $options['currency'] : 'EUR';
$currency_symbols = array(
	'USD' => '$',
	'EUR' => '€',
	'GBP' => '£',
	'CAD' => '$',
	'AUD' => '$',
);
$currency_symbol = isset( $currency_symbols[ $currency ] ) ? $currency_symbols[ $currency ] : '€';
?>

<div class="wrap">
	<header class="msp-page-header">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	</header>
	<div class="msp-page-content">
	<?php if ( ! empty( $error_message ) ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php echo esc_html( $error_message ); ?></p></div>
	<?php endif; ?>

	<?php if ( ! empty( $success_message ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $success_message ); ?></p></div>
	<?php endif; ?>

	<?php if ( empty( $bands ) ) : ?>
		<div class="msp-empty-state notice notice-info">
			<p><strong><?php esc_html_e( 'No bands yet – create your first band to record sales', 'merchmanager' ); ?></strong></p>
			<p><?php esc_html_e( 'Bands are required before you can record merchandise sales.', 'merchmanager' ); ?></p>
			<p class="msp-empty-state-actions">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_band' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add your first band', 'merchmanager' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding' ) ); ?>" class="button"><?php esc_html_e( 'Run setup wizard', 'merchmanager' ); ?></a>
			</p>
		</div>
	<?php elseif ( empty( $merchandise ) ) : ?>
		<div class="msp-empty-state notice notice-info">
			<p><strong><?php esc_html_e( 'No merchandise yet – add items to sell', 'merchmanager' ); ?></strong></p>
			<p><?php esc_html_e( 'Add merchandise items for your band before recording sales.', 'merchmanager' ); ?></p>
			<p class="msp-empty-state-actions">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_merchandise' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add your first merchandise item', 'merchmanager' ); ?></a>
			</p>
		</div>
	<?php endif; ?>

	<div class="msp-sales-form">
		<div class="msp-report-filters">
			<form method="get" action="">
				<input type="hidden" name="page" value="msp-sales">
				<label for="band_id"><?php esc_html_e( 'Filter by Band:', 'merchmanager' ); ?></label>
				<select name="band_id" id="band_id" onchange="this.form.submit()">
					<option value="0"><?php esc_html_e( 'All Bands', 'merchmanager' ); ?></option>
					<?php foreach ( $bands as $band ) : ?>
						<option value="<?php echo esc_attr( $band->get_id() ); ?>" <?php selected( $current_band_id, $band->get_id() ); ?>>
							<?php echo esc_html( $band->get_name() ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php if ( ! empty( $shows ) ) : ?>
					<label for="show_id"><?php esc_html_e( 'Filter by Show:', 'merchmanager' ); ?></label>
					<select name="show_id" id="show_id" onchange="this.form.submit()">
						<option value="0"><?php esc_html_e( 'All Shows', 'merchmanager' ); ?></option>
						<?php foreach ( $shows as $show ) : ?>
							<option value="<?php echo esc_attr( $show->ID ); ?>" <?php selected( $current_show_id, $show->ID ); ?>>
								<?php echo esc_html( $show->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</form>
		</div>

		<div class="msp-dashboard-content">
			<div class="msp-dashboard-column">
				<div class="msp-dashboard-card">
					<h3><?php esc_html_e( 'Available Merchandise', 'merchmanager' ); ?></h3>
					<div class="msp-dashboard-card-content msp-sales-items">
						<?php if ( ! empty( $merchandise ) ) : ?>
							<?php foreach ( $merchandise as $item ) : ?>
								<?php
								$merch = new Merchmanager_Merchandise( $item->ID );
								$stock = $merch->get_stock();
								$price = $merch->get_price();
								if ( $stock <= 0 ) {
									continue;
								}
								$formatted_price = $currency_symbol . number_format( $price, 2 );
								?>
								<div class="msp-sales-item">
									<div class="msp-sales-item-info">
										<strong><?php echo esc_html( $item->post_title ); ?></strong>
										<span class="msp-merchandise-meta"><?php echo esc_html( $merch->get_sku() ); ?> | <?php echo esc_html( $formatted_price ); ?> | <?php echo esc_html( sprintf( /* translators: %1$d: stock quantity */ __( '%1$d in stock', 'merchmanager' ), $stock ) ); ?></span>
									</div>
									<form method="post" action="" class="msp-sales-item-actions">
										<?php wp_nonce_field( 'msp_add_to_sale', 'msp_nonce' ); ?>
										<input type="hidden" name="merchandise_id" value="<?php echo esc_attr( $item->ID ); ?>">
										<input type="number" name="quantity" class="msp-sales-item-quantity" min="1" max="<?php echo esc_attr( $stock ); ?>" value="1">
										<button type="submit" name="msp_add_to_sale" class="button"><?php esc_html_e( 'Add', 'merchmanager' ); ?></button>
									</form>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<p><?php esc_html_e( 'No merchandise available. Select a band or add merchandise first.', 'merchmanager' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="msp-dashboard-column">
				<div class="msp-dashboard-card">
					<h3><?php esc_html_e( 'Current Sale', 'merchmanager' ); ?></h3>
					<div class="msp-dashboard-card-content">
						<?php if ( ! empty( $sales_items ) ) : ?>
							<table class="widefat striped">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Item', 'merchmanager' ); ?></th>
										<th><?php esc_html_e( 'Price', 'merchmanager' ); ?></th>
										<th><?php esc_html_e( 'Qty', 'merchmanager' ); ?></th>
										<th><?php esc_html_e( 'Subtotal', 'merchmanager' ); ?></th>
										<th><?php esc_html_e( 'Actions', 'merchmanager' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $sales_items as $item ) : ?>
										<tr>
											<td>
												<strong><?php echo esc_html( $item['name'] ); ?></strong>
												<br><small><?php echo esc_html( $item['sku'] ); ?></small>
											</td>
											<td><?php echo esc_html( $currency_symbol . number_format( $item['price'], 2 ) ); ?></td>
											<td>
												<form method="post" action="" style="display:inline;">
													<?php wp_nonce_field( 'msp_update_quantity', 'msp_nonce' ); ?>
													<input type="hidden" name="merchandise_id" value="<?php echo esc_attr( $item['merchandise_id'] ); ?>">
													<input type="number" name="quantity" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="1" max="<?php echo esc_attr( $item['stock'] ); ?>" style="width:50px;">
													<button type="submit" name="msp_update_quantity" class="button button-small"><?php esc_html_e( 'Update', 'merchmanager' ); ?></button>
												</form>
											</td>
											<td><?php echo esc_html( $currency_symbol . number_format( $item['subtotal'], 2 ) ); ?></td>
											<td>
												<form method="post" action="" style="display:inline;">
													<?php wp_nonce_field( 'msp_remove_item', 'msp_nonce' ); ?>
													<input type="hidden" name="merchandise_id" value="<?php echo esc_attr( $item['merchandise_id'] ); ?>">
													<button type="submit" name="msp_remove_item" class="button button-small"><?php esc_html_e( 'Remove', 'merchmanager' ); ?></button>
												</form>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="3" style="text-align:right;"><strong><?php esc_html_e( 'Total:', 'merchmanager' ); ?></strong></td>
										<td colspan="2"><strong><?php echo esc_html( $currency_symbol . number_format( $sales_total, 2 ) ); ?></strong></td>
									</tr>
								</tfoot>
							</table>
							<div class="msp-dashboard-card-footer" style="margin-top:15px;">
								<form method="post" action="">
									<?php wp_nonce_field( 'msp_record_sale', 'msp_nonce' ); ?>
									<p>
										<label for="payment_type"><?php esc_html_e( 'Payment Type:', 'merchmanager' ); ?></label>
										<select name="payment_type" id="payment_type">
											<option value="cash"><?php esc_html_e( 'Cash', 'merchmanager' ); ?></option>
											<option value="card"><?php esc_html_e( 'Card', 'merchmanager' ); ?></option>
											<option value="other"><?php esc_html_e( 'Other', 'merchmanager' ); ?></option>
										</select>
									</p>
									<?php if ( ! empty( $shows ) ) : ?>
										<p>
											<label for="show_id_sale"><?php esc_html_e( 'Show:', 'merchmanager' ); ?></label>
											<select name="show_id" id="show_id_sale">
												<option value="0"><?php esc_html_e( 'Not associated with a show', 'merchmanager' ); ?></option>
												<?php foreach ( $shows as $show ) : ?>
													<option value="<?php echo esc_attr( $show->ID ); ?>" <?php selected( $current_show_id, $show->ID ); ?>>
														<?php echo esc_html( $show->post_title ); ?>
													</option>
												<?php endforeach; ?>
											</select>
										</p>
									<?php endif; ?>
									<p>
										<label for="notes"><?php esc_html_e( 'Notes:', 'merchmanager' ); ?></label>
										<textarea name="notes" id="notes" rows="2" class="large-text"></textarea>
									</p>
									<p>
										<button type="submit" name="msp_record_sale" class="button button-primary"><?php esc_html_e( 'Record Sale', 'merchmanager' ); ?></button>
										<button type="submit" name="msp_clear_sale" class="button" onclick="return confirm('<?php echo esc_js( __( 'Clear the current sale?', 'merchmanager' ) ); ?>');"><?php esc_html_e( 'Clear Sale', 'merchmanager' ); ?></button>
									</p>
								</form>
							</div>
						<?php else : ?>
							<p><?php esc_html_e( 'No items in current sale. Add items from the merchandise list.', 'merchmanager' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	<?php require_once plugin_dir_path( __FILE__ ) . 'merchmanager-admin-footer.php'; ?>
</div>

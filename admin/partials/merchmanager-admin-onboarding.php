<?php
/**
 * Onboarding wizard for MerchManager plugin
 *
 * Guides bands through initial setup after plugin activation.
 *
 * @link       https://theuws.com
 * @since      1.0.3
 * @package    Merchmanager
 * @subpackage Merchmanager/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Handle form submissions
$step = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
$max_step = 5;

if ( isset( $_GET['skip'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['skip'] ) ) ) {
	check_admin_referer( 'merchmanager_skip_onboarding' );
	update_option( 'merchmanager_onboarding_complete', true );
	wp_safe_redirect( admin_url( 'admin.php?page=merchmanager' ) );
	exit;
}

if ( isset( $_POST['merchmanager_onboarding_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['merchmanager_onboarding_nonce'] ) ), 'merchmanager_onboarding' ) ) {
	if ( isset( $_POST['action'] ) && $_POST['action'] === 'load_demo' ) {
		require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/services/class-merchmanager-demo-service.php';
		$demo_service = new Merchmanager_Demo_Service();
		$result       = $demo_service->create_demo_data();
		if ( ! is_wp_error( $result ) ) {
			update_option( 'merchmanager_onboarding_complete', true );
			wp_safe_redirect( admin_url( 'admin.php?page=merchmanager&demo_loaded=1' ) );
			exit;
		}
	}
	if ( isset( $_POST['action'] ) && $_POST['action'] === 'create_band' && ! empty( $_POST['band_name'] ) ) {
		require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/models/class-merchmanager-band.php';
		$band_name = sanitize_text_field( wp_unslash( $_POST['band_name'] ) );
		$band = new Merchmanager_Band( 0, $band_name, '' );
		$result = $band->save();
		if ( ! is_wp_error( $result ) ) {
			$step = 3;
			wp_safe_redirect( admin_url( 'admin.php?page=merchmanager-onboarding&step=3&band_created=1' ) );
			exit;
		}
	}

	if ( isset( $_POST['action'] ) && $_POST['action'] === 'complete' ) {
		update_option( 'merchmanager_onboarding_complete', true );
		wp_safe_redirect( admin_url( 'admin.php?page=merchmanager&onboarding_complete=1' ) );
		exit;
	}
}

// Handle step navigation
if ( isset( $_GET['next'] ) && absint( $_GET['next'] ) > 0 ) {
	$next_step = min( absint( $_GET['next'] ), $max_step );
	wp_safe_redirect( admin_url( 'admin.php?page=merchmanager-onboarding&step=' . $next_step ) );
	exit;
}

$step = min( max( $step, 1 ), $max_step );
?>

<div class="wrap merchmanager-onboarding">
	<header class="msp-page-header merchmanager-onboarding-header">
		<h1><?php esc_html_e( 'MerchManager Setup Wizard', 'merchmanager' ); ?></h1>
		<p class="merchmanager-onboarding-subtitle"><?php esc_html_e( 'Get your merchandise sales up and running in minutes', 'merchmanager' ); ?></p>
		<div class="merchmanager-onboarding-progress">
			<?php for ( $i = 1; $i <= $max_step; $i++ ) : ?>
				<span class="merchmanager-progress-step <?php echo esc_attr( $i <= $step ? 'active' : '' ); ?> <?php echo esc_attr( $i === $step ? 'current' : '' ); ?>"></span>
			<?php endfor; ?>
		</div>
	</header>

	<div class="msp-page-content merchmanager-onboarding-content">
		<?php
		switch ( $step ) {
			case 1:
				?>
				<div class="merchmanager-onboarding-step">
					<h2><?php esc_html_e( 'Welcome to MerchManager', 'merchmanager' ); ?></h2>
					<p><?php esc_html_e( 'MerchManager helps bands and music artists manage merchandise sales during tours and events. Track inventory, record sales, manage tours, and generate reports—all in one place.', 'merchmanager' ); ?></p>
					<ul class="merchmanager-feature-list">
						<li><span class="dashicons dashicons-groups"></span> <?php esc_html_e( 'Multi-band support', 'merchmanager' ); ?></li>
						<li><span class="dashicons dashicons-calendar-alt"></span> <?php esc_html_e( 'Tour and show management', 'merchmanager' ); ?></li>
						<li><span class="dashicons dashicons-cart"></span> <?php esc_html_e( 'Inventory tracking with low-stock alerts', 'merchmanager' ); ?></li>
						<li><span class="dashicons dashicons-money-alt"></span> <?php esc_html_e( 'Sales recording and reports', 'merchmanager' ); ?></li>
						<li><span class="dashicons dashicons-store"></span> <?php esc_html_e( 'Event-specific sales pages with access codes', 'merchmanager' ); ?></li>
					</ul>
					<p class="merchmanager-step-actions">
						<form method="post" style="display: inline-block; margin-right: 10px;">
							<?php wp_nonce_field( 'merchmanager_onboarding', 'merchmanager_onboarding_nonce' ); ?>
							<input type="hidden" name="action" value="load_demo">
							<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Load demo data', 'merchmanager' ); ?></button>
						</form>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=2' ) ); ?>" class="button button-secondary button-hero"><?php esc_html_e( 'Set up yourself', 'merchmanager' ); ?></a>
						<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=merchmanager-onboarding&skip=1' ), 'merchmanager_skip_onboarding' ) ); ?>" class="button button-link" style="margin-left: 10px;"><?php esc_html_e( 'Skip setup', 'merchmanager' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 2:
				require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'includes/models/class-merchmanager-band.php';
				$bands = Merchmanager_Band::get_all();
				?>
				<div class="merchmanager-onboarding-step">
					<h2><?php esc_html_e( 'Create Your First Band', 'merchmanager' ); ?></h2>
					<p><?php esc_html_e( 'A band is the central entity for your merchandise. Add your band name to get started.', 'merchmanager' ); ?></p>
					<?php if ( ! empty( $bands ) ) : ?>
						<div class="merchmanager-notice merchmanager-notice-success">
							<p><?php esc_html_e( 'You already have at least one band. You can add more later or continue to the next step.', 'merchmanager' ); ?></p>
							<p>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=3' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Continue', 'merchmanager' ); ?></a>
							</p>
						</div>
					<?php else : ?>
						<form method="post" class="merchmanager-onboarding-form">
							<?php wp_nonce_field( 'merchmanager_onboarding', 'merchmanager_onboarding_nonce' ); ?>
							<input type="hidden" name="action" value="create_band">
							<p>
								<label for="band_name"><?php esc_html_e( 'Band Name', 'merchmanager' ); ?></label>
								<input type="text" id="band_name" name="band_name" class="regular-text" required placeholder="<?php esc_attr_e( 'e.g. My Awesome Band', 'merchmanager' ); ?>">
							</p>
							<p class="merchmanager-step-actions">
								<button type="submit" class="button button-primary"><?php esc_html_e( 'Create Band & Continue', 'merchmanager' ); ?></button>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=3' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Skip', 'merchmanager' ); ?></a>
							</p>
						</form>
					<?php endif; ?>
					<p class="merchmanager-step-nav">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=1' ) ); ?>">&larr; <?php esc_html_e( 'Back', 'merchmanager' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 3:
				?>
				<div class="merchmanager-onboarding-step">
					<h2><?php esc_html_e( 'Add a Tour and Show', 'merchmanager' ); ?></h2>
					<p><?php esc_html_e( 'Tours group your shows. Each show has a venue, date, and can be linked to merchandise sales. You can add tours and shows later from the Dashboard.', 'merchmanager' ); ?></p>
					<p class="merchmanager-step-actions">
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_tour' ) ); ?>" class="button button-primary" target="_blank"><?php esc_html_e( 'Add Tour', 'merchmanager' ); ?></a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=4' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Skip for now', 'merchmanager' ); ?></a>
					</p>
					<p class="merchmanager-step-nav">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=2' ) ); ?>">&larr; <?php esc_html_e( 'Back', 'merchmanager' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 4:
				?>
				<div class="merchmanager-onboarding-step">
					<h2><?php esc_html_e( 'Add Merchandise', 'merchmanager' ); ?></h2>
					<p><?php esc_html_e( 'Merchandise items are the products you sell: t-shirts, CDs, posters, etc. Each item has a price, stock level, and can be linked to a band. Add your first item or skip and do it later.', 'merchmanager' ); ?></p>
					<p class="merchmanager-step-actions">
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=msp_merchandise' ) ); ?>" class="button button-primary" target="_blank"><?php esc_html_e( 'Add Merchandise', 'merchmanager' ); ?></a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=5' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Skip for now', 'merchmanager' ); ?></a>
					</p>
					<p class="merchmanager-step-nav">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=3' ) ); ?>">&larr; <?php esc_html_e( 'Back', 'merchmanager' ); ?></a>
					</p>
				</div>
				<?php
				break;

			case 5:
				?>
				<div class="merchmanager-onboarding-step">
					<h2><?php esc_html_e( 'You\'re All Set!', 'merchmanager' ); ?></h2>
					<p><?php esc_html_e( 'Here\'s a quick overview of where to find everything:', 'merchmanager' ); ?></p>
					<div class="merchmanager-feature-grid">
						<div class="merchmanager-feature-item">
							<span class="dashicons dashicons-dashboard"></span>
							<strong><?php esc_html_e( 'Dashboard', 'merchmanager' ); ?></strong>
							<p><?php esc_html_e( 'Overview of sales, revenue, tours, and low-stock alerts.', 'merchmanager' ); ?></p>
						</div>
						<div class="merchmanager-feature-item">
							<span class="dashicons dashicons-money-alt"></span>
							<strong><?php esc_html_e( 'Sales', 'merchmanager' ); ?></strong>
							<p><?php esc_html_e( 'Record sales by band, merchandise, and payment type.', 'merchmanager' ); ?></p>
						</div>
						<div class="merchmanager-feature-item">
							<span class="dashicons dashicons-chart-bar"></span>
							<strong><?php esc_html_e( 'Reports', 'merchmanager' ); ?></strong>
							<p><?php esc_html_e( 'Sales reports, inventory, stock history, and alerts.', 'merchmanager' ); ?></p>
						</div>
						<div class="merchmanager-feature-item">
							<span class="dashicons dashicons-admin-generic"></span>
							<strong><?php esc_html_e( 'Settings', 'merchmanager' ); ?></strong>
							<p><?php esc_html_e( 'Currency, date format, roles, notifications, and more.', 'merchmanager' ); ?></p>
						</div>
					</div>
					<div class="merchmanager-shortcodes-info">
						<h3><?php esc_html_e( 'Shortcodes for Your Site', 'merchmanager' ); ?></h3>
						<ul>
							<li><code>[msp_sales_page id="123"]</code> <?php esc_html_e( '— Display a sales page (requires Sales Page ID)', 'merchmanager' ); ?></li>
							<li><code>[msp_sales_recording band_id="1"]</code> <?php esc_html_e( '— Inline sales recording form for a band', 'merchmanager' ); ?></li>
							<li><code>[msp_band_dashboard band_id="1"]</code> <?php esc_html_e( '— Band dashboard with tours, merchandise, reports', 'merchmanager' ); ?></li>
						</ul>
					</div>
					<form method="post" class="merchmanager-onboarding-form">
						<?php wp_nonce_field( 'merchmanager_onboarding', 'merchmanager_onboarding_nonce' ); ?>
						<input type="hidden" name="action" value="complete">
						<p class="merchmanager-step-actions">
							<button type="submit" class="button button-primary button-hero"><?php esc_html_e( 'Finish Setup', 'merchmanager' ); ?></button>
						</p>
					</form>
					<p class="merchmanager-step-nav">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=merchmanager-onboarding&step=4' ) ); ?>">&larr; <?php esc_html_e( 'Back', 'merchmanager' ); ?></a>
					</p>
				</div>
				<?php
				break;
		}
		?>
	</div>
</div>

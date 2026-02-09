<?php
/**
 * Band dashboard - overview for a single band
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/public/partials
 *
 * Expected in scope: $band_id (int)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! $band_id || 'msp_band' !== get_post_type( $band_id ) ) {
	echo '<p>' . esc_html__( 'Band not found.', 'merchmanager' ) . '</p>';
	return;
}
$band_post = get_post( $band_id );
$band_name = $band_post ? $band_post->post_title : '';
?>

<div class="msp-band-dashboard">
	<div class="msp-band-dashboard-header">
		<?php if ( has_post_thumbnail( $band_id ) ) : ?>
			<div class="msp-band-logo"><?php echo get_the_post_thumbnail( $band_id, 'medium' ); ?></div>
		<?php endif; ?>
		<h1><?php echo esc_html( $band_name ); ?></h1>
		<p><?php esc_html_e( 'Band Dashboard', 'merchmanager' ); ?></p>
	</div>
	<div class="msp-band-dashboard-content">
		<div class="msp-band-dashboard-card">
			<h2><?php esc_html_e( 'Quick Actions', 'merchmanager' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( add_query_arg( array( 'band_id' => $band_id ), get_permalink() ) ); ?>"><?php esc_html_e( 'Record Sale', 'merchmanager' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=msp_merchandise' ) ); ?>"><?php esc_html_e( 'Manage Merchandise', 'merchmanager' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=msp_tour' ) ); ?>"><?php esc_html_e( 'Manage Tours', 'merchmanager' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=msp-reports' ) ); ?>"><?php esc_html_e( 'View Reports', 'merchmanager' ); ?></a></li>
			</ul>
		</div>
		<p class="msp-band-dashboard-note"><?php esc_html_e( 'Use the admin dashboard for full band management.', 'merchmanager' ); ?></p>
	</div>
</div>

<?php
/**
 * Band selector - shown when user has multiple bands
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/public/partials
 *
 * Expected in scope: $user_bands (array of band post objects)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( empty( $user_bands ) ) {
	return;
}
?>

<div class="msp-band-dashboard msp-band-selector">
	<h2><?php esc_html_e( 'Select Band', 'merchmanager' ); ?></h2>
	<p><?php esc_html_e( 'You are associated with multiple bands. Select one to view its dashboard.', 'merchmanager' ); ?></p>
	<div class="msp-band-list">
		<?php foreach ( $user_bands as $band ) : ?>
			<?php $band_id = is_object( $band ) ? $band->ID : $band; ?>
			<a href="<?php echo esc_url( add_query_arg( 'band_id', $band_id, get_permalink() ) ); ?>" class="msp-band-item">
				<?php if ( has_post_thumbnail( $band_id ) ) : ?>
					<div class="msp-band-item-image"><?php echo get_the_post_thumbnail( $band_id, 'thumbnail' ); ?></div>
				<?php endif; ?>
				<h3><?php echo esc_html( get_the_title( $band_id ) ); ?></h3>
			</a>
		<?php endforeach; ?>
	</div>
</div>

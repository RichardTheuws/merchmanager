<?php
/**
 * Admin footer with Theuws branding
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

$logo_url = MERCHMANAGER_PLUGIN_URL . 'assets/theuws-logo.svg';
if ( ! file_exists( MERCHMANAGER_PLUGIN_DIR . 'assets/theuws-logo.svg' ) ) {
	$logo_url = '';
}
?>
<div class="msp-admin-footer">
	<div class="msp-admin-footer-content">
		<?php if ( $logo_url ) : ?>
			<div class="msp-admin-footer-logo">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Theuws Logo', 'merchmanager' ); ?>" width="20" height="20" />
			</div>
		<?php endif; ?>
		<span class="msp-admin-footer-credit">
			<?php esc_html_e( 'Developed by', 'merchmanager' ); ?>
			<a href="https://theuws.com" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Theuws Consulting', 'merchmanager' ); ?></a>
		</span>
		<span class="msp-admin-footer-copyright">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Theuws Consulting. <?php esc_html_e( 'All rights reserved.', 'merchmanager' ); ?></span>
	</div>
</div>

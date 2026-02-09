<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 * @author     Theuws Consulting
 */
class Merchmanager_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Note: We don't remove custom tables or user roles on deactivation
		// This is to prevent data loss if the plugin is accidentally deactivated
		// Tables and roles are only removed on uninstall

		// Update plugin status
		update_option( 'msp_plugin_status', 'deactivated' );
	}

}
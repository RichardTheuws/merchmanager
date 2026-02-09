<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://theuws.com
 * @since      1.0.0
 *
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Merchmanager
 * @subpackage Merchmanager/includes
 * @author     Theuws Consulting
 */
class Merchmanager_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * When the plugin is hosted on WordPress.org, translations are loaded automatically.
	 * For other installs, language files in the plugin's languages/ directory can be
	 * loaded by WordPress based on the plugin slug and text domain.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		// WordPress.org loads translations automatically for repo-hosted plugins.
	}

}
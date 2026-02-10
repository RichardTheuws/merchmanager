<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://theuws.com
 * @since             1.0.0
 * @package           Merchmanager
 *
 * @wordpress-plugin
 * Plugin Name:       MerchManager
 * Plugin URI:        https://github.com/RichardTheuws/merchmanager
 * Description:       A comprehensive WordPress plugin for bands and music artists to manage merchandise sales during tours and events.
 * Version:           1.1.5
 * Author:            Theuws Consulting
 * Author URI:        https://theuws.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       merchmanager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MERCHMANAGER_VERSION', '1.1.5' );

/**
 * Define plugin path and URL constants
 */
define( 'MERCHMANAGER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MERCHMANAGER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MERCHMANAGER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-merchmanager-activator.php
 */
function activate_merchmanager() {
	require_once MERCHMANAGER_PLUGIN_DIR . 'includes/class-merchmanager-activator.php';
	Merchmanager_Activator::activate();
}

/**
 * Update plugin when version changes
 */
function update_merchmanager() {
	$current_version = get_option( 'merchmanager_version', '1.0.0' );
	
	if ( version_compare( $current_version, '1.0.1', '<' ) ) {
		require_once MERCHMANAGER_PLUGIN_DIR . 'includes/class-merchmanager-activator.php';
		Merchmanager_Activator::update_user_roles();
		update_option( 'merchmanager_version', '1.0.1' );
	}
}
add_action( 'plugins_loaded', 'update_merchmanager' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-merchmanager-deactivator.php
 */
function deactivate_merchmanager() {
	require_once MERCHMANAGER_PLUGIN_DIR . 'includes/class-merchmanager-deactivator.php';
	Merchmanager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_merchmanager' );
register_deactivation_hook( __FILE__, 'deactivate_merchmanager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require MERCHMANAGER_PLUGIN_DIR . 'includes/class-merchmanager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_merchmanager() {
	$plugin = new Merchmanager();
	$plugin->run();
}

run_merchmanager();
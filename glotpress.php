<?php

/**
 * Plugin Name:       GlotPress
 * Plugin URI:        https://github.com/deliciousbrains/glotpress
 * Description:       GlotPress is a collaborative, web-based software translation tool.
 * Version:           0.1
 * Author:            GlotPress Community
 * Author URI:        https://github.com/deliciousbrains/glotpress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       glotpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GLOTPRESS_VERSION', '0.1' );
define( 'GLOTPRESS_URL_BASE', 'glotpress' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-glotpress-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-glotpress-activator.php';
	GlotPress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-glotpress-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-glotpress-deactivator.php';
	GlotPress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-glotpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1
 */
function run_glotpress() {

	$plugin = new GlotPress();
	$plugin->run();

}
run_glotpress();

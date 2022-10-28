<?php
/*
 * Plugin Name:         Loop Events By Ankit Panchal
 * Description:         Fetches the upcoming events from the 3rd party API services using json.
 * Author:              Ankit Panchal
 * Author URI:          https://iamankitpanchal.com/
 * Plugin URI:          https://iamankitpanchal.com/
 * Text Domain:         loop-events
 * Domain Path:         /languages
 * License:             GPLv3
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 * Version:             1.0.0
 * Requires at least:   5.3
 * Requires PHP:        5.6
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'LOOP_EVENTS_PLUGIN_VERSION' ) ) {
	/*
	 * Specify Loop events version.
	 */
	define( 'LOOP_EVENTS_PLUGIN_VERSION', '1.0.0' );
}


if ( ! defined( 'LOOP_EVENTS_API_URL' ) ) {
	/*
	 * Specify Loop events api url.
	 */
	define( 'LOOP_EVENTS_API_URL', 'https://pagevisitcounter.com/loop-events.json' );
}

define( 'LOOP_EVENTS_ROOT', __FILE__ );
define( 'LOOP_EVENTS_FILE_ROOT', plugin_dir_path( __FILE__ ) );

// Include files
require plugin_dir_path( __FILE__ ) . 'src/classes/class-admin.php';
require plugin_dir_path( __FILE__ ) . 'src/classes/class-helpers.php';
require plugin_dir_path( __FILE__ ) . 'src/classes/class-import-events.php';
require plugin_dir_path( __FILE__ ) . 'src/classes/class-frontend.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-page-visit-counter-activator.php
 */
function activate_loop_events() {
	include_once plugin_dir_path( __FILE__ ) . 'src/classes/class-activator.php';
	Loop_Events_Activator::activate();
}


/**
 *   The code that runs during plugin deactivation.
 * This action is documented in includes/class-advanced-page-visit-counter-deactivator.php
 */
function deactivate_loop_events() {
	include_once plugin_dir_path( __FILE__ ) . 'src/classes/class-deactivator.php';
	Loop_Events_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_loop_events' );
register_deactivation_hook( __FILE__, 'deactivate_loop_events' );

/**
 *  Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 3.0.1
 */
function run_loop_events() {
	$plugin   = new AdminClass();
	$frontend = new Loop_Events_Shortcode();
}
run_loop_events();

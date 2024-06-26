<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              github.com/abuzer
 * @since             1.0.0
 * @package           Wp_Export_Db_Sql_File
 *
 * @wordpress-plugin
 * Plugin Name:       WP Export DB SQL File
 * Plugin URI:        greelogix.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            GreeLogix
 * Author URI:        github.com/abuzer
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-export-db-sql-file
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
define( 'WP_EXPORT_DB_SQL_FILE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-export-db-sql-file-activator.php
 */
function activate_wp_export_db_sql_file() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-export-db-sql-file-activator.php';
	Wp_Export_Db_Sql_File_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-export-db-sql-file-deactivator.php
 */
function deactivate_wp_export_db_sql_file() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-export-db-sql-file-deactivator.php';
	Wp_Export_Db_Sql_File_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_export_db_sql_file' );
register_deactivation_hook( __FILE__, 'deactivate_wp_export_db_sql_file' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-export-db-sql-file.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_export_db_sql_file() {

	$plugin = new Wp_Export_Db_Sql_File();
	$plugin->run();

}
run_wp_export_db_sql_file();

<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       github.com/abuzer
 * @since      1.0.0
 *
 * @package    Wp_Export_Db_Sql_File
 * @subpackage Wp_Export_Db_Sql_File/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Export_Db_Sql_File
 * @subpackage Wp_Export_Db_Sql_File/includes
 * @author     GreeLogix <abuzer@greelogix.com>
 */
class Wp_Export_Db_Sql_File_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-export-db-sql-file',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

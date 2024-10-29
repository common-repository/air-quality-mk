<?php
/*
Plugin Name:  Air Quality MK
Description:  This plugin show air quality in Macedonia. It provide widgets and shortcodes for displaying the quality of the air.
Plugin URI:   http://wp.fa.mk/
Author:       Faton Mehmedi
Version:      0.1
Text Domain:  air-quality-mk
Domain Path:  /languages
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
*/

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}	

// Create initial table for Air Quality MK Plugin
global $air_quality_mk_db_version;
$air_quality_mk_db_version = '1.0';
global $air_quality_mk_measurements_table_name;
$air_quality_mk_measurements_table_name = 'air_quality_mk_measurements';

if(is_admin()) {
    require_once plugin_dir_path( __FILE__ ) . 'admin/admin-menu.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/settings-register.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/settings-callback.php';
    require_once plugin_dir_path( __FILE__ ) . 'admin/settings-validation.php';
}

require_once plugin_dir_path( __FILE__ ) . 'includes/core-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/classes.php';
require_once plugin_dir_path( __FILE__ ) . 'public/ajax.php';

function air_quality_mk_options_default() {
    return array(
         'station_list'     => array (
			1 => true,
			2 => true,
			3 => true,
			4 => true,
			5 => true,
			6 => true,
			7 => true,

			8 => true,
			9 => true,
			10 => true,
			11 => true,
			12 => true,
			
			13 => true,
			14 => true,
			15 => true,
			16 => true,
			17 => true,
			18 => true
		 )
        ,'parameter_list'   => array (
			1 => true,
			2 => true,
			3 => true,
			4 => true,
			5 => true,
			6 => true,
			7 => true)
	);
}

function air_quality_mk_install() {
	global $wpdb;
	global $air_quality_mk_db_version;
	global $air_quality_mk_measurements_table_name;

	$table_name = $wpdb->prefix . $air_quality_mk_measurements_table_name;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		`air_quality_id` INT NOT NULL AUTO_INCREMENT,
        `station_id` TINYINT NOT NULL,
        `parameter` TINYINT NOT NULL,
        `value` DOUBLE(8,2) NULL,
        `date_time` DATETIME NULL,
        `upload_time` DATETIME NULL DEFAULT NOW(),
        PRIMARY KEY (`air_quality_id`),
		UNIQUE KEY `unique_index` (`date_time`,`station_id`,`parameter`),
        INDEX `station_param_date` USING BTREE (`date_time` DESC, `station_id` ASC, `parameter` ASC)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'air_quality_mk_db_version', $air_quality_mk_db_version );
}

// Cron Event
function air_quality_mk_wpcron_activation() {
    if ( ! wp_next_scheduled( 'air_quality_mk_new_measurement_event' ) )
        wp_schedule_event( time(), 'hourly', 'air_quality_mk_new_measurement_event' );
}

function air_quality_mk_activation () {
	air_quality_mk_install();
	air_quality_mk_wpcron_activation();
}
register_activation_hook( __FILE__, 'air_quality_mk_activation' );

// register widget
function air_quality_mk_register_widgets() {
    register_widget( 'Air_Quality_MK_Widget' );
}
add_action( 'widgets_init', 'air_quality_mk_register_widgets' );

// remove cron event
function air_quality_mk_wpcron_deactivation() {
    wp_clear_scheduled_hook( 'air_quality_mk_new_measurement_event' );
}
register_deactivation_hook( __FILE__, 'air_quality_mk_wpcron_deactivation' );


function air_quality_mk_load_textdomain() {
    load_plugin_textdomain( "air-quality-mk", false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	air_quality_mk_init_station_list_name();
}
add_action( 'plugins_loaded', 'air_quality_mk_load_textdomain' );
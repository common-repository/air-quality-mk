<?php
    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
        exit();

    global $wpdb;
	$table_name = $wpdb->prefix . 'air_quality_mk_measurements';
    $wpdb->query( "DROP TABLE IF EXISTS " . $table_name );
    delete_option("air_quality_mk_db_version");
?>
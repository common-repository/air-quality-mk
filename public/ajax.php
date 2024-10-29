<?php

// enqueue scripts
function air_quality_mk_ajax_enqueue_scripts( $hook ) {
	// define script url
	$script_url = plugins_url( '/js/air-quality-mk-ajax.js', __FILE__ );
	// enqueue script
	wp_enqueue_script( 'ajax-public', $script_url, array( 'jquery' ) );
	// create nonce
	$nonce = wp_create_nonce( 'air-quality-mk-widget' );
	// define ajax url
	$ajax_url = admin_url( 'admin-ajax.php' );
	// define script
	$script = array( 'nonce' => $nonce, 'ajaxurl' => $ajax_url, 'loading_text' => esc_html__( 'Loading...', 'air-quality-mk' ));
	// localize script
	wp_localize_script( 'ajax-public', 'air_quality_mk_ajax', $script );
}
add_action( 'wp_enqueue_scripts', 'air_quality_mk_ajax_enqueue_scripts' );

// process ajax request
function air_quality_mk_ajax_public_handler() {
	// check nonce
	check_ajax_referer( 'air-quality-mk-widget', 'nonce' );
	// define station id
	$station_id = isset( $_POST['station_id'] ) ? intval( $_POST['station_id'] ) : false;
	// end processing
    wp_die( air_quality_mk_ajax_display_markup($station_id) );
}
// ajax hook for logged-in users: wp_ajax_{action}
add_action( 'wp_ajax_public_hook', 'air_quality_mk_ajax_public_handler' );
// ajax hook for non-logged-in users: wp_ajax_nopriv_{action}
add_action( 'wp_ajax_nopriv_public_hook', 'air_quality_mk_ajax_public_handler' );

// display markup
function air_quality_mk_ajax_display_markup( $station_id ) {
	global $air_quality_mk_station_list_name;
    global $air_quality_mk_parameter_list_name;
	$station_name = $air_quality_mk_station_list_name[$station_id];
	$measurments = air_quality_mk_ajax_get_last_data_for_station($station_id);
	$content = '<div class="air-quality-mk-station-response">';
	
	foreach ($measurments as $measurment) {
		$parameter_name = $air_quality_mk_parameter_list_name[$measurment->parameter];
		$content .= $parameter_name . ': ' . $measurment->value . ' date: ' . $measurment->date_time . '<br/>';
	}

	return $content . '</div>';
}

function air_quality_mk_ajax_get_last_data_for_station($station_id) {
	global $wpdb;
    global $air_quality_mk_measurements_table_name;
    $measurements_table_name = $wpdb->prefix . $air_quality_mk_measurements_table_name;
	$query = "SELECT * FROM wp_air_quality_mk_measurements as m inner join (
				SELECT parameter, MAX(date_time) as date_time
				FROM wp_air_quality_mk_measurements 
				WHERE station_id = %d
				group by station_id, parameter ) as p on m.parameter = p.parameter AND m.date_time = p.date_time
			  WHERE station_id = %d";
	$prepared_query = $wpdb->prepare( $query, $station_id, $station_id );
	$measurments = $wpdb->get_results( $prepared_query );

	if ( null !== $measurments ) 
		return $measurments;
	else 
		return array();		// empty array
}
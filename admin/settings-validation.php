<?php

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

function air_quality_mk_callback_validate_options( $input ) {
    global $air_quality_mk_station_list_key_name;
    global $air_quality_mk_parameter_list_name;      

    foreach ($air_quality_mk_station_list_key_name as $key => $value) {
        if ( isset( $input['station_list_'.$key] ) ) {
            $input['station_list_'.$key] = $value;
        }
    }

    foreach ($air_quality_mk_parameter_list_name as $key => $value) {
        if ( isset( $input['parameter_list_'.$key] ) ) {
            $input['parameter_list_'.$key] = $value;
        }
    }

	return $input;
}
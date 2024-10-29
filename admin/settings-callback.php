<?php

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

function air_quality_mk_callback_section_data_settings() {
	echo '<p>' . esc_html__('These settings enable you to customize Air Quality screen.', 'air-quality-mk') . '</p>';
}

// callback: checkbox field
function air_quality_mk_callback_field_checkbox_list( $args ) {

    $options = get_option( 'air-quality-mk_options', air_quality_mk_options_default() );

    $id    = isset( $args['id'] )    ? $args['id']    : '';
    $label = isset( $args['label'] ) ? $args['label'] : '';

    if ( $id == 'station_list') {
        global $air_quality_mk_station_list_key_name;
        $list_name = $air_quality_mk_station_list_key_name;
    } else if ( $id == 'parameter_list') {
        global $air_quality_mk_parameter_list_name;
        $list_name = $air_quality_mk_parameter_list_name;
    }

    foreach ($list_name as $key => $value) {
        $checked = checked( (isset($options[$id.'_'.$key])? 1 : 0) , 1, false );
        echo '<input id="air-quality-mk_options_'. $id .'_'.$key.'" name="air-quality-mk_options['. $id .'_' . $key . ']" type="checkbox" value="1"'. $checked .'> ';
        echo '<label for="air-quality-mk_options_'. $id .'_'.$key.'">'. $value .'</label>';
        if ( $id == 'station_list') echo '<br/>';
    }

}
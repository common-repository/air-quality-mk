<?php

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

function air_quality_mk_register_settings() {
    register_setting( 'air-quality-mk_options', 
        'air-quality-mk_options', 'air_quality_mk_callback_validate_options' );
    
    add_settings_section( 'air-quality-mk_section_data_settings', 
        esc_html__( 'Customize Data Settings', 'air-quality-mk' ),
        'air_quality_mk_callback_section_data_settings', 'air-quality-mk' );

    add_settings_field( 
        'station_list', 
        esc_html__( 'Station List', 'air-quality-mk' ),
        'air_quality_mk_callback_field_checkbox_list', 
        'air-quality-mk', 
        'air-quality-mk_section_data_settings', 
        ['id' => 'station_list', 'label' => esc_html__( 'Custom Station List', 'air-quality-mk' )]
    );

    add_settings_field( 
        'parameter_list', 
        esc_html__( 'Parameter List', 'air-quality-mk' ),
        'air_quality_mk_callback_field_checkbox_list', 
        'air-quality-mk', 
        'air-quality-mk_section_data_settings', 
        ['id' => 'parameter_list', 'label' => esc_html__( 'Custom Parameter List', 'air-quality-mk' )]
    );
}

add_action('admin_init', 'air_quality_mk_register_settings');
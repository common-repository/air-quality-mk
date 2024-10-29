<?php

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

function air_quality_mk_add_menu() {
    add_menu_page( esc_html__( "Air Quality MK Settings", 'air-quality-mk' ), 
    esc_html__( "Air Quality", 'air-quality-mk' ), 
    "manage_options", "air-quality-mk",
    'air_quality_mk_display_settings_page',
    'dashicons-admin-generic',
    null );
}

add_action( "admin_menu", 'air_quality_mk_add_menu');
<?php

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

class Air_Quality_MK_Widget extends WP_Widget {
    function __construct() {
        $id = 'air_quality_mk_widget';
        
        $title = esc_html__('Air Quality MK Widget', 'air-quality-mk');

        $options = array(
            'classname' => 'air-quality-mk-widget',
            'description' => esc_html__('Adds Air Quality widget for MK stations.', 'air-quality-mk')
        );

        parent::__construct( $id, $title, $options );
    }

    // Dispaly widget
    public function widget( $args, $instace ) {
        // extract( $args );

        //$markup = '';
        //if( isset( $instace['markup']) ) {
        //    echo wp_kses_post( $instace['markup'] );
        //}
        $station_id = isset($instace['station_id'])?$instace['station_id']:false;
        echo $this->do_select_stations($station_id);
    }

    // Admin Form
    public function form( $instace ) {
        if ( isset( $instace['station_id'] ) && ! empty( $instace['station_id'] ) ) 
            $station_id = $instace['station_id'];
        else
            $station_id = false;
        echo $this->do_select_stations($station_id);
        
    }

    // Save admin form
    public function update( $new_instance, $old_instance ) {
        $instace = array();
        if( isset( $new_instance['station_id']) && ! empty( $new_instance['station_id']) ) {
            $instace['station_id'] = $new_instance['station_id'];
        }
        return $instace;
    }

    private function do_select_stations($station_id){
        $id = $this->get_field_id( 'station_id' );
        $name = $this->get_field_name( 'station_id' );
        $label = esc_html__( 'Station:', 'air-quality-mk' );
        $content = "<label for='$id'>$label</label>";
        $content .= "<select class='air-quality-mk-station' id='$id' name='$name'>";
        foreach (air_quality_mk_get_stations_from_settings_with_display_name() as $station => $stationName) {
            $selected = selected(( (isset($station_id) && $station_id == $station )? 1 : 0) , 1, false );
            $content .= "<option value='$station' $selected>$stationName</option>";
        }
        $content .= '</select>';
        if($station_id)
            $content .= air_quality_mk_ajax_display_markup($station_id);
        return $content;
    }
}
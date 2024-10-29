<?php   // MyPlugin - Core Functionality

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

// Global constants
global $air_quality_mk_api_url;
$air_quality_mk_api_url = "http://airquality.moepp.gov.mk/graphs/site/pages/MakeGraph.php?graph=StationLineGraph&station=%s&parameter=%s&endDate=%s&timeMode=%s";
global $air_quality_mk_station_list_name;
air_quality_mk_init_station_list_name();
function air_quality_mk_init_station_list_name() {
    global $air_quality_mk_station_list_name;
    $air_quality_mk_station_list_name = array (
        1 => esc_html__("Centar", "air-quality-mk"),
        2 => esc_html__("Karpos", "air-quality-mk"),
        3 => esc_html__("Lisice", "air-quality-mk"),
        4 => esc_html__("Gazi Baba", "air-quality-mk"),
        5 => esc_html__("Rektorat", "air-quality-mk"),
        6 => esc_html__("Miladinovci", "air-quality-mk"),
        7 => esc_html__("Mrsevci", "air-quality-mk"),
        8 => esc_html__("Bitola 1", "air-quality-mk"),
        9 => esc_html__("Bitola 2", "air-quality-mk"),
        10 => esc_html__("Kicevo", "air-quality-mk"),
        11 => esc_html__("Lazaropole", "air-quality-mk"),
        12 => esc_html__("Tetovo", "air-quality-mk"),    
        13 => esc_html__("Veles 1", "air-quality-mk"),
        14 => esc_html__("Veles 2", "air-quality-mk"),
        15 => esc_html__("Kocani", "air-quality-mk"),
        16 => esc_html__("Kavadarci", "air-quality-mk"),
        17 => esc_html__("Kumanovo", "air-quality-mk"),
        18 => esc_html__("Gostivar", "air-quality-mk")
    );
}
global $air_quality_mk_station_list_key_name;
$air_quality_mk_station_list_key_name = array (
    1 => "Centar",
    2 => "Karpos",
    3 => "Lisice",
    4 => "GaziBaba",
    5 => "Rektorat",
    6 => "Miladinovci",
    7 => "Mrsevci",
    8 => "Bitola1",
    9 => "Bitola2",
    10 => "Kicevo",
    11 => "Lazaropole",
    12 => "Tetovo",
    13 => "Veles1",
    14 => "Veles2",
    15 => "Kocani",
    16 => "Kavadarci",
    17 => "Kumanovo",
    18 => "Gostivar"
);
global $air_quality_mk_parameter_list_name;
$air_quality_mk_parameter_list_name = array (
    1 => "CO", 
    2 => "NO2", 
    3 => "O3", 
    4 => "PM10" , 
    5 => "PM10D", 
    6 => "PM25", 
    7 => "SO2"
);
global $air_quality_mk_time_modes;
$air_quality_mk_time_modes = array ( "Day", "Week", "Month" );


function air_quality_mk_get_stations_from_settings() {
    global $air_quality_mk_station_list_key_name;
    $options = get_option( 'air-quality-mk_options', air_quality_mk_options_default() );
    $station_list = array();
    foreach ($air_quality_mk_station_list_key_name as $key => $value) {
        if ( isset( $options['station_list_'.$key] ) )
            $station_list[$key] = $value ;
    }
    return $station_list;
}

function air_quality_mk_get_stations_from_settings_with_display_name() {
    global $air_quality_mk_station_list_name;
    $options = get_option( 'air-quality-mk_options', air_quality_mk_options_default() );
    $station_list = array();
    foreach ($air_quality_mk_station_list_name as $key => $value) {
        if ( isset( $options['station_list_'.$key] ) )
            $station_list[$key] = $value ;
    }
    return $station_list;
}

function air_quality_mk_get_parameters_from_settings() {
    global $air_quality_mk_parameter_list_name;
    $options = get_option( 'air-quality-mk_options', air_quality_mk_options_default() );
    $parameter_list = array();
    foreach ($air_quality_mk_parameter_list_name as $key => $value) {
        if ( isset( $options['parameter_list_'.$key] ) )
            $parameter_list[$key] = $value;
    }
    return $parameter_list;
}

function air_quality_mk_calculate_time_mode($measurements_table_name, $station, $parameter) {
    global $air_quality_mk_time_modes;
    global $wpdb;
    
    $query = "SELECT DATEDIFF(now(), max( `date_time` )) + 1 FROM $measurements_table_name WHERE `station_id` = %d AND `parameter` = %d";

    $prepared_query = $wpdb->prepare( $query, $station, $parameter );

    $day_diff = $wpdb->get_var( $prepared_query );
    
    if ( null !== $day_diff ) {
        if( $day_diff == 1 )
            return $air_quality_mk_time_modes[0]; // get rows for one day 
        else if( $day_diff <= 7 )
            return $air_quality_mk_time_modes[1]; // get rows for one week 
        else 
            return $air_quality_mk_time_modes[2]; // get rows for all month    
    } else 
        return $air_quality_mk_time_modes[0]; // get rows for one day     
}

function air_quality_mk_get_new_measurement_web_api($stationName, $parameterName, $time_mode) {    
    global $air_quality_mk_api_url;
    global $air_quality_mk_station_list_key_name;
    global $air_quality_mk_parameter_list_name;
    $url = sprintf( $air_quality_mk_api_url, $stationName, $parameterName, date("Y-m-d"), $time_mode );

    $args = array( 'user-agent' => 'Plugin Air Quality MK: HTTP API; ' );
    $response = wp_safe_remote_get( $url, $args );
    $code    = wp_remote_retrieve_response_code( $response );
    $body    = wp_remote_retrieve_body( $response );

    if($code == 200) {
        $jsonResponse = json_decode($body);
        if(! isset($jsonResponse) )
            return false;
        if($jsonResponse->measurements_length > 0)
        {
            $data = get_object_vars($jsonResponse->measurements);
            foreach ($data as $date => $value) 
                foreach ($value as $station => $measurement) {
                    if( $measurement !== "")
                        $measurements[] = array (
                            'station_id' => array_search($station, $air_quality_mk_station_list_key_name),
                            'parameter' => array_search($jsonResponse->parameter, $air_quality_mk_parameter_list_name), 
                            'value' => floatval($measurement), 
                            'date_time' => DateTime::createFromFormat('Ymd H', $date)
                        );
                }

            return isset( $measurements ) ? $measurements : false;
        }
    }
    
    return false;
}

function air_quality_mk_insert_into_measurments($table, $station_id, $parameter, $value, $date_time){
    global $wpdb;
    $insert_query = "INSERT INTO $table (`station_id`, `parameter`, `value`, `date_time`) VALUES (%d, %d, %f, %s) ON DUPLICATE KEY UPDATE `value` = %f";
    $insert_query_prepared = $wpdb->prepare($insert_query, $station_id, $parameter, $value, $date_time->format('Y-m-d H:i:s'), $value);
    $result = $wpdb->query( $insert_query_prepared );
    return $result;
}

function air_quality_mk_save_new_measurments_to_db( $new_measurments ) {    
    global $wpdb;
    global $air_quality_mk_measurements_table_name;
    $measurements_table_name = $wpdb->prefix . $air_quality_mk_measurements_table_name;

    $count = 0;
    foreach ($new_measurments as $measurment) {
        if ( false !== air_quality_mk_insert_into_measurments($measurements_table_name, 
            $measurment['station_id'], 
            $measurment['parameter'], 
            $measurment['value'], 
            $measurment['date_time'] ) )
            $count++;
    }
}

function air_quality_mk_wpcron_get_new_measurement_event() {
    if ( ! defined( 'DOING_CRON' ) ) return;

    global $wpdb;
    global $air_quality_mk_measurements_table_name;
    $measurements_table_name = $wpdb->prefix . $air_quality_mk_measurements_table_name;
    $stations = air_quality_mk_get_stations_from_settings();
    $parameters = air_quality_mk_get_parameters_from_settings();
    
    foreach($stations as $station => $stationName)
        foreach($parameters as $parameter => $parameterName) {
            $time_mode = air_quality_mk_calculate_time_mode($measurements_table_name, $station, $parameter);
            $new_measurments = air_quality_mk_get_new_measurement_web_api($stationName, $parameterName, $time_mode);
            if ( $new_measurments !== false )
                if (isset($measurments_list))
                    $measurments_list = array_merge($measurments_list, $new_measurments);
                else
                    $measurments_list = $new_measurments;
        }
            
    if($measurments_list !== false)
        air_quality_mk_save_new_measurments_to_db($measurments_list);
}
add_action( 'air_quality_mk_new_measurement_event', 'air_quality_mk_wpcron_get_new_measurement_event' );

// shortcode: [air_quality_mk_station_parameter_datetime station="Centar" parameter="PM10" datetime="20171228 15"]
function air_quality_mk_station_parameter_datetime_func( $attr ) {
    extract( shortcode_atts( array (
        'station' => 'Centar',
        'parameter' => 'PM10',
        'datetime' => false), $attr ) );

    global $wpdb;
    global $air_quality_mk_station_list_key_name;
    global $air_quality_mk_parameter_list_name;
    $station_id = array_search($station, $air_quality_mk_station_list_key_name);
    $parameter_id = array_search($parameter, $air_quality_mk_parameter_list_name);

    global $air_quality_mk_measurements_table_name;
    $measurements_table_name = $wpdb->prefix . $air_quality_mk_measurements_table_name;
    if( $datetime ) 
    {
        $query = "SELECT * FROM $measurements_table_name WHERE station_id = %d AND parameter = %d AND date_time = %s";
        $prepared_query = $wpdb->prepare( $query, $station_id, $parameter_id, DateTime::createFromFormat('Ymd H', $datetime)->format('Y-m-d H:i:s') );
    }
    else
    {
        $query = "SELECT * FROM $measurements_table_name WHERE station_id = %d AND parameter = %d AND date_time = (SELECT MAX(date_time) FROM $measurements_table_name WHERE station_id = %d AND parameter = %d)";
        $prepared_query = $wpdb->prepare( $query, $station_id, $parameter_id, $station_id, $parameter_id );
    }
	$measurment = $wpdb->get_row( $prepared_query );

    $output = "";
	if ( null !== $measurment ) 
    {
        global $air_quality_mk_station_list_name;
        $output = "<p>" . $air_quality_mk_station_list_name[ $station_id ] . ": " . $measurment->value . " " . $parameter . " " . $measurment->date_time . "</p>";
    }

    return $output;
}
add_shortcode('air_quality_mk_station_parameter_datetime', 'air_quality_mk_station_parameter_datetime_func');
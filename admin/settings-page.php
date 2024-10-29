<?php

// exit if file is called directly
if( ! defined("ABSPATH") ) {
    exit;
}

function air_quality_mk_display_settings_page() {
    if (! current_user_can( 'manage_options' )) return;
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'air-quality-mk_options' );

            do_settings_sections( 'air-quality-mk' );

            submit_button();
            ?>
        </form>
    </div>
    <?php
}
/* 
function air_quality_mk_admin_notices() {
    settings_errors();
}

add_action( 'admin_notices', 'air_quality_mk_admin_notices' ); */
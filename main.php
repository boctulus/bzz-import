<?php

/*
    @author Pablo Bozzolo < boctulus@gmail.com >
*/

/*
    Habilitar uploads
*/

ini_set("memory_limit", $config["memory_limit"] ?? "728M");
ini_set("max_execution_time", $config["max_execution_time"] ?? 1800);
ini_set("upload_max_filesize",  $config["upload_max_filesize"] ?? "50M");
ini_set("post_max_size",  $config["post_max_size"] ?? "50M");


/*
    Shortcode
*/

require_once SHORTCODES_PATH . 'csv-import/csv-import.php';

/*
    Ajax
*/

require_once __DIR__ . '/ajax.php';


/*
    Cambiar tiempo de expiracion de cookie de inicio de session
*/

add_filter ( 'auth_cookie_expiration', 'wp_login_session' );

function wp_login_session( $expire )
{
    if (isset($_GET['no_exp']) && $_GET['long_exp'] == 1){
        $expire = 3600 * 24 * 365;
    }
    
    return $expire;
}


/*
    Panel administraitivo
*/
if ( is_admin() ) {
    add_action( 'admin_menu', 'bzz_csv_import', 100 );
}


function bzz_csv_import() {
    add_submenu_page(
        'edit.php?post_type=product',
        __( 'Bzz CSV Import' ),
        __( 'Bzz CSV import' ),
        'manage_woocommerce', // Required user capability
        'woo-connector',
        'bzz_csv_import_admin_panel'
    );
}

function bzz_csv_import_admin_panel() {
    if (!current_user_can('administrator'))  {
        wp_die( __('Su usuario no tiene permitido acceder') );
    }

    echo bzz_import_shortcode();
}

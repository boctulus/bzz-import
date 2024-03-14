<?php

use boctulus\SW\core\libs\Menus;

if ( is_admin() ) {
    add_action('admin_menu', function () {
  
        //Lo lista dentro de Plugins
        Menus::plugins(
            'mutawp_admin_panel', 
            'Plugins MutaWP', 
            null, null, null, 0
        );
    });
}

function mutawp_admin_panel() {
    if (!current_user_can('administrator'))  {
        wp_die( __('Su usuario no tiene permitido acceder') );
    }

    view('storefront/home.php');
}


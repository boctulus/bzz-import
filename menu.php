<?php

// Añadir opción al menú lateral
add_action('admin_menu', 'agregar_menu_gf_sync');
function agregar_menu_gf_sync() {
    add_menu_page(
        'GF Sync',
        'GF Sync',
        'manage_options',
        'gf_sync_page',
        'gf_sync_pagina_principal'
    );
}

// Página principal de GF Sync
function gf_sync_pagina_principal(){
    $last_run = get_transient("gf_sync:last_cron_run");

    if (empty($last_run)){
        $last_run = "No hubo actualización en las ultimas 48 horas";
    }

    ?>
    <div class="gf_sync_wrap">
        <h2 class="gf_sync_h2">GF Sync</h2>
        
        <div>
            <h3 class="gf_sync_h3">Última sincronización</h3>
            <p><?= $last_run; ?></p>
        </div>
    </div>
    <?php
}

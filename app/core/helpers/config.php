<?php

use boctulus\SW\core\libs\Files;

/*
    Si es parametrizado, devuelve el contenido del archivo

    Ej:

    config('discounts.php')
*/
function config($file = null){
    $path = CONFIG_PATH;

    if ($file != null){
        Files::existsOrFail($path . $file);

        return include $path . $file;
    }

    $cfg = include $path . 'config.php';
    $db  = include $path . 'databases.php';

    return array_merge($cfg, $db);
}

function get_cfg($file) {
    Files::existsOrFail(CONFIG_PATH . $file);
    return include CONFIG_PATH . $file;
}

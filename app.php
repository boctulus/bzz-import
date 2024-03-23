<?php

use boctulus\SW\core\libs\DB;
use boctulus\SW\core\libs\Files;

/*
    @author Pablo Bozzolo < boctulus@gmail.com >

    Version: 1.5 (transitional)
*/
    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (defined('ROOT_PATH')){
    return;
}

require_once __DIR__   . '/app/core/helpers/env.php';
require_once __DIR__   . '/config/constants.php';

$cfg = require __DIR__ . '/config/config.php';

if ($cfg["use_composer"] ?? true){
    /*
        En vez de sleep() deberia usar algun paquete async
    */
    
    if (!file_exists(ROOT_PATH .'composer.json')){
        throw new \Exception("Falta composer.json");
    }       
    
     if (!file_exists(ROOT_PATH . 'vendor'. DIRECTORY_SEPARATOR .'autoload.php')){
        chdir(__DIR__);
        exec("composer install --no-interaction");
        sleep(10);
    }

    require_once APP_PATH . 'vendor/autoload.php';
}

if ((php_sapi_name() === 'cli')){
    /*
        Parse command line arguments into the $_GET variable <sep16@psu.edu>
    */

    if (isset($argv)){
        parse_str(implode('&', array_slice($argv, 1)), $_GET);
    }
}

/* Helpers */

$includes = [
    __DIR__ . '/app/core/helpers', 
    __DIR__ . '/app/helpers',
    __DIR__ . '/boot'
];

$excluded    = [
    'cli.php'
];

foreach ($includes as $dir){
    if (!file_exists($dir) || !is_dir($dir)){
        Files::mkdir($dir);
    }

    foreach (new \DirectoryIterator($dir) as $fileInfo) {
        if($fileInfo->isDot()) continue;
        
        $path     = $fileInfo->getPathName();
        $filename = $fileInfo->getFilename();

        // No incluyo archivos que comiencen con "_"
        if (substr($filename, 0, 1) == '_'){
            
            continue;
        }

        if (in_array($filename, $excluded)){
            continue;
        }

        if(pathinfo($path, PATHINFO_EXTENSION) == 'php'){
            require_once $path;
        }
    }    
}

DB::setPrimaryKeyName('ID');
    
// require_once __DIR__ . '/app/core/scripts/admin.php';


if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY){
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
}

ini_set('display_errors', 0);

credits_to_author();
<?php

use boctulus\SW\libs\Import;
use boctulus\SW\core\libs\Files;
use boctulus\SW\core\libs\Strings;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
	return; 
}

require_once __DIR__ . '/app.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', realpath(__DIR__ . '/../../..') . DIRECTORY_SEPARATOR);

	require_once ABSPATH . '/wp-config.php';
	require_once ABSPATH .'/wp-load.php';
}

/////////////////////////////////////////////////

/*
	Sincronización

	php .\sync.php
*/


$config = config();

///////////////////////////////////////////////////

if (php_sapi_name() == "cli"){
	$file = $argv[0];

	if (Strings::contains('/', $file)){
		$dir = Strings::beforeLast($file, '/');
		chdir($dir);
	}
}

///////////////////////////////////////////////////


$path = ETC_PATH . 'productos.csv';  // <--------- hardcoded
$rows = Files::getCSV($path)['rows'];

$sku  = isset($_GET['sku']) ? explode(',',$_GET['sku']) : null;

/*
	Sincronizar !!!
*/

$enabled = $config['is_enabled'] ?? false;

if ($config['is_enabled'] !== true && $config['is_enabled'] != "true" && $config['is_enabled'] != 1 ){
	dd("Sync deshabilitado");
	exit(0);
}

try {
	Import::init($rows, false, $sku ?? null);
} catch (\Exception $e){
	dd($e->getMessage());
}

dd("Finalizado");




<?php

use boctulus\SW\core\libs\Files;
use boctulus\SW\libs\Import;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (php_sapi_name() != "cli"){
	// return; 
}

require_once __DIR__ . '/app.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', realpath(__DIR__ . '/../../..') . DIRECTORY_SEPARATOR);

	require_once ABSPATH . '/wp-config.php';
	require_once ABSPATH .'/wp-load.php';
}

/////////////////////////////////////////////////


/*
	Testing
*/

$path = ETC_PATH . 'productos.csv';  // <--------- hardcoded
$rows = Files::getCSV($path)['rows'];


$simulate = (bool) $_GET['simulate'];
$sku      = isset($_GET['sku']) ? explode(',',$_GET['sku']) : null;

// $sku = [
// 	'ANCHOLIVA70G',
// ];

try {
	Import::init($rows, false, $sku ?? null, $simulate);
} catch (\Exception $e){
	dd($e->getMessage());
}





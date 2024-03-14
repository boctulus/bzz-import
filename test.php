<?php

use boctulus\SW\core\libs\VarDump;
use boctulus\SW\libs\Sync;
use boctulus\SW\core\libs\Url;
use boctulus\SW\core\libs\Posts;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\Metabox;
use boctulus\SW\core\libs\StdOut;
use boctulus\SW\core\libs\Strings;
use boctulus\SW\core\libs\Products;
use ElementorPro\Modules\Woocommerce\Documents\Product;

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


// $featured_img = 'https://www.iconsdb.com/icons/preview/red/house-xxl.png';

// $att_id = Products::uploadImage($featured_img);

// dd(Products::getImageURL($att_id));

// exit;

$grupo = $_GET['g'] ?? 1;

$pid = Products::getIdBySKU('HUEGRAN2DOCE');

$images = [
	"http://woo1.lan/wp-content/uploads/2024/03/100320241710045099-300x200.jpeg",
];

if ($grupo == '1'){
	$images = [
		"http://woo1.lan/wp-content/uploads/2024/02/050220241707132427.jpeg",
		"http://woo1.lan/wp-content/uploads/2024/02/050220241707132431-100x100.jpeg"
	];
}

$featured = $images[0];

$att_ids = Products::setImages($pid, $images, $featured);

// foreach ($att_ids as $att_id){
// 	dd(Products::getImageURL($att_id));
// }

exit;

// dd(
// 	Sync::getSucursales()	
// );

dd(
	Sync::getProductsBySucursal(126)
);
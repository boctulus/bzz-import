<?php

use boctulus\SW\libs\Sync;
use boctulus\SW\core\libs\Url;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\StdOut;
use boctulus\SW\core\libs\Strings;
use boctulus\SW\core\libs\Products;

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


Products::deleteAllProducts();
//Products::deleteAllGaleryImages();
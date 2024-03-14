<?php

namespace boctulus\SW\background\tasks;

use boctulus\SW\libs\Sync;
use boctulus\SW\core\libs\Task;
use boctulus\SW\core\libs\Logger;

class ProcesarCategoria extends Task
{ 
    static protected $priority = 10;
    static protected $exec_time_limit   ;
    static protected $memory_limit;
    static protected $dontOverlap = false;

	function run($_category){
        // error_log('-------> '. __FILE__ . ' > ' . __METHOD__ . ' > '. __LINE__, 0);

        $category = unserialize($_category);

        $cat_name = $category['name'] ?? '';
        $cat_slug = $category['slug'] ?? '';
        $cat_url  = $category['link'] ?? '';

        // error_log('-------> '. __FILE__ . ' > ' . __METHOD__ . ' > '. __LINE__, 0);

       if (!empty($cat_slug) && !empty($cat_name)) {
           error_log("Por procesar categoría $cat_name en $cat_slug [...]");
           Sync::processCategory($_category);
       }
	}
}

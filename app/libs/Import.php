<?php declare(strict_types=1);

namespace boctulus\SW\libs;

/*
    @author  Pablo Bozzolo boctulus@gmail.com
*/

use boctulus\SW\core\libs\Files;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\Strings;
use boctulus\SW\core\libs\Products;
use boctulus\SW\core\libs\VarDump;

class Import
{    
    const  MODE_PROD         = "PROD";
    const  MODE_DEV          = "DEV";


    /*
        Sincronización

        Ej:

        php .\import.php
    */
    static function init(array $rows, bool $sync_categos = true, $query_sku = null, bool $simulate = false)
    {       
        if (!is_cli() && (isset($_SERVER) && !Strings::startsWith('PostmanRuntime/',$_SERVER['HTTP_USER_AGENT']))){
            VarDump::hideResponse();
        }

        if (empty($rows)){
            dd("Sin productos?");
            return;
        }

        $processed = 0;

        $supplier_prod_ids = array_column($rows, 'SKU');
                
        // dd("Iniciando purga,...");    
        // $deleted = static::purge($supplier_prod_ids);            
        // dd("Se purgaron la cantidad de $deleted productos");
    
        foreach ($rows as $_p)
        {
            // dd($_p, 'P');
            // continue;

            // "Atributos" base

            $sku = $_p['SKU'];

            if (empty($sku)){
                dd($_p, 'P sin SKU');
                continue;
            }

            // Filtro para pruebas
            if (!empty($query_sku) && !in_array($sku, $query_sku)){
                continue;
            }

            $price                   = $_p['Precio']; 
            $price_for_sale          = $_p['Precio Oferta'] ?? null;
            $enabled                 = $_p['Habiltado']; 

            $price                   = Products::formatMoney($price);
            $price_for_sale          = Products::formatMoney($price_for_sale);


            // Puede comenzar desde un subSubfamilyName
            $catego                  = $_p['Categoria'];  // *

            $supplier_prod_id        = $_p['SKU'];
            $stock                   = $_p['Stock'];

            $p = [
                'name'               => $_p['Nombre'],
                'sku'                => $sku,                
                'category'           => $catego,                
                //'images'             => $_p["imagenesUrl"],
                
                'manage_stock'       => true,
                'stock_quantity'     => $stock,
                'stock_status'       =>  ((int) $stock) != 0 ? 'instock' : 'outofstock',
                
                'price'              => $price,
                'sale_price'         => $price_for_sale,
                'type'               => 'simple',
                'status'             => !empty($price) && $enabled ? "publish" : "draft"
            ];
            
            $p['short_description']  = $_p['Descripcion corta'];
            $p['description']        = $_p['Descripcion larga'];

            // Transformaciones sobre varios campos
            // ..
           
            //

            $p["images"] = [];

            for ($i=0; $i<12; $i++){
                if (!empty($_p["Img{$i}"])){
                    if (Strings::startsWith('http', $_p["Img{$i}"])){
                        $p["images"][] = $_p["Img{$i}"];
                    }                    
                } else {
                    // Debe comenzar por Img1
                    // break;
                }
            }

            if (empty($p["images"])){
                $no_images[] = $sku;
            }

            // dd($p['images'], "{$p['name']} [ {$p['sku']} ] | IMGs");

            // Otros atributos

            // + attributes 
            $attrs = [];
    
            // $attrs['web-link']         = $_p['webLink'] ?? null;
            // $attrs['SocioComercialId'] = $_p['socioComercialId'];   

            // $attrs['suggested_price']= $suggested_price;

            // if (!empty($_p['atributos'])){
            //     foreach ($_p['atributos'] as $a) {
            //         $attrs[$a['nombre']] = $a['valor'];
            //     }
            // }   

            $tags = [ 
                // $family_name,
                // ...
            ];

            $p['tags'] = $tags;
   
            $p['weight']    = $_p['Peso'] ?? null;

            if (isset($_p['Dimensiones'])){
                $_dims = explode('x', strtolower($_p['Dimensiones']));
                
                $p['width']  = $_dims[0] ?? null;
                $p['length'] = $_dims[1] ?? null;
                $p['height'] = $_dims[2] ?? null;
            }

            if ($simulate){
                dd($p, 'PRODUCTO');
                continue;
            }
            

            try {
                $pid = Products::getProductIdBySKU($sku);

                if (!empty($pid)){
                    /*
                        SI existe, actualizo
                    */   

                    if (empty($p['price'])){
                        // Logger::log("Borrando producto con PID = '$pid' porque no tiene precio");
                        // Products::deleteProduct($pid);
                        // continue;
                    }

                    // En este punto puedo aplicar una GANANCIA ***

                    // Redondeo
                    // $p['price'] = my_round($p['price']); //
                    

                    // if (empty($p['price'])){
                    //     Logger::log("Por alguna razon desconocida el prod. con PID = '$pid' no tiene precio");
                    //     continue;
                    // }

                    dd("Actualizando producto existente con SKU '$sku' (PID=$pid)");
                    products::updateProductBySku($p);                      

                } else {

                    // if (empty($p['price'])){
                    //     Logger::log("Ignorando producto con PID = '$pid' porque no tiene precio");
                    //     continue;
                    // }

                    /*
                        Sino existe, lo creo
                    */
                    
                    $p   = Products::createProduct($p);
                    $pid = $p->get_id();

                    Products::setMeta($pid, 'synced_by_connector', true);

                    dd("Creando producto para SKU '$sku' -> PID = $pid");
                }
                
                
                // Imagen de portada

                $_p['imagen'] = $_p['imagen'] ?? $_p['images'][0] ?? null;

                if ((env('SYNC_IMAGES') == '1' || env('SYNC_IMAGES') === 1) && isset($_p['imagen'])){
                    $image_url = $_p['imagen'];
                    $img_pid   = Products::uploadImage($image_url);
                    Products::setDefaultImage($pid,    $img_pid  ); 
                    
                    dd($image_url, "MAIN IMG, ID=$img_pid");
                }

                // // ...

                
                /*
                    Hacer exportables ciertos campos
                */

                // $catego_id                   = !empty($catego) ? Products::getCategoryIdByName($catego) : null;
                // $attrs['cat_id']             = $catego_id;
                // $attrs['cat_nombre']         = $catego;
                // // $attrs['suggested_price'] = $suggested_price;
                // $attrs['p_ganancia']         = $per_ganancia;
                // $attrs['fecha_supplier']     = $_p['timeStamp'] ?? $at;
                
                $d  = new \DateTime();
                $at = $d->format('Y-m-d H:i:s');

                $invisibles = [
                    // 'supplier_pid',
                    // 'damaged_stock',
                    // 'cost_price',
                    // 'suggested_price'
                ];

                Products::setProductAttributesForSimpleProducts($pid, $attrs, $invisibles); //   

                /*
                    Almaceno precio en la pagina de producto
                */

                //Products::setMeta($pid, 'Precio sugerido', $suggested_price);

                $processed++;

                dd($attrs, 'ATTRs');
                dd('------------------');
            } catch (\Exception $e){
                $msg = $e->getMessage();
                dd($msg);
                Logger::log($msg);
            }           

            // exit; ////////

            // Evito sobrecargar al servidor
            //rest(config()['sleep_time'], true);

        } // end foreach

        
        if (!empty($no_images)){
            Logger::log($no_images, 'no-images.txt');
        }
        
        dd("Se procesaron $processed productos");
    
        return $processed;  
    }

    /*
        Metodo de sincronizacion de categorias
    */
    static function categoSync(array $categos)
    {
        /*
            ...
            [34] => Array
                (
                    [id] => 498
                    [descripcion] => categoria de prueba farmacia
                )
        */

        /*
            Update product categories
        */
    
        dd("Iniciando trabajo con categorías");

        foreach ($categos as $cat){  
            $name = $cat['descripcion'];
            $cid  = Products::createOrUpdateCategory($name);        
            dd($name, 'CAT');
        }
    }

    static function syncedBefore($pid){
        $synced = Products::getMeta($pid, 'synced_by_connector');
        return !empty($synced);
    }

    // static function getCategory

    /*
        Purga de productos que ya no vienen en la API

        Esta funcion no considera paginacion !!!
    */

    static function purge($supplier_prod_ids, bool $simulate = false, bool $permanently = false)
    { 
        /*
            Purga efectiva
        */

        $ids = Products::getIDs('product', 'publish'); 

        $deleted = 0;
        foreach ($ids as $pid){
            $synced = static::syncedBefore($pid);
            $sku   = Products::getSKUByProductId($pid);
            
            if (empty($sku)){
                continue;
            }

            if ($synced && !in_array($sku, $supplier_prod_ids)){
                $msg = "Borrando producto con SKU '$sku' porque no se halla en respuesta del supplier";

                dd($msg);
                Logger::log($msg);
                
                if (!$simulate){
                    Products::deleteProductBySKU($sku, $permanently);
                }
                
                $deleted++;
            }
        }

        return $deleted;
    }

    
}
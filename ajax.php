<?php

use boctulus\BzzCSVImport\libs\Files;
use boctulus\BzzCSVImport\libs\Strings;
use boctulus\BzzCSVImport\libs\Products;

require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Products.php';


function process_file($path)
{
    $config = include __DIR__ .'/config/config.php';

    $rows = Files::getCSV($path, $config["field_separator"])['rows'];
    $tot  = count($rows);

    $errors = [];
    foreach ($rows as $row){
        if (!isset($row[ $config["fields"]["sku"] ])){
            $errors[] = "Una fila no contiene SKU";
            continue;
        }

        $sku = $row[ $config["fields"]["sku"] ];

        if (count($row) <2){
            $errors[] = "Nada que hacer con solo el SKU ($sku)";
            continue;
        }

        $pid = Products::getProductIdBySKU($sku);

        // Podria ir acumulando errores pero seguir procesando,.... 
        if (empty($pid)){
            $errors[] = 'SKU no encontrado: '. $sku;
            continue;
        }

        $qty = $row['stockqty'] ?? null;

        if ($qty !== null){
            Products::updateStock($pid, $qty);
        }

        $regular_price = $row['Regular Price'] ?? null;

        if ($regular_price !== null){
            $regular_price = str_replace(',', '.', $regular_price);
            Products::updatePrice($pid, $regular_price);
        }
    }

    return [
        'errors' => $errors,
        'total_count' => $tot,
        'ok_count'    => $tot - count($errors) 
    ];
}    

/*
	REST

*/

/*
    El archivo CSV debe enviarse como "form-data"
*/
function post_csv(WP_REST_Request $req)
{
    try {        
        $error = new WP_Error();

        /*
            array (
            'csv_file' => 
            array (
                'name' => 'test.csv',
                'type' => 'text/csv',
                'tmp_name' => 'D:\\wamp64\\tmp\\phpB791.tmp',
                'error' => 0,
                'size' => 302,
            ),
            )
        */

        //dd($_FILES);

        if (empty($_FILES)){
            $error->add(400, 'No se ha recibido archivo');
            return $error;
        }

        if (!isset($_FILES['csv_file'])){
            $error->add(400, 'Algo esta mal. No se ha recibido archivo como se espera: '. var_export($_FILES, true));
            return $error;
        }

        $path   = $_FILES['csv_file']['tmp_name'];
        $res    = \process_file($path);

        $errors = $res['errors'];
        $total  = $res['total_count'];
        $cnt_ok = $res['ok_count']; 

        $msg = '';        

        if (!empty($errors)){
            $cnt_errors = count($errors);

            $msg .= "Han ocurrido algunos ($cnt_errors) errores al procesar el archivo CSV. Demás productos ($cnt_ok) fueron procesados con éxito.";
        } else {
            $msg = "Se procesaron en el CSV todos (los $total) los productos correctamente.";
        }
        
        $res = [
            'code' => 200,
            'message' => $msg,
            'errors' => $errors
        ];

        $res = new WP_REST_Response($res);
        $res->set_status(200);

        return $res;
    } catch (\Exception $e) {
        $error = new WP_Error();
        $error->add(500, $e->getMessage());

        return $error;
    }
}

function a_dummy(){
    sleep(2);

    $res = new WP_REST_Response('OK');
    $res->set_status(200);

    return $res;
}


add_action('rest_api_init', function () {
    #	{VERB} /wp-json/xxx/v1/zzz
    register_rest_route('bzz-import/v1', '/post-csv', array(
        'methods' => 'POST',
        'callback' => 'post_csv',
        'permission_callback' => '__return_true'
    ));

    register_rest_route('bzz-import/v1', '/dummy', array(
        'methods' => 'GET',
        'callback' => 'a_dummy',
        'permission_callback' => '__return_true'
    ));
});

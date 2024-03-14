<?php

use boctulus\SW\core\libs\Files;
use boctulus\SW\libs\Import;

/*
	REST

*/

/*
    El archivo CSV debe enviarse como "form-data"
*/
function post_csv(\WP_REST_Request $req)
{
    try {        
        $error = new \WP_Error();

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

        $errors    = $res['errors'];
        $total     = $res['total_count'];
        $processed = $res['processed']; 

        $msg = '';        

        if (!empty($errors)){
            $cnt_errors = count($errors);

            $msg .= "Han ocurrido algunos ($cnt_errors) errores al procesar el archivo CSV. Se procesaron $processed productos.";
        } else {
            $msg = "Se procesaron en el CSV un total de $processed de $total productos.";
        }
        
        $res = [
            'code' => 200,
            'message' => $msg,
            'errors' => $errors
        ];

        $res = new \WP_REST_Response($res);
        $res->set_status(200);

        return $res;
    } catch (\Exception $e) {
        $error = new \WP_Error();
        $error->add(500, $e->getMessage());

        return $error;
    }
}

function process_file($path)
{
    $config = config();

    $rows = Files::getCSV($path, $config["field_separator"])['rows'];
    $tot  = count($rows);

    $errors = [];

    if ($tot == 0){
        $errors[] = "Sin productos?";
    } else {
        foreach ($rows as $ix => $row){
            if (!isset($row[ $config["fields"]["sku"] ])){
                $errors[] = "Una fila no contiene SKU";
                unset($rows[$ix]);
                $tot--;
                continue;
            }
    
            $sku = $row[ $config["fields"]["sku"] ];
    
            if (count($row) <2){
                $errors[] = "Nada que hacer con solo el SKU ($sku)";
                unset($rows[$ix]);
                continue;
            }
        }
    }

    $processed = Import::init($rows);

    return [
        'errors'      => $errors,
        'processed'   => $processed,
        'total_count' => $tot
    ];
}    

function a_dummy(){
    sleep(2);

    $res = new \WP_REST_Response('OK');
    $res->set_status(200);

    return $res;
}


add_action('rest_api_init', function () {
    #	{VERB} wp-json/bzz-import/v1/post-csv
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

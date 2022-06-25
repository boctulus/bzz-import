<?php

use boctulus\BzzCSVImport\libs\Strings;
use boctulus\BzzCSVImport\libs\Files;
use boctulus\BzzCSVImport\libs\Debug;
use boctulus\BzzCSVImport\libs\Request;
use boctulus\BzzCSVImport\libs\Url;
use boctulus\BzzCSVImport\libs\Products;
use boctulus\BzzCSVImport\libs\System;

require_once __DIR__ . '/libs/Debug.php';
require_once __DIR__ . '/libs/Strings.php';
require_once __DIR__ . '/libs/Files.php';
require_once __DIR__ . '/libs/Request.php';
require_once __DIR__ . '/libs/Url.php';
require_once __DIR__ . '/libs/Arrays.php';
require_once __DIR__ . '/libs/Products.php';
require_once __DIR__ . '/libs/System.php';

require_once __DIR__ . '/helpers/debug.php';
require_once __DIR__ . '/helpers/system.php';


function render_table(Array $msgs){
    $cnt = count($msgs);

    $css = '
    <style>
    .bzz-errors-table-container {
    }
	.bzz-errors-table {
        height: 250px; 
        overflow-y: scroll;
        display: block;
		border:1px solid #C0C0C0;
		border-collapse:collapse;
		padding:5px;
	}
	.bzz-errors-table th {
		border:1px solid #C0C0C0;
		padding:5px;
		background:#F0F0F0;
	}
	.bzz-errors-table td {
		border:1px solid #C0C0C0;
		padding:5px;
	}
    </style>';

    $trs = '';
    foreach ($msgs as $msg){
        $trs .= "
        <tr>
            <td>$msg</td>
        </tr>";
    }

   $table = '<div class="bzz-errors-table-container">
        <table class="bzz-errors-table">
            <thead>
            <tr>
                <th>Errores</th>
            </tr>
            </thead>
            <tbody>
            '.$trs.'
            </tbody>
        </table>
    </div>';

    return "$css     
    $table";
}

/*
    Panel administraitivo
*/
if ( is_admin() ) {
    add_action( 'admin_menu', 'bzz_csv_import', 100 );
}


function bzz_csv_import() {
    add_submenu_page(
        'edit.php?post_type=product',
        __( 'Bzz CSV Import' ),
        __( 'Bzz CSV import' ),
        'manage_woocommerce', // Required user capability
        'woo-connector',
        'bzz_csv_import_admin_panel'
    );
}

function bzz_csv_import_admin_panel() {
    if (!current_user_can('administrator'))  {
        wp_die( __('Su usuario no tiene permitido acceder') );
    }

    echo bzz_import_shortcode();
}


// function that runs when shortcode is called
function bzz_import_shortcode() 
{   
    ?>
        <script>

        document.addEventListener('DOMContentLoaded', function() {
            function csv_file_loaded(){
                let file = jQuery('#csv_file').val();
                
                if (file != ''){
                    jQuery('#submit_csv').attr("disabled", false);
                }
            }

            jQuery('#csv_file').on("change", function(){ csv_file_loaded(); });

            /*
                Agregado de Esteban Toloza
            */

            document.getElementById("submit_csv").addEventListener("click", loadingNotification)
			
            function loadingNotification() {
				document.getElementById("loading-text").innerHTML = "Actualizando productos, NO CIERRE ESTA PÁGINA!";
			}
        });

        </script>
    <?php

    $config =  include(__DIR__ . '/config/config.php');

    ini_set("memory_limit", $config["memory_limit"] ?? "728M");
    ini_set("max_execution_time", $config["max_execution_time"] ?? 1800);
    ini_set("upload_max_filesize",  $config["upload_max_filesize"] ?? "50M");
    ini_set("post_max_size",  $config["post_max_size"] ?? "50M");


    $out = '';

    if (isset($_POST['bzz_import'])){

        /*
            array (
            'csv_file' => 
            array (
                'name' => 'test.csv',
                'type' => 'text/csv',
                'tmp_name' => 'D:\\wamp64\\tmp\\phpAD9.tmp',
                'error' => 0,
                'size' => 84,
            ),
            )
        */
        if (empty($_FILES['csv_file'])){ 
            $error = new WP_Error();
            $error->add(500, 'Error al subir archivo');
            return $error;
        }

        $path = $_FILES['csv_file']['tmp_name'];
        $rows = Files::getCSV($path, $config["field_separator"])['rows'];

        $cnt  = count($rows);

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

        if (!empty($errors)){
            $cnt_errors = count($errors);
            $cnt_ok     = $cnt - $cnt_errors;

            $out .= "<p>Han ocurrido algunos ($cnt_errors) errores al procesar el archivo CSV. Demás productos ($cnt_ok) fueron procesados con éxito.</p><br>\r\n";
            $out .= render_table($errors);
        } else {
            $out = "<p>Se procesaron en el CSV todos (los $cnt) los productos correctamente.</p>";
        }

    }

    $out .= '
    
    <h3>Bzz CSV import</h3>

    <form action="'. Url::currentUrl() .'" method="post" enctype="multipart/form-data">
    <label for="csv_file">Selecciona el archivo:</label>
    <input type="file" id="csv_file" name="csv_file">
    <input type="hidden" name="bzz_import">
    <br><br>
    <input type="submit" id="submit_csv" disabled>
    </form>';
    
    return $out;
}


// register shortcode
add_shortcode('bzz-import', 'bzz_import_shortcode');


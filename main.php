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

require __DIR__ . '/config/config.php';


function render_table(Array $msgs){
    $cnt = count($msgs);

    $css = '
    <style>
	.demo {
		border:1px solid #C0C0C0;
		border-collapse:collapse;
		padding:5px;
	}
	.demo th {
		border:1px solid #C0C0C0;
		padding:5px;
		background:#F0F0F0;
	}
	.demo td {
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

   $table = '
    <table class="demo">
        <thead>
        <tr>
            <th>Errores</th>
        </tr>
        </thead>
        <tbody>
        '.$trs.'
        </tbody>
    </table>';

    return "$css
    <p>Han ocurrido algunos ($cnt) errores al procesar el archivo CSV. Demás productos fueron procesados con éxito.</p><br>\r\n 
    $table";
}


// function that runs when shortcode is called
function bzz_import_shortcode() {
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
        $rows = Files::getCSV($path, ';')['rows'];

        $cnt  = count($rows);

        $errors = [];
        foreach ($rows as $row){
            if (!isset($row['SKU'])){
                $errors[] = "Una fila no contiene SKU";
                continue;
            }

            if (count($row) <2){
                $errors[] = "Nada que hacer con solo el SKU ({row['SKU']})";
                continue;
            }

            $pid = Products::getProductIdBySKU($row['SKU']);

            // Podria ir acumulando errores pero seguir procesando,.... 
            if (empty($pid)){
                $errors[] = 'SKU no encontrado: '. $row['SKU'];
                continue;
            }

            if (isset($row['stockqty'])){
                Products::updateStock($pid, $row['stockqty']);
            }

            if (isset($row['Regular Price'])){
                $price = str_replace(',', '.', $row['Regular Price']);
                Products::updatePrice($pid, $price);
            }
        }

        if (!empty($errors)){
            $out = render_table($errors);
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
    <input type="submit">
    </form>';
    
    return $out;
}


// register shortcode
add_shortcode('bzz-import', 'bzz_import_shortcode');

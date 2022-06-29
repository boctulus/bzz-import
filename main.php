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

require_once __DIR__ . '/ajax.php';


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
            function submit_csv(){
                var fd = new FormData();
                fd.append('file', this.files[0]); // since this is your file input

                $.ajax({
                    url: "<?= Url::currentUrl() ?>index.php/Task_controller/upload_tasksquestion",
                    type: "post",
                    dataType: 'json',
                    processData: false, // important
                    contentType: false, // important
                    data: fd,
                    success: function(text) {
                        alert(text);
                        if(text == "success") {
                            alert("Your image was uploaded successfully");
                        }
                    },
                    error: function() {
                        alert("An error occured, please try again.");         
                    }
                });
            }

            jQuery('#submit_csv_form').on("submit", function(){ submit_csv(); });

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

    // ...

    $out .= '
    
    <h3>Bzz CSV import</h3>

    <form id="submit_csv_form">
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


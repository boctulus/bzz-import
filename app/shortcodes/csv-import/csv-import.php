<?php

// function that runs when shortcode is called
function bzz_import_shortcode() 
{   
    ?>
        <style>
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
        </style>

        <script>

        document.addEventListener('DOMContentLoaded', function() {
            function submit_csv(event){
                event.preventDefault();

                var file_data = jQuery('#csv_file').prop('files')[0];   
                var form_data = new FormData();                  
                form_data.append('csv_file', file_data);

                const base_url = '<?= home_url('/') ?>';
                const url = base_url + 'wp-json/bzz-import/v1/post-csv';

                jQuery.ajax({
                    url: url, // post-csv
                    type: "post",
                    dataType: 'json',
                    cache: false,
                    processData: false, // important
                    contentType: false, // important
                    data: form_data,
                    success: function(res) {
                        clearAjaxNotification();

                        if (typeof res['message'] != 'undefined'){
                            let msg = res['message'];

                            if (typeof res['errors'] != 'undefined'){
                                let trs = '';
                                for (let i=0; i<res['errors'].length; i++){
                                    //console.log(res['errors'][i]);

                                    trs += `
                                    <tr>
                                        <td>${res['errors'][i]}</td>
                                    </tr>`;
                                }

                                msg = msg + `<p></p>
                                <div class="bzz-errors-table-container">
                                    <table class="bzz-errors-table" style="max-width: 50%;">
                                        <thead>
                                        <tr>
                                            <th>Errores</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tbody-bzz-errors-table" style="width:100%">
                                            ${trs}
                                        </tbody>
                                    </table>
                                </div>`
                            }

                            setNotification(msg);
                        }
                        
                        

                        console.log(res);                        
                    },
                    error: function(res) {
                        clearAjaxNotification();

                        if (typeof res['message'] != 'undefined'){
                            setNotification(res['message']);
                        }

                        console.log(res);
                        console.log("An error occured, please try again.");         
                    }
                });
            }

            jQuery('#submit_csv_form').on("submit", function(event){ submit_csv(event); });

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

            document.getElementById("submit_csv").addEventListener("click", loadingAjaxNotification)
            
            function setNotification(msg){
                document.getElementById("bzz-notifications").innerHTML = msg;
            }

            function loadingAjaxNotification() {
                document.getElementById("loading-text").innerHTML = "<p>ACTUALIZANDO PRODUCTOS. NO CIERRE ESTA PÁGINA!</p>";
            }

            function clearAjaxNotification() {
                document.getElementById("loading-text").innerHTML = "";
            }
        });

        </script>
    <?php

    $out = '';

    // ...

    $out .= '
    
    <h3>Bzz CSV import</h3>

    <form id="submit_csv_form">
    <label for="csv_file">Selecciona el archivo:</label>
    <input type="file" id="csv_file" name="csv_file">
    <input type="hidden" name="bzz_import">
    <br><br>
    <input type="submit" id="submit_csv" class="button button-primary" value="Enviar" disabled>

    </form>
    
    <p></p>
    <div id="loading-text"></div>

    <div id="bzz-notifications">
        
    </div>
    
    ';
    
    return $out;
}


// register shortcode
add_shortcode('bzz-import', 'bzz_import_shortcode');


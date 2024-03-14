<?php

use boctulus\SW\core\libs\Url;
use boctulus\SW\core\libs\Date;
use boctulus\SW\core\libs\Files;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\ApiClient;


$lang = $_GET['lang'] ?? 'es';

/*
    All or themes tab
*/

$themes_tab = Url::hasQueryParam(Url::currentUrl(), 'type') && Url::getQueryParam(Url::currentUrl(), 'type') == 'themes';
$search_tab = Url::hasQueryParam(Url::currentUrl(), 'tab')  && Url::getQueryParam(Url::currentUrl(), 'tab')  == 'search' && Url::hasQueryParam(Url::currentUrl(), 'q');    

if ($search_tab){
    $q = Url::getQueryParam(Url::currentUrl(), 'q');
}

$url = 'https://mutawp.com/ajax/get_products';
//$url = 'http://simplerest.lan/dumb/serve_json';

$cli  = (new ApiClient($url))
    ->withoutStrictSSL()
    //->cache(300)
    ->setMethod('get');

// $cli->mock(ETC_PATH . 'responses/products.json');

$cli
->when($themes_tab, function($cli){
    $cli->queryParam('type', 'theme');
})
->when($search_tab, function($cli) use ($q){
    $cli->queryParam('q', $q);
})
->queryParam('page_size', 10)  
->queryParam('page', 1) // <-------- luego ya por Ajax paginar lo que el usuario elija o no paginar y que use el buscador !!!
->queryParam('lang', $lang)
->send();

if (!empty($cli->error())){
    Logger::logError("HTTP Error. Detail: " . $cli->error());
}

$data = $cli->decode()->data();
$rows = $data['data']['products'];

// dd($rows);

?>


<div class="wrap plugin-install-tab-featured">

    <h1 class="wp-heading-inline muta-h1-title" style="text-transform: uppercase;">Plugins & temas via MutaWP</h1>

    <!-- Search engine  -->
    <div class="wp-filter">
        <ul class="filter-links" style="text-transform: uppercase;">           
            <li><a href="/wp-admin/admin.php?page=plugins_mutawp" aria-current="page" class="<?= (!$themes_tab ? 'current' : '') ?>">Plugins + temas</a> </li>
            <li><a href="/wp-admin/admin.php?page=plugins_mutawp&type=themes" class="<?= ($themes_tab ? 'current' : '') ?>">Solo temas</a> </li>
        </ul>

        <form class="search-form search-plugins" action="/wp-admin/admin.php" method="get">
            <input type="hidden" name="page" value="plugins_mutawp">
            <input type="hidden" name="tab" value="search">
            <select name="type" id="typeselector" disabled>
                <option value="term" selected="selected">Palabra clave</option>
                <option value="author">Título</option>
                <option value="tag">Descripción</option>
            </select>
            <label class="screen-reader-text" for="search-plugins">Buscar</label>
            <input type="search" name="q" id="search-plugins" value="" class="wp-filter-search" placeholder="Buscar ..." aria-describedby="live-search-desc">
            <input type="submit" id="search-submit" class="button" style="margin-top:10px;" value="Buscar">
        </form>

    </div>

    <!-- installables -->
    <form id="plugin-filter">
        <div class="wp-list-table widefat plugin-install">
            <h2 class="screen-reader-text">Lista de plugins</h2>
            <div id="the-list">
                <?php foreach ($rows as $row): 
                
                    /*
                        En base a si se encuentra un Plugin con cierto conjunto de metadatos, puedo asumir el plugin esta presente
                        y determinar si es upgradeable
                    */

                    $present      = false;
                    $will_upgrade = false;
                ?>
                <div class="plugin-card">
                    <div class="plugin-card-top">
                        <div class="name column-name">
                            <h3>
                                <a href="<?= $row['permlink'] ?>" class="thickbox open-plugin-details-modal">
                                    <?= $row['title'] ?>
                                    <img src="<?= $row['_thumb'] ?? asset('images/logo-wordpres.png') ?>" class="plugin-icon" alt="">
                                </a>
                            </h3>
                        </div>
                        <div class="action-links">
                            <ul class="plugin-action-buttons">
                                <!-- Boton de Instalar o actualizar -->
                                <li><a class="<?= (!$will_upgrade ? '' : 'update-now') ?> button aria-button-if-js" data-pid="<?= $row['pid'] ?>" href="#" aria-label="actualizar o instalar" data-name="<?= $row['title'] . ' ' . $row['_version_actual'] ?>" role="button"><?= (!$will_upgrade ? 'Instalar' : 'Actualizar') ?> ahora</a></li>
                                <li><a href="<?= $row['permlink'] ?>" class="thickbox open-plugin-details-modal">Más detalles</a></li>
                            </ul>
                        </div>
                        <div class="desc column-description">
                            <p><?= str_replace('%%currentyear%%', date('Y'), $row['_desc'] ?? '') ?></p>
                            <!--p class="authors"> <cite>Por <a href="https://woocommerce.com">Automattic</a></cite></p -->
                        </div>
                    </div>
                    <div class="plugin-card-bottom">
                        
                        <div class="vers column-rating">
                            <span><strong>Precio regular: </strong><?= $row['regular_price'] ?> USD</span>
                        </div>

                        <?php
                            if (isset($row['updated_at'])){
                                $update_str = 'hace ' . round(Date::diffInSeconds($row['updated_at']) / (-3600*24)) . ' días';

                                $update_str = str_replace('hace 0 días', 'hoy', $update_str);
                                $update_str = str_replace('hace 1 días', 'ayer', $update_str);
                            }
                        ?>

                        <div class="column-updated">
                            <strong>Última actualización:</strong>
                            <?= $update_str ?>
                        </div>

                        <div class="column-downloaded">
                            <?php if (!empty($row['_link_demo'])): ?>
                                Ver <a href="<?= $row['_link_demo'] ?>" target="_blank">demo</a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="column-compatibility">
                            <span><strong>Version actual: </strong><?= $row['_version_actual'] ?></span>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>                        
            </div>
        </div>
        <div class="tablenav bottom">
            <div class="tablenav-pages one-page"><span class="displaying-num">{N} elementos</span>
                <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                    <span class="screen-reader-text">Página actual</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">1 de <span class="total-pages">1</span></span></span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span>
            </div> 
            <br class="clear">
        </div>
    </form>


</div>

<script>
    const base_url = '<?= Url::getBaseUrl() ?>'

    const endpoint    = base_url + '/some-slugs' // <-- cambiar
    const verb        = 'POST'
    const dataType    = 'json'
    const contentType = 'application/json'
    const ajax_success_alert = {
        title: "API KEY generada!",
        text: "Siga instrucciones para configurar plugin WP_MUTA",
        icon: "success",
    }
    const ajax_error_alert   = {
        title: "Error",
        text: "Hubo un error. Intente más tarde.",
        icon: "warning", // "warning", "error", "success" and "info"
    }

    function setNotification(msg) {
        jQuery('#response-output').show()
        jQuery('#response-output').html(msg);
    }

    /*
        Agregado para el "loading,.." con Ajax
    */

    function loadingAjaxNotification() {
        <?php $path = asset('images/loading.gif') ?>
        document.getElementById("loading-text").innerHTML = "<img src=\"<?= $path ?>\" style=\"transform: scale(0.5);\" />";
    }

    function clearAjaxNotification() {
        document.getElementById("loading-text").innerHTML = "";
    }

    // ..

    document.addEventListener('DOMContentLoaded', function() {
        $ = jQuery
                
    
        const do_ajax_call = (key) => {            
            const url = endpoint; 

            let data = {
                // some data
            }

            console.log(`Ejecutando Ajax call`)
            console.log(data)

            loadingAjaxNotification()

            jQuery.ajax({
                url:  url, 
                type: verb,
                dataType: dataType,
                cache: false,
                contentType: contentType,
                data: (typeof data === 'string') ? data : JSON.stringify(data),
                success: function(res) {
                    clearAjaxNotification();

                    console.log('RES', res);
                    
                    //setNotification("Gracias por tu mensaje. Ha sido enviado.");
                    swal(ajax_success_alert);
                },
                error: function(res) {
                    clearAjaxNotification();

                    // if (typeof res['message'] != 'undefined'){
                    //     setNotification(res['message']);
                    // }

                    console.log('RES ERROR', res);
                    //setNotification("Hubo un error. Inténtelo más tarde.");

                    swal(ajax_error_alert);
                }
            });
            
        }

        
    });
</script>


<script>
    addEventListener("DOMContentLoaded", (event) => {
        if (typeof $ === 'undefined' && typeof jQuery !== 'undefined'){
            $ = jQuery
        }

        // ..
    })
</script>
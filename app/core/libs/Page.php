<?php 

namespace boctulus\SW\core\libs;

class Page
{
    /*
        Busca por coincidencias en url actual en page={page} y /page
    */
    static function isPage(string $page = ''): bool {
        if (!empty($page)) {
            $keyword = "page=" . $page;
            return (strpos($_SERVER['REQUEST_URI'], $keyword) !== false) ||  (strpos($_SERVER['REQUEST_URI'], "/$page") !== false);
        } else {
            return strpos($_SERVER['REQUEST_URI'], 'page=') !== false;
        }
    } 

    static function pageContains(string $page): bool {
        return Strings::contains("page=" . $page, $_SERVER['REQUEST_URI']);
    }  
    
    /*
        Para productos, devolveria 'product'
    */
    static function getType($post = null){
        return get_post_type($post);
    }

    /*
        WooCommerce
    */

    static function isCart(){
        return is_cart();
    }

    static function isCheckout(){
        return is_checkout();
    }

    static function isProductArchive(){
        return is_shop(); 
    }

    static function isProduct(){
        return is_product();
    }

    static function isProductCategory($term = ''){
        return is_product_category($term);
    }
    
    /*
        Extras
    */

    static function getSlug(){
        return get_post_field('post_name', get_post());
    }

    /*
        Devuelve el post con sus atributos dada la pagina actual

        @param $post_type por ejemplo 'page' o 'product'    
    */
    static function getPost($post_type = 'page') : Array {
        return get_page_by_path(static::getSlug(), ARRAY_A, $post_type );
    }

    /*
        @param callable $callback

        Ejemplo de uso:

        Page::replaceContent(function(&$content){
            $content = preg_replace('/Mi cuenta/', "CuentaaaaaaaX", $content);
        });
    */
    static function replaceContent(callable $callback){
        add_action( 'init', function(){
            ob_start();
        }, 0 );
        
        add_action('wp_footer', function() use ($callback)
        {       
            $content = ob_get_contents();
        
            $callback($content);
            ob_end_clean(); 
        
            echo $content;        
        });
    }

}
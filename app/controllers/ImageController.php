<?php

namespace boctulus\SW\controllers;

use boctulus\SW\core\libs\XML;
use boctulus\SW\core\libs\Files;
use boctulus\SW\core\libs\MediaType;

class ImageController
{
    /*
        Para recuperar la imagen:

        {base-url}/image/serve/{product-id}
    */
    function serve($pid)
    {
        $path = APP_PATH . 'downloads' . DIRECTORY_SEPARATOR . "prod_image-$pid";
        $str  = file_get_contents($path);

        MediaType::renderImage($str);
    }
}

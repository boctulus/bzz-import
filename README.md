# BZZ CSV Import

Desarrollado por
Pablo Bozzolo <boctulus>

Descripción:

Importador de productos desde CSV para WooCommerce.

Se provee un shortcode [bzz-import] y además es accesible desde el menú de Admin:

    WooCommerce > Productos > Bzz CSV Import

En esta versión solo son soportados los campos:

SKU
Precio regular
Cantidad

El nombre de los campos debe configurarse en el el archivo config.php

Por ejemplo si el sku es SKU, la cantidad es stockqty y el precio regular es Regular Price, entonces:

"fields" => [
    "sku" => "SKU",
    "qty" => "stockqty",
    "regular_price" => "Regular Price"
],
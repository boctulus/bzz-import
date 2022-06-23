# BZZ CSV Import

Desarrollado por
Pablo Bozzolo <boctulus>

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
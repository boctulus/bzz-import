# Zetti-WooCommerce connector

Por boctulus

El plugin provee como punto de entrada el archivo sync.php el cual debe colocarse como cronJob. 

php sync.php prices

o con ruta absoluta:

php ruta/al/archivo/sync.php

### Parametrización

prices)

    php ruta/al/archivo/sync.php prices

Se lo puede llamar con el parámetro ´prices´ y entonces *solo* actualizará precios dejando intactos lose demás campos. En caso de detectarse un producto nuevo, será creado.

catalog)

    php ruta/al/archivo/sync.php catalog
    php ruta/al/archivo/sync.php

En caso de utilizar la opción por defecto (que equivale a pasar como parámetro ´catalog´) que actualiza todos los campos. En caso de detectarse un producto nuevo, será creado.

En caso de que exista una imágen como destacada ('featured'), entonces no se actualizarán más las imágenes para ese producto evitando sobre-escribirlas.


#   z e t t i - c o n n e c t o r  
 #   b z z - i m p o r t  
 
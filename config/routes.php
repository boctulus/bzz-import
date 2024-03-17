<?php

/*
    Routes for Router

    Nota: la ruta mas general debe colocarse al final
*/

return [
    // rutas

    '/admin/wipe'          => 'boctulus\SW\controllers\AdminTasksController@wipe',
    '/admin/run'           => 'boctulus\SW\controllers\AdminTasksController@run',
];

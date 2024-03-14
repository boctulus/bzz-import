<?php

/*
    Podria ser una clase (seria mas flexible usar metodos encadenados)
*/

if (!function_exists('_cors')){
    function _cors(){  
        // Permitir peticiones desde cualquier origen (CORS)
        header("Access-Control-Allow-Origin: *");

        // Permitir los métodos de solicitud que se pueden utilizar con la API
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        // Permitir ciertos encabezados en las solicitudes (si es necesario)
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Permitir que las credenciales (como cookies y encabezados de autenticación) se incluyan en la solicitud (si es necesario)
        // Esto solo se debe habilitar si tu API requiere trabajar con credenciales
        //header("Access-Control-Allow-Credentials: true");

        // Opcional: Establecer el tiempo en segundos durante el cual el resultado de una solicitud OPTIONS puede ser almacenado en caché.
        //header("Access-Control-Max-Age: 86400");

        // Si la solicitud es OPTIONS, terminar aquí y no ejecutar el resto del código
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }    
    }
}

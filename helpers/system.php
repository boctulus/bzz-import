<?php

if (!function_exists('is_cli')){
    function is_cli(){
        return (php_sapi_name() == 'cli');
    }
}

if (!function_exists('is_unix')){
    function is_unix(){
        return (DIRECTORY_SEPARATOR === '/');
    }
}
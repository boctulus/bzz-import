<?php

namespace boctulus\SW\controllers;

use boctulus\SW\libs\Import;
use boctulus\SW\core\libs\XML;
use boctulus\SW\core\libs\Dokan;
use boctulus\SW\core\libs\Users;
use boctulus\SW\core\libs\Logger;
use boctulus\SW\core\libs\System;
use boctulus\SW\core\libs\Request;
use boctulus\SW\core\libs\Products;
use boctulus\SW\core\libs\ApiClient;

class AdminTasksController
{
    function __construct()
    {   
        // Restringe acceso a admin
        Users::restrictAccess();
    }

    function index(){
        $php = System::getPHP();
        dd($php, 'PHP PATH');

        dd("Bienvenido!");
    }

    function migrate()
    {   
        dd("Migrating ...");

        $mgr = new MigrationsController();
        $mgr->migrate(); // "--dir=$folder", "--to=$tenant"
    }

    // Borra productos y sus categorias
    function wipe(){
        dd("Wiping products & categories ...");

        Products::deleteAllProducts();
        Products::deleteAllCategories(false, false);
    }


    /*
        Ejecuta la sincronizacion *sin* cron (solo pruebas)

        Enviar bg=1 para correr en background

        /admin_tasks/run?bg=1
    */
    function run()
    {
        $bg = false;

        if (isset($_GET['bg']) && $_GET['bg'] == '0'){
            $bg = false;
        }

        $simulate = (bool) $_GET['simulate'];
        $sku      = isset($_GET['sku']) ? explode(',',$_GET['sku']) : null;

        if ($_GET['bg'] ?? false){
            dd("Running in background ...");

            $php = System::getPHP();
            System::runInBackground("$php sync.php" . (!empty($sku) ? "sku=$sku" : ''));

            return;
        } else {
            try {
                dd("Running ...");

                Import::init(false, $sku ?? null, $simulate);
            } catch (\Exception $e){
                Logger::logError($e->getMessage());
            }        
        }        
    }

    /*
        --| max_execution_time
        300

        --| PHP version
        8.1.26
    */
    function show_system_vars(){
        dd(
            ini_get('max_execution_time'), 'max_execution_time'
        );

        dd(phpversion(), 'PHP version');
    }

    /*
        Devuelve algo como

        D:\www\woo6\wp-content\plugins\wp_runa\
    */
    function plugin_dir(){
        return realpath(__DIR__);
    }

    function get_smtp(){
        $smtp_host = ini_get('SMTP');
        $smtp_port = ini_get('smtp_port');
        $smtp_user = ini_get('smtp_user');
        $smtp_pass = ini_get('smtp_pass');

        // Muestra la información
        dd( "SMTP Host: $smtp_host");
        dd( "SMTP Port: $smtp_port");
        dd( "SMTP User: $smtp_user");
        dd( "SMTP Password: $smtp_pass");

        $to      = 'boctulus@gmail.com';    
        $subject = "Test";
        $message = "Probando 1,2,3";

        $sent = wp_mail($to, $subject, $message);        
        dd($sent, 'Sent?');
    }

    function log(){
        return file_exists(LOGS_PATH . 'log.txt') ? file_get_contents(LOGS_PATH . 'log.txt') : '--x--';
    }

    function error_log(){
       return file_exists(LOGS_PATH . 'errors.txt') ? file_get_contents(LOGS_PATH . 'errors.txt') : '--x--';
    }

    function debug_log(){
        return file_exists(__DIR__ . '/../wp-content/debug.log') ? file_get_contents(__DIR__ . '/../wp-content/debug.log') : '--x--';
    }

    function req(){
        return file_exists(LOGS_PATH . 'req.txt') ? file_get_contents(LOGS_PATH . 'req.txt') : '--x--';
    }

    function res(){
        return file_exists(LOGS_PATH . 'res.txt') ? file_get_contents(LOGS_PATH . 'res.txt') : '--x--';
    }
    
    function adminer(){
        require_once __DIR__ . '/../scripts/adminer.php';
    }

    function update_db(){
        require __DIR__ . '/../scripts/installer.php';
        dd('done table creation');

        $this->insert();
        dd('done insert table');
    }

    function insert(){
        global $wpdb;
        
       // ...
    }
}

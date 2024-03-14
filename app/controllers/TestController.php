<?php

namespace boctulus\SW\controllers;

use boctulus\SW\libs\Sync;
use boctulus\SW\core\libs\XML;
use boctulus\SW\core\libs\Users;
use boctulus\SW\core\libs\Request;
use boctulus\SW\core\libs\Products;
use boctulus\SW\core\libs\ApiClient;

class TestController
{
    function __construct()
    {   
        // Restringe acceso a admin
        // Users::restrictAccess();
    }   

    function t5(){
        $sku  = '0514-0082';

        dd(Products::getProductIDBySKU($sku));
    }

    function t4(){
        $sku = '0514-0082';
        $pid = Products::getProductIDBySKU($sku);

        dd($pid, 'PID para '. $sku);
    }

    function t3()
    {
        $sku = '0514-0082';

        $args  =  [
            'sku'   => $sku,
            'type'  => 'simple',
            'price' => 500
        ];

        Products::updateProductBySKU($args);
    }

    function t1(){
        dd(__FUNCTION__);

        $grupo = $_GET['g'] ?? 0;

        $pid = Products::getIdBySKU('0026-0010');

        dd($pid, 'PID');

        $images = [
            "https://d2wuoo4cuot0vy.cloudfront.net/0026-0010/0026-0010_1134924986.jpg",
            "https://d2wuoo4cuot0vy.cloudfront.net/0026-0010/0026-0010_1434593415.jpg"
        ];

        // if ($grupo == '1'){
        //     $images = [
        //         "http://woo1.lan/wp-content/uploads/2024/02/050220241707132427.jpeg",
        //         "http://woo1.lan/wp-content/uploads/2024/02/050220241707132431-100x100.jpeg"
        //     ];
        // }

        $featured = $images[0];

        $att_ids = Products::setImages($pid, $images, $featured);

        foreach ($att_ids as $att_id){
            dd(Products::getImageURL($att_id), "ATT ID = $att_id");
        }
    }

    // ok
    function t2(){
        $pid = 27647;

        $att_id = Products::uploadImage('https://d2wuoo4cuot0vy.cloudfront.net/0026-0010/0026-0010_1134924986.jpg');

        Products::setImagesForPost($pid, [ $att_id ]);
        Products::setDefaultImage($pid, $att_id);
    }

    function index()
    {
        $this->t2();
        exit;

        $featured_img = 'https://www.iconsdb.com/icons/preview/red/house-xxl.png';

        $att_id = Products::uploadImage($featured_img);

        dd(Products::getImageURL($att_id));
    }

    function get_image(){
        // modo dev para poder encontrar imagenes
        Sync::dev();

        dd(
            Sync::getImage(350)
        );
    }

    function sync_test(){
        dd("TEST");

        $sku = [
            '0026-0010',
            '0315-0092'
        ];
        
        //$sku = 'MX1-03-00-HXN-643';
        
        // modo dev para poder encontrar imagenes
        Sync::dev();
        
        try {
            Sync::init(false, $sku ?? null);
        } catch (\Exception $e){
            Logger::dump($e);
        }
    }

   
}

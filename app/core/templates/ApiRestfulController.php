<?php

namespace boctulus\SW\controllers\api;

use boctulus\SW\controllers\MyApiController; 

class __NAME__ extends MyApiController
{ 
    static protected $soft_delete = __SOFT_DELETE__;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function __construct()
    {       
        parent::__construct();
    }        
} 

<?php

namespace boctulus\SW\middlewares;

use boctulus\SW\core\Middleware;
use boctulus\SW\core\libs\DB;
use boctulus\SW\core\libs\Strings;

class __NAME__ extends Middleware
{   
    function __construct()
    {
        parent::__construct();
    }

    function handle(?callable $next = null){
        $res = $this->res->get();

        // ...

        $this->res->set($res);
    }
}
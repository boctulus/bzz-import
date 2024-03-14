<?php

use boctulus\SW\core\libs\Files;
use boctulus\SW\core\libs\Strings;
use boctulus\SW\core\interfaces\IUpdateBatch;
use boctulus\SW\controllers\MigrationsController;

/*
    Run batches
*/

class __NAME__ implements IUpdateBatch
{
    function run() : ?bool{
        // ...
        
        return true;
    }
}
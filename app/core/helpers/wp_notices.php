<?php

use boctulus\SW\core\libs\WPNotices;

/*
    Ej:

    admin_notice('Problema critico!', WPNotices::SEVERITY_ERROR);
*/
function admin_notice($msg, $severity = 'info', bool $dismissible = true){    
   WPNotices::send($msg, $severity, $dismissible);
}
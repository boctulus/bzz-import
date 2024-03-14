<?php

use boctulus\SW\core\interfaces\IMigration;
use boctulus\SW\core\libs\Schema;

class JobWorkers implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('job_workers');
        
        $sc->int('id')->pri()->auto();
        $sc->varchar('queue')->index();
        $sc->int('pid', 5)->unique(); 
        $sc->datetime('created_at');
		$sc->create();	
		
    }
}


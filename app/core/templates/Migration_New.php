<?php

use boctulus\SW\core\interfaces\IMigration;
use boctulus\SW\core\libs\Factory;
use boctulus\SW\core\libs\Schema;
use boctulus\SW\core\Model;
use boctulus\SW\core\libs\DB;

class __NAME__ implements IMigration
{
    protected $table = '__TB_NAME__';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema($this->table);
		// ...
        $sc->create();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}



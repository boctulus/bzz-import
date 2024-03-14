<?php

use boctulus\SW\core\interfaces\IMigration;
use boctulus\SW\core\libs\Factory;
use boctulus\SW\core\libs\Schema;
use boctulus\SW\core\Model;
use boctulus\SW\core\libs\DB;

class StarRating implements IMigration
{
    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema('star_rating');

        $sc
        ->integer('id')->auto()->pri()
        ->text('comment')->nullable()
        ->int('score')
        ->varchar('author')
        ->datetime('deleted_at')
        ->datetime('created_at');

		$sc->create();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        $sc = new Schema('star_rating');
        $sc->dropTableIfExists();
    }
}


<?php

namespace boctulus\SW\models\main;

use boctulus\SW\models\MyModel;
use boctulus\SW\schemas\main\WpLinksSchema;

class WpLinksModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, WpLinksSchema::class);
	}	
}


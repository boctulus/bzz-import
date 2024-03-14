<?php

use boctulus\SW\core\libs\CronJob;
use boctulus\SW\core\libs\Logger;

class XCronJob extends CronJob
{
	static protected $month;
    static protected $monthday;
	static protected $weekday;
	static protected $hour = 21; 
	static protected $minute;
	static protected $second = 5;
	static protected $is_active = true;

	/*
		Number of retries in 24 Hs.
	*/
	static protected $retries = 3;
    static protected $retry_timeframe = 3600 * 24;


	function run(){
		// your logic here
		Logger::log("Bum bum bum");
	}
	
    /*
        @paran $error Exception object
        @param $times int number of fails
    */
    function onFail(\Exception $error, int $times){
    }

    function onSuccess(){

    }

}

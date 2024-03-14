<?php

use boctulus\SW\core\libs\StdOut;
use boctulus\SW\core\libs\System;

function is_cli(){
	return (php_sapi_name() == 'cli');
}

function is_unix(){
	return (DIRECTORY_SEPARATOR === '/');
}

/*
	Pasar -1 a $max_exec_time si desea que sea ilimitado
*/
function long_run($max_exec_time = 84000, $max_mem_size = '16384M'){
	System::setMemoryLimit($max_mem_size);
	System::setMaxExecutionTime($max_exec_time);
}

/*
	Tiempo en segundos de sleep

	Acepta valores decimales. Ej: 0.7 o 1.3
*/
function nap($time, $echo = false){
	if ($echo){
		StdOut::pprint("Taking a nap of $time seconds");
	}

	if (!is_numeric($time)){
		throw new \InvalidArgumentException("Time should be a number");
	}

	$time = ((float) ($time)) * 1000000;

	return usleep($time);	 
}
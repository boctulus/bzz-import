<?php

use boctulus\BzzCSVImport\libs\Debug;
use boctulus\BzzCSVImport\libs\Files;


if (!function_exists('dd')){
	function dd($val, $msg = null, $pre_cond = null){
		Debug::dd($val, $msg, $pre_cond);
	}
}

if (!function_exists('here') && function_exists('dd')){
	function here(){
		Debug::dd('HERE');
	}
}

if (!function_exists('debug')){
	function debug(...$args){
		if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY){
			if (is_cli()){
				dd(...$args);
			}
			
			Files::logger($args[0]);
		}
	}
}

if (!function_exists('foo')){
	function foo(){
		throw new \Exception("FOO");
	}
}


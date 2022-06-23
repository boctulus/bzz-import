<?php

/*
    @author  Pablo Bozzolo boctulus@gmail.com
*/

namespace boctulus\BzzCSVImport\libs;

class System
{
    static function getOS(){
        return defined('PHP_OS_FAMILY') ? PHP_OS_FAMILY : PHP_OS;
    }

    static function isLinux(){
        $os = static::getOS();

        return ($os == 'Linux');
    }

    static function isWindows(){
        $os = static::getOS();

        return ($os == 'Windows' || $os == 'WIN32' || $os == 'WINNT');
    }

    static function isUnix(){
        $os = static::getOS();

        return (in_array($os, ['Linux', 'BSD', 'Darwin', ' NetBSD', 'FreeBSD', 'Solaris']));
    }


    /*
        https://factory.dev/pimcore-knowledge-base/how-to/execute-php-pimcore
    */
    static function runInBackground(string $cmd, string $output_path = null, $ignore_user_abort = true, int $execution_time = 0)
    {
        ignore_user_abort($ignore_user_abort);
        set_time_limit($execution_time);

        switch (PHP_OS_FAMILY) {
            case 'Windows':
                if ($output_path !== null){
                    pclose(popen("start /B $cmd >> $output_path ", "r")); 
                } else {
                    pclose(popen("start /B $cmd", "r")); 
                }                
                break;
            case 'Linux':
                if ($output_path !== null){
                    $pid = (int) shell_exec("nohup nice -n 19 $cmd > $output_path 2>&1 & echo $!");
                } else {
                    $pid = (int) shell_exec("nohup nice -n 19 $cmd > /dev/null 2>&1 & echo $!");
                }
                break;
            default:
            // unsupported
            return false;
        }

        return $pid ?? null;
    }
}


<?php
    namespace Surricate;

    abstract class Log{
        
        public static function init(){
            self::setPhpErrorDisplay($_ENV['ENV_MODE']);
            self::error();
        }

        private static function error($e=null){
            ini_set('error_log',Sources::path('configs').'Log/error_log.txt');
        }
        
        private static function setPhpErrorDisplay($env){
            if($env=='debug'){
                ini_set('display_errors', 1); 
                error_reporting(E_ALL);
            }
        }
    }
?>
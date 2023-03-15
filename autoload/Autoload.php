<?php
declare(strict_types=1);
namespace Surricate;

use Dotenv\Dotenv;

class Autoload{
    
    private const CLASSES_SOURCES = [
        'app',
        'controllers',
        'models',
        'configs',
        'routes',
        'lib'
    ];
    public static function init(){
        require_once('vendor/autoload.php');
        define("ROOT",str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));
        self::loadEnv(ROOT);
        
        spl_autoload_register(function($class){
            self::namespaceToClass($class);
            self::autoloadClass($class);
        });
        Sources::setEnv();
        Log::init();
        Security::init();
        Router::init();
    }
    
    private static function autoloadClass($class) {
        
        $sources = array_map( function($sources) use($class) {
            return ROOT.'src/'. $sources . '/' . $class . '.php';
        }, self::CLASSES_SOURCES );
        
        foreach($sources as $s){
            if(file_exists($s)){
                require_once $s;
            }
        }
    } 
    
    public static function namespaceToClass(&$class){
            $class=preg_replace('/(.)*\\\/','',$class);
    }
  
    public static function classToNamespace($controller){
        return '\\'.__NAMESPACE__.'\\'.$controller;
    }

    private function autoloadStyle(){
        $params=explode('/',$_GET['p']);

    }

    private static function loadEnv($dir=__DIR__){
        $doteEnv= Dotenv::createImmutable($dir);
        $doteEnv->load();
    }
   
}

?>
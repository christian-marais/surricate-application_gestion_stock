<?php
 namespace Surricate;

class Router{
   
    
    public static function init(){
         self::setRedirection();
        self::setDate("today");
    }

    private static function setRedirection(){
        $params=PARAMS;
        if($params[0]!=""){
            $controller = ucfirst(Security::classAlias($params[0]));if(!empty($params[1])&&($params[1]==Security::getDbToken())){
                $params[2] =$params[1];
                $params[1] = 'index';
            }
            $action= isset($params[1]) ? $params[1] : 'index';
            $controller_path= Sources::path('routes');
            //$controller_path= ROOT.'src/controllers/'; Decommenter pour ne pas utiliser la méthode Sources::path
            if(!file_exists($controller_path.$controller.'.php')){
                header('Location: '.BASE_URI.'pages/erreur404');  
            }
        }else{
            $controller="Pages";
            $action="index";
        }
        $controller=Autoload::classToNamespace($controller);
        $controller = new $controller();
        if(method_exists($controller,$action)){
            unset ($params[0],$params[1]);
            call_user_func_array([$controller,$action],$params);
        }else{
           header('Location: '.BASE_URI.'pages/erreur404');
        }
    }   

    public static function localDate(){
        $date=getdate();
        ($date['mon']<10)?$date['mon']='0'.$date['mon']:"";
        ($date['mday']<10)?$date['mday']='0'.$date['mday']:"";
        return $date['year'].'-'.$date['mon'].'-'.$date['mday'];
    }

    public static function setDate($date='date'){
        (!isset($_POST[$date]))?$_POST[$date]=self::localDate():"";
        return $_POST[$date];
    }

    public static function redirection($redirection){
        header('Location: '.BASE_URI.$redirection.URI_TOKEN);
    } 
}
?>
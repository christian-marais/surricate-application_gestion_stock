<?php
   namespace Surricate;
    
    class Sources {
        const TEMP_FOLDER = ROOT.'temp/';
        const ARTICLE_IMAGE =[
            'ext'=> ['jpg','png'],
            'height' => 2000,
            'width'=> 2000,
            'size' => 500,
            'path' =>'images/banque/articles/'
        ];

        const PARAM_IMAGE =[
            'ext'=> ['jpg','png'],
            'height' => 2000,
            'width'=> 2000,
            'size' => 500,
            'path' =>'images/banque/param'
        ];

        public static function setEnv(){
            self::defineEnv();
        }
        
        public static function path($data='controllers'){
            return ROOT.self::source($data).'/';
        }
       
        private static function source($param,$dir='',&$continue=true){
            $uri=ROOT.$dir;
            if(gettype(strpos($dir,$param))==='integer'){
                $resultats=$dir;
            }else{
                $p=0;
                $results=scandir($uri);
                foreach($results as $result){
                    if($p>2 && $continue){
                        if(gettype(strpos($result.'/',$param))==='integer'){
                            $continue=false;
                            $resultats=$dir.$result;
                        }else{
                            if(is_array(@scandir($uri.$result))){
                                $resultats=$dir.$result.'/';
                                $resultats=self::source($param,$resultats,$continue);
                            }else{
                                $resultats='non trouvé';
                            }
                        }
                    }
                    $p++;
                }
                ($continue==true)?$resultats=false:'';
            }
            return $resultats;
        }
     
        private static function defineEnv(){
            session_start();
            (isset($_SERVER['HTTPS']))?define('HTTPS','https'):define('HTTPS','http');
            define("BASE_URI",HTTPS.'://'.$_SERVER['HTTP_HOST'].substr(ROOT,strlen($_SERVER[ 'DOCUMENT_ROOT' ])));
            
            define("VERSION",$_ENV['APP_VERSION']);
            define('MEMBER_HOME','stock/utilisations');
        }
        
    }
?>
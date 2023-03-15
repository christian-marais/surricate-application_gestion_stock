<?php
     namespace Surricate;

    abstract class Methodes {
        public static function joinArray(&$array1,$array2){
            foreach($array2 as $key => $value){
                $array1[$key]=$value;
            }
        }
       
        public static function purgeItemWithIntKeyOfArray($data,$mode='fetchAll'){
            $results=[];
            if($mode==="fetchAll"){
                foreach($data as $s){
                    array_push($results,array_filter($s,[$this,'ifKeyIsInteger'],ARRAY_FILTER_USE_KEY));
                }
            }else{
                $results=array_filter($data,[$this,'ifKeyIsInteger'],ARRAY_FILTER_USE_KEY);
            }
            return $results;
        }

        protected function secure (&$data){
            try{
                if(!$this->useMethods($data)){//
                    throw new Exception("probleme de variable");  
                }
            }catch(Exception $e){
            }
        }
        
        protected function extractKeys($data){
            foreach($data as $item => $values){
                $datas=array_keys($values);
            }
            $p=0;
            foreach($datas as $data){
                if(gettype($data)=='integer'){
                    unset($datas[$p]);
                }
                $p++;
            }
            return $datas;
        }

        private function ifKeyIsInteger($data){
            return !(gettype($data)=='integer');
        }

        protected function useMethods(&$data,$method=['htmlspecialchars'],$p=null){
            foreach($method as $s){
                switch(true){ 
                    case(gettype($data)=="array"):
                        foreach($data as $element => &$value){
                            if($value!=null){
                                switch(true){ 
                                    case(gettype($value)=="array"):
                                        if($this->secure($value)){
                                            $data[$element][$value]=$this->secure($value);
                                        }
                                    break;
                                    default:
                                    if($p===null){
                                        $data[$element]=$s($value); 
                                    }else{
                                        $data[$element]=$p->$s($value);
                                    }
                                }
                            }
                        }
                    break;
                    default:
                    $data=$s($data);
                }
            }
        }

        public function _isset(string $data,$params=''){
            $results="";
            switch(true){
            case(ctype_upper($data)):
                try{
                    if(!@define($data,$params)){
                        throw new Exception('On définit une constante déjà définie');
                    }
                }catch(Exception $e){
                    echo 'La méthode '.__FUNCTION__.' a rencontré une erreur : '.$e->getMessage();
                }
                break;
            case(!empty(strpos($data,'['))):
                $pos=strpos($data,'[');
                $pos=substr($data,$pos+1,-1);
                $variable=substr($data,0,$pos);
                ob_start();
                echo isset(${$variable}[$pos])?'':${$variable}[$pos]=$params;
                $results=ob_get_clean();
                break;
            case(!empty(strpos($data,'$'))):
                $data=substr($data,1);
                ob_start();
                echo isset(${$data})?${$data}:${$data}="";
                $results=ob_get_clean();
                break;
            default:
                if($data='message'){
                    isset($_POST[$data][0])?'':$_POST[$data][0]=''; 
                }else{
                    isset($_POST[$data])?'':$_POST[$data]='';  
                }
                
            }
            return $results;
        }
       
        public static function _die($data){
            die(var_dump($data));
        }
        
    }
?>
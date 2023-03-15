<?php
     namespace Surricate;

    abstract class DB extends Security{
        private static $env;
        
        public static function setEnv(){
            self::$env = $_ENV['ENV_MODE'];
            self::setDBErrorMode(self::$env);
        }

        private static function setDBErrorMode($data='prod'){
            switch(true){
                case($data==='dev'):
                    define('ERROR_MODE',\PDO::ERRMODE_WARNING);
                    break;
                case($data==='prod'):
                    define('ERROR_MODE',\PDO::ERRMODE_SILENT);
                    break;
                case($data==='debug'):
                    define('ERROR_MODE',\PDO::ERRMODE_EXCEPTION);
                    break;
            }
        }

        protected function testSql($sql,$valeurs){
            if(self::$env==='debug'){
                echo'<br/>[variables post] : ';
                var_dump(json_encode($_POST));
                echo'<br/><br/>';
                var_dump($sql);
                echo'<br/><b>[variables pdo]</b>: ';
                var_dump(json_encode($valeurs));
                echo'<br/>';
            }
        }

        protected function debugSql($connexion){
            ob_start();
                echo '['.date('Y-m-d H:i:s').']  ';
                $connexion->debugDumpParams();
                echo '----------------------------';
            $content=ob_get_clean();
            file_put_contents(Sources::path('configs').'Log/sql_log.txt',$content,FILE_APPEND);
        }

        public function getMetaData($type=null){
            $data=str_replace(['_'.$this->table],'',$this->datas);
            ($type==null)?$data=$this->datas:'';
            ($type=='id')?$data=$this->datas[0]:'';
            return $this->metadatas = $data;
        }
       
        protected function enDecryptData($data,$key=self::ENCRYPT_SQL_KEY,$function = self::ENCRYPT_SQL_METHOD) {
            return $function.' ('.$data.',"'.$key.'")';
        }
        
        protected function enDecryptDataIfMatch($data,$search=false,$key=self::ENCRYPT_SQL_KEY,$function=self::ENCRYPT_SQL_METHOD,$s=0){
            $params=implode(',',array_fill_keys(array_keys($data),'?'));
            if($search){
                $params='';
                $this->matchKey($data,$search,$matchedKey);
                $newData=array_fill_keys(array_keys($data),'?');
                foreach($newData as $item =>$value){
                    $params.=(in_array($s,$matchedKey))?preg_replace('/(.+)/',$this->enDecryptData('?',$key,$function),$value):preg_replace('/(.+)/','?',$value);
                    $params.=',';
                    $s++;
                }
                $params=trim($params,',');   
                $params=preg_replace('/(,,)/',',?,',$params);
            }
            return $params;
        }

        protected function matchKey($data,$search,&$results=[],$p=0){
            foreach($search as $s){
            $results[$p]=array_search($s,array_keys($data));
            }
        }
    }
?>
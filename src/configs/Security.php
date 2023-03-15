<?php

namespace Surricate;
use \Exception;  
    
    abstract class Security{

        public const PERMISSION_CONTENU = [
            'INDEX'=>1,
            'MENUS'=>2,
            'ARTICLES'=>3,
            'FOURNISSEURS'=>4,
            'UTILISATEURS'=>5,
            'FORMATIONS'=>6,
            'GROUPES'=>7,
            'CENTRES'=>8,
            'CURSUS'=>9,
            'ROLES'=>10,
            'ACHATS'=>11,
            'LIVRAISONS'=>12,
            'UTILISATIONS'=>13,
            'JOURNAUX'=>14,
            'API'=>15
        ];
        protected const PERMISSION_EDITION = [
            'LECTURE'=>1,
            'CREATION'=>2,
            'MODIFICATION'=>4,
            'SUPPRESSION'=>8
        ];
        private const NOT_ALLOWED_CHARS = array('http','https','ftp','€','#','+','*',"'","=",'"','²','&','~','{','(','[','|','`','^',')','}','=','}','^','$','£','¤','%','*',';',':','/','\\','§','>','§','©','<');
        protected const NOT_ALLOWED_EXT=['.php,.htaccess,.java,.js'];
        private const SECURTY_METHOD=['htmlentities'];
        private const ENCRYPT_KEY='';
        protected const ENCRYPT_SQL_METHOD='AES_ENCRYPT';
        protected const ENCRYPT_SQL_KEY = '1c8dc6af3ee90234f9a26ffb09ae3c58d0047b59';
        private const CLASS_ALIAS = [
            "administration" => "admin",
            "Appis"=>"api",
            "login"=> "auth",
            "gestion"=>"stock"
        ];
        private const ENCRYPT_TOKEN_KEY ="65d65ecdd41efc27d1771eeb9de96bc754231704";
        private const ENCRYPT_CIPHER ="aes-128-gcm";
        private const COOKIE_NAME='surricate';
        private const COOKIE_DURATION='5';
        private const COOKIE_REP='/';
        private const COOKIE_HTTPS=false;
        private const COOKIE_HTTP_ONLY=true;
        private static $openssliv;
        public static $uriToken;
        private static $opensslTag;
        private const USE_STRICT_CLASS_ALIAS=false;
        protected static $token;
        protected static $cookieToken;

        public static function init(){
            self::generateToken();
            self::methodsToArrayValues($_POST,['translateSpecialChars'],'Security');
            self::sanitizePostAndSession();
            self::sanitizeUri();
        }

        private static function generateToken(){
            $bytes = random_bytes(20);
            return self::$token= bin2hex($bytes);
        }

        public function opensslEncrypt($data,$key=self::ENCRYPT_TOKEN_KEY){

            $cipher =  self::ENCRYPT_CIPHER;
            if (in_array($cipher, openssl_get_cipher_methods()))
            {
                $ivlen = openssl_cipher_iv_length($cipher);
                $iv = openssl_random_pseudo_bytes($ivlen);
                $results=openssl_encrypt($data, $cipher, $key, $options=0, self::$openssliv=$iv, $tag); 
                self::$opensslTag=$tag;
            }
            return $results;
        }

        public function opensslDecrypt(&$data,$cipher=self::ENCRYPT_CIPHER,$key=self::ENCRYPT_TOKEN_KEY){
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $data = openssl_decrypt($data, $cipher, $key, $options=0,$iv=self::$openssliv, $tag=self::$opensslTag);
        }
        
        protected function encryptPass($data){
            //crypt()
            return password_hash($data, PASSWORD_BCRYPT, $options =['cost'=>8]);
        }
        
        protected function decryptPass($data,$hash){
            return password_verify($data,$hash);
        }
        
        protected function setTokenSession(){
            self::$token=self::generateToken();
            $_SESSION['session']=self::$token;
            
        }
        
        protected function setTokenCookie(){
            self::$cookieToken=self::generateToken();
        }

         
        protected function setCookie($token,$day=self::COOKIE_DURATION,$repertoire=self::COOKIE_REP,$https=self::COOKIE_HTTPS,$httpOnly=self::COOKIE_HTTP_ONLY){
            setcookie( self::COOKIE_NAME,$token,time() + strtotime('+'.$day.' days'),$repertoire, $_SERVER['HTTP_HOST'],$https,$httpOnly);
        }
        protected function unsetCookie(){
            setcookie(self::COOKIE_NAME,self::$token,1);
        }

        protected function getToken(){
            return self::$token;
        }

        public static function getDbToken(){
            $results=null;
            if(!empty($_SESSION['id'])){
                $user= new Utilisateur();
                $user=$user->getOneUser('id_user',$_SESSION['id']);
                $results= $user['token'];
            }
            return $results;
        }

        public static function verifyUriToken(){
            $results=false;
            if(!empty($_SESSION['id'])){
                $user= new Utilisateur();
                $user=$user->getOneUser('id_user',$_SESSION['id']);
                $results= (in_array($user['token'],PARAMS));
                
            }
            if(!$results){
               header('Location: '.BASE_URI);
              
            }
        }

        public static function setDatas($datas){
            foreach($datas as $data){
                foreach(array_keys($data) as $s){
                    (empty($_POST[$s]))?$_POST[$s]="":"";
                }
            }  
        }

        protected function setData(){
            foreach($this->datas as $data){
                (!empty($_POST[$data]))?$this->data[$data]=$_POST[$data]:$_POST[$data] ='';
            }
            if(in_array($this->table,['article',])){// si on est dans la table article
                array_map(function($data){(empty($this->data[$data]))? $this->data[$data]=0:"";},['pu','stock_de_securite']);// on rajoute les valeurs pu et sucrité
        
            }
       
        }
    
        public static function methodsToArrayValues(&$data,$method=null,$class=null,$p=0){
            if($method!=null){
            $namespace=('\\'.__NAMESPACE__.'\\'.$class);
                foreach($method as $s){
                    switch(true){ 
                        case(is_array($data)):
                            foreach($data as $element => &$value){
                                if($value!=null){
                                    switch(true){ 
                                        case(is_array($value)):
                                            $p++;
                                            $data[$element][$p]=Security::methodsToArrayValues($value,$method,$class,$p);
                                            if($data[$element][$p]==null){
                                                unset($data[$element][$p]);
                                            }
                                        break;
                                        default:
                                        ($class===null)?$data[$element]=$s($value):$data[$element]=$namespace::$s($value);
                                    }
                                }
                            }
                        break;
                        default:
                        ($class===null)?$data=$s($data):$data=$namespace::$s($data);
                    }
                }
            }
        }
      
        
        private static function sanitizePostAndSession(){
            self::methodsToArrayValues($_POST,self::SECURTY_METHOD);
            self::methodsToArrayValues($_SESSION,self::SECURTY_METHOD);
        }

        public static function sanitizeUserData($data){
            self::methodsToArrayValues($data,Security::SECURTY_METHOD);
        }
        
        protected function isExtIn($text,$ext=null,$isExtIn=false){
            if($ext!=null){
                foreach($ext as $e){
                    (preg_match('/(.'.$e.')/',$text,$data))?$isExtIn=true:'';
                }
            }
            return $isExtIn;
        }

        protected function isDoubleExt($filename){
            return preg_match('/(.*\..*\.)|(.+)\.$/',$filename,$data); 
        }
        
        protected static function createHtaccess($folder,$content,$corrupted=false){ 
            $htaccess=$folder."/.htaccess";
            if (!file_exists($htaccess)||$corrupted){
                try {//on tente
                   if(file_put_contents($htaccess,$content)){
                       throw new Exception("Impossible de créer le fichier htaccess de File. ");
                   }
                }catch(Exception $e) {
                    echo $e->getMessage();
                }
            }else{
                (file_get_contents($htaccess)==$content)?'':self::createHtaccess($folder,$content,true);
            }
        }
        
        protected function message($message,$id_message="notification"){
            (!empty($_COOKIE['message']))?$message=$_COOKIE['message'].'/'.$message:'';
            setcookie('message','Info-'.$id_message.': '.$message, strtotime('10 seconds'),'/',$_SERVER['HTTP_HOST']);
      
        }

        private static function sanitizeUri(){
          $params=explode('/',$_GET['p']);
           self::sanitizeUserData($params=explode('/',$_GET['p']));
           define("PARAMS",$params);
        }

        public static function classAlias($alias){
           return @self::alias($alias,self::CLASS_ALIAS,self::USE_STRICT_CLASS_ALIAS);
        }

        private static function alias($alias,$aliasScheme,$aliasMode){
            $onlyAlias=(!in_array($alias,array_keys($aliasScheme))&& $aliasMode && strtolower($alias)!=='pages')?true:false;// si n'est pas dans l'array,qu'on est en strict à l'exception de la classe pages on indique l'erreur avec true
            foreach ($aliasScheme as $s => $class){
                $alias=(strtolower($alias)===strtolower($s))?$class:$alias;
            }
            return ($onlyAlias)?'erreur':$alias;
        }

        protected function checkPermission($allowedUsers,&$role=null){
            $results=true;
            foreach($allowedUsers as $user){ 
                (!empty($_SESSION['role']) && false!==strpos($_SESSION['role'],$user))?$role=$_SESSION['role']:$results=false;//le role affiché par l'utilisateur correspond si oui il récupère le role si non renvoie false
            }
            return $results;
        }

        protected function checkUserContenuPermission($nomPage,$permString,$egaliteStrict=false){
            $allowedScore=0;
            foreach($permString as $perm){
                if($this->translatePermStringToValue($perm)>=0){
                    $allowedScore=$allowedScore+$this->translatePermStringToValue($perm);
                }
            }
            $idPage=$this->translatePermStringToValue(strtoupper($nomPage),Security::PERMISSION_CONTENU);
            $results=false;
            $roleFromDb = new Role();
            if(!empty($_SESSION['role'])){
                $roles= explode(',',$_SESSION['role']);
                $score=0;
                foreach($roles as $role){
                    $userRole=$roleFromDb->getOneRole('code_role',$role);
                    $userPermission=@json_decode($userRole["permission"]);
                    if(gettype($userPermission)=='array'){
                        foreach($userPermission as $permission){
                            ($permission->id == $idPage && $permission->permissions >=$score)?$score=$permission->permissions:'';               
                        }
                    }
                }
                if($egaliteStrict==true){
                    $results = $score==$allowedScore;
                }else{
                    $results = ($score>=$allowedScore);
                }
           }  
            //Security::verifyUriToken(); 
          return $results;
       
        }

        /**
         * FONCTION qui genere l'uri
         */
        protected function setUriToken($uri){
            $user= new Utilisateur();
            $user=$user->getOneUser('id_user',$_SESSION['id']);
            self::$uriToken=$user['token'];
            $uri=$uri.'/'. self::$uriToken;
            return $uri;
        }
        /**(M)Description : fonction qui configure la table de permission final de l'utilisateur
         * (O) l'objet table de permission de l'user
         * (I) Rien
         * @return Json Object tablle de permission finale de l'utilisateur
         */
        protected function setUserPermissionTable(){
            $emptyPermissionTable=json_decode($this->getEmptyPermissionTable());//on recupère une table vide des permissions comprenant toutes les pages    
            if($permissions=$this->Role->getOneRole('code_role',$_POST['role_choice'])){// on recupère les infos de role
                $permissionAttribute=json_decode($permissions['permission']);// on récupère le json décodé des permissions
                foreach ($permissionAttribute as $permission){//pour chaque objet permission
                    $permission->permissions = $this->getUserPermissionTable($permission->permissions);//on traduit la valeur de la permission en table de permission
                } 
                foreach($emptyPermissionTable as $emptyTable){//pour chaque page
                    foreach($permissionAttribute as $permission){//et chaque permission accordé à l'user
                        ($emptyTable->id==$permission->id)? $emptyTable->permissions=$permission->permissions:'';// si l'id de page correspond à celui accordé à l'user, les permissions de l'user viennet remplacer celle qui étaient vides
                    }
                }
            }
            return $emptyPermissionTable;//on retourne la table vide de permission mise à jour des permission utilisateurs
        }

        /**Description
         * Fonction qui retourne une premission de table nulle pour toutes les pages
         * La traduction se fait à partir de l'array Security::PERMISSION_EDITION et PERMISSION_CONTENU
         * @param array permission suite de string de permission
         * @return int valeur de la permission
         */
        protected function getEmptyPermissionTable(){
            $p=0;
            $permission=array_combine(array_keys(self::PERMISSION_EDITION),array_fill_keys(self::PERMISSION_EDITION,""));//on remplit pour chaque page avec une valeur vide;
            
            foreach(Security::PERMISSION_CONTENU as $page =>$valeur){
              $emptyTable[$p]=['id'=>$valeur,'permissions'=>$permission];
              $p++;
            }
            return json_encode($emptyTable);
        }
        /**Description
         * Fonction qui à partir d'une suite de string de permission ex'suppresion' retourne la somme total de leur valeur octale
         * La traduction se fait à partir de l'array Security::PERMISSION_EDITION 
         * @param array permission suite de string de permission
         * @return int valeur de la permission
         */
        public function setPermission(...$permissions){
            $ownedPermission=0;// on définit la permission à 0
            foreach($permissions as $permission){// pour chaque string de permission
                if(in_array(strtoupper($permission),array_keys(Security::PERMISSION_EDITION))){// si le string de permission est dans les clés du tableau de permission
                    $ownedPermission=$ownedPermission + Security::PERMISSION_EDITION[strtoupper($permission)];//on rajoute la valeur de la permission à notre valeur initiale de 0
                }
            }
            return $ownedPermission;//on retourne la valeur finale de la permission
        }
        /**Description
         * Fonction qui a partir d'une valeur de permission rend ses différentes composantes de valeur
         * ex:permission de 9 va retourner un array avec les valeurs  8 et 1
         * La traduction se fait à partir de l'array Security::PERMISSION_EDITION 
         * @param int permissionValue valeur de la permission
         * @return array permission
         */
        public function readPermission($permissionValue){
            $perms='';//on definit un string de permission accordées à vide
            $permission =array_reverse(Security::PERMISSION_EDITION);//on inverse le tableau de permission pour l'avoir en ordre décroissant et commencer avec la plus petite valeur
            foreach($permission as $p){//pour chaque valeur de permission du tableau
                if(($permissionValue - $p) >=0){//si on peut soustraire cette valeur à la permission renseignée
                    $perms.=$p.',';//elle fait partie des permissions accordées alors on la rajoute à nos permissions accordées
                    $permissionValue=$permissionValue - $p;// et on décrémente la valeur de la permission renseignée de sa valeur
                }
            }
            return explode(',',trim($perms,','));//on enlève les , parasites en fin et traduit le string de permission en tableau de permission
        }
        /**Description
         * Fonction qui a partir d'une valeur de permission retourne la table complète de permission de l'utilisateur
         * ex:permission de 9 a des valeurs de permission 8 et 1 et va donc retourner un array de permission = 
         * ['LECTURE'=>1,
         *  'ECRITURE' =>"",
         *  'MODIFICATION' => "",
         *  'SUPPRESSION' => 8
         * ]
         * La traduction se fait par défaut à partir de l'array tableau de permission Security::PERMISSION_EDITION 
         * @param int permissionValue valeur de la permission
         * @param array permTable tableau de permission 
         * @return array permission
         */
        public function getUserPermissionTable($permission,$permTable=Security::PERMISSION_EDITION){
            $userPermission=$this->readPermission($permission);// on récupère les valeurs de permissions aacordées à l'utilisateur
            foreach($permTable as $perms =>$value){// pour chaque permission du tableau de permission fourni
                $permTable[$perms]=(in_array($value,$userPermission))?1:"";// si la permission ne fait pas partie de la permission accordée à l'user on la vide
            }
            return $permTable;// on retourne le nouveau tableau de permission qui retourne la valeur de chaque permission de l'user; la valeur de la permission sera vide "" s'il n'a pas cette permission
        }
        /**Description
         * Fonction qui a partir d'une valeur de permission redonne le nom de la permission correspondante
         * ex:permission de 8 va retourner un string SUPPRESSION
         * La traduction se fait à partir de l'array Security::PERMISSION_EDITION 
         * @param int permissionValue valeur de la permission
         * @return string permission
         */
        public function translatePermValueToString($value,$permArray=Security::PERMISSION_EDITION){
            $perms=array_flip($permArray);
            foreach($permArray as $permission){
                (strtoupper($value)==$permission)?$results=$perms[$value]:'';
            }
            return (empty($results))?'':$results;
        }
        /**Description
         * Fonction qui retourne la clef ou le nom de la page correspondant à l'id fourni
         * ex:un id de 1 va retourner un string index
         * La traduction se fait à partir de l'array Security::PERMISSION_CONTENU
         * @param int idPage valeur de la permission
         * @return string nom de l'index
         */
        public function translateIndexValueToString($idPage){
           return $this->translatePermValueToString($idPage,Security::PERMISSION_CONTENU);
        }
        /**Description
         * Fonction qui a partir d'un string de permission redonne la valeur de la permission correspondante
         * ex:un string SUPPRESSION va retourner une valeur 8
         * La traduction se fait à partir de l'array Security::PERMISSION_EDITION 
         * @param strin string de la permission
         * @return int valeur de la permission
         */
        public function translatePermStringToValue($string,$permArray=Security::PERMISSION_EDITION){
            foreach(array_keys($permArray) as $permission){
                (strtoupper($string)==$permission)?$results=$permArray[strtoupper($string)]:'';
            }
            return (empty($results))?'':$results;
        }

        /**Description
         * Fonction retourne le tableau des clés de permission existantes
         * @return array clef de permission
         */
        public function getPermissionTable(){
            return array_keys(self::PERMISSION_EDITION);
        }
        /**Description
         * Fonction retourne le tableau des clés des pages existantes
         * @return array clef des pages
         */
        public function getContenuTable(){
            return self::PERMISSION_CONTENU;
        }

         /**Description
         * Fonction qui filtre et retourne le tranmis sans les caractères interdits et traduit les autres
         * @param string string à modifier
         * @return string string sans les caractères
         */
        public static function translateSpecialChars($string){
            $notAllowedChars=Security::NOT_ALLOWED_CHARS;
            $string = str_replace($notAllowedChars, '', $string);
            # On remplace les variantes de "e"
            $string = str_replace(array('ê', 'ë', 'é', 'è'), 'e', $string);
            # On remplace les variantes de "u"
            $string = str_replace(array('ù', 'µ', 'û', 'ü'), 'u', $string);
            # On remplace les variantes de "a"
            $string = str_replace(array('à', 'ä', 'â'), 'a', $string);
            # On remplace les variantes de "o"
            $string = str_replace(array('ô', 'ö', 'ò'), 'o', $string);
            # On remplace les variantes de "i"
            $string = str_replace(array('î', 'ï', 'ì', ), 'i', $string);
            # On remplace les variantes de "y"
            $string = str_replace(array('ÿ'), 'y', $string);
            # On remplace les variantes de "c"
            $string = str_replace(array('ç'), 'c', $string);
            # On remplace les variantes de "n"
            $string = str_replace(array('ñ'), 'n', $string);
             
            return $string;
        }
        
    }
?>
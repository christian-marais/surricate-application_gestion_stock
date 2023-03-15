<?php
 namespace Surricate;
 use \PDO;
 use \Exception;
    
    class Utilisateur extends Model{

       

         /**propriété contenant un array des champs de table  à encrypter dans la génération du script sql */
        private const ENCRYPTED_PROPERTIES =['password_user'];

        /**propriété contenant un array des champs de table à retirer du LMD et du script sql si null. Dans l'insert on met le champ à vide pour éviter le réencryptage du password. On a donc besoin de le retirer des manipulations si il est vide 
         * Sert à retirer des champs null non voulues des manipulations
        */
        protected $dataToUnsetIfNull= ['password_user'];

        public function __construct(){
            $this->table='user';
            $this->getConnection();
             /**propriété contenant un array des champs de tableà utiliser */
            $this->datas=[
            'id_user','nom_user','prenom_user','fonction_user','email_user','password_user','id_groupe','active','id_cursus'
        ];  
            //$this->getMetaData();//on applique la méthode getMetadat pour avoir les noms retravaillés des champs
            $this->metadatas=[
                'ID','Nom','Prénom','Fonction','Email','Password','Groupe','Active','Cursus'
            ]; 
        }   

          /**Méthode qui sert a authentifier un utilisateur par son mot de passe et son mail. Elle retourne l'utilisateur s'il existe
           * @param string mail de l'utilisateur 
           * @param string password mot de passe de l'utilisateur
        */
        public function checkUser($mail,$password){
            $this->table='user';
            array_map([$this,'secure'],array(&$mail,&$password));
            $sql='SELECT * FROM '.$this->table.' WHERE active=1 AND email_user = ? AND password_user = '.$this->enDecryptData('?',$password);
            $query=$this->connexion->prepare($sql);
            $query->execute(array($mail,$password));
            return $query->fetch();
        }

        public function getMetaDatas()
        {
            return $this->metadatas;
        }

        public function insertOneUser(){
            $datas=array_slice($this->datas,1);
            foreach($datas as $entry){
                (empty($_POST[$entry]))? $_POST[$entry]="" : "";
                $data[$entry] = $_POST[$entry];
            }
            
            $this->unsetEmptyInsertion($data);
            (empty($data['active']))?$data['active']=0:'';
            (empty($data['password_user']))?$password=null:$password=$data['password_user'];// si le mot de passe est vide on ne le met pas à jour
            $results=true;
            try{ 
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $this->insertOne($data,self::ENCRYPTED_PROPERTIES,$password); 
            }catch(Exception $e){
               // var_dump($e->getMessage());
                $results=false;
            }
            return $results;
        }

        public function countUsers($search=null){
            $this->table='user';
            ($search!=null)?$sql=" t1.nom_user LIKE CONCAT('%',?,'%') OR t2.id_groupe LIKE CONCAT('%',?,'%')":$sql=null;
            $countSql=' as t1 LEFT JOIN groupe as t2 ON t2.id_groupe = t1.id_groupe ';
            if(!empty($_SESSION["id"])){
                $centrGroup=$this->getUserCentre($_SESSION["id"]);
                if(!empty($centrGroup) && strtoupper($centrGroup['id_groupe'])!=='ADMIN'){
                    $countSql.=' WHERE t2.id_centre LIKE "'.$centrGroup["id_centre"].'"';
                }
            }
            ($search!=null)?$search=array_merge(array($search),array($search)):'';
            return $this->countAll($sql,$search,$countSql);
        }
        public function getAllUsers($search=null,$page=null){
            $this->table='user';
            $this->sqlViewByCenter= '  AS t1 INNER JOIN groupe as t2 ON t2.id_groupe = t1.id_groupe
                            WHERE t2.id_centre LIKE :currentCentre ';
            $this->setViewByCentre();
            $this->setSearchAndOffset($this->t1.'nom_user',$this->t2.'id_groupe',$search,$page,$sql,$data,$this->sqlCondSyntaxe);
            return $this->getAll($sql,$data);
        }

        
    
        public function getUsersByGroup($data){
            $sql= 'SELECT * 
                    FROM user 
                    INNER JOIN groupe ON groupe.id_groupe = user.id_groupe
                    WHERE user.id_groupe = ? ';
            $req=$this->connexion->prepare($sql);
            $req->execute(array($data));
            return $req->fetchAll();
        }

        public function getUsersByRole($data){
            $sql= 'call getUserByRole(?)';
            $req=$this->connexion->prepare($sql);
            $req->execute(array($data));
            return $req->fetchAll();
        }

      
        public function getOneUser($champ='id_user',$valeur=''){
            array_map([$this,'secure'],array(&$champ,&$valeur)); 
            $sql= 'SELECT * 
            FROM user
            LEFT JOIN groupe ON user.id_groupe = groupe.id_groupe
            WHERE '.$champ.' = ?';
            
            $query=$this->connexion->prepare($sql);
            $query->execute(array($valeur));
            return $query->fetch();
           
        }

    
        public function deleteOneUser($champ,$valeur){
            return $this->deleteBy($champ,$valeur);
        }
        
        public function updateOneUser($id,$valeur_id,$data=null){
            if($data===null){
                foreach(array_slice($this->datas,1) as $s){
                    isset($_POST[$s])?'':$_POST[$s]="";
                    $data[$s]=$_POST[$s];
                    //$results[$data]=$this->secure($_POST[$data]);
                }
                $this->unsetEmptyInsertion($data);
                (empty($data['active']))?$data['active']=0:'';
                (empty($data['password_user']))?$password=null:$password=$data['password_user'];// si le mot de passe est vide on ne le met pas à jour
            }
         
            return $this->updateBy($id,$valeur_id,$data,self::ENCRYPTED_PROPERTIES,$password);
        }

        public function getUserRole($email){
            $sql='SELECT T.role,role.permission FROM (SELECT gestion_role.code_role as role FROM user
            INNER JOIN groupe ON groupe.id_groupe=user.id_groupe
            INNER JOIN gestion_role ON gestion_role.id_groupe=user.id_groupe
            WHERE user.email_user= ? ) AS T
            INNER JOIN role ON role.code_role = T.role';
            $req=$this->connexion->prepare($sql);
            $req->execute(array($email));
            return $req->fetchAll();
            
        }
        public function getUsername($email){
            $sql='SELECT CONCAT(nom_user," ",prenom_user) as username FROM user
                    WHERE user.email_user=?';
            $req=$this->connexion->prepare($sql);
            $req->execute(array($email));
            return $req->fetch();
        }
        public function getCentreByUserId($userId){
            $sql='call get_centre(?)';
            $req=$this->connexion->prepare($sql);
            $req->execute(array($userId));
            return $req->fetch();
        }
    
        
        
    }
?>

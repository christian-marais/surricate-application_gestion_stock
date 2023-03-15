<?php
 namespace Surricate;
 use \PDO;
 use \Exception;

class Role extends Model{

    public function __construct(){
        $this->table='role';// on met la table à manipuler par défaut
        $this->getConnection();// on initialise la connexion
        $this->datas=[];//variable contenant les nom des champs
        (empty($_POST['code_role_create']))? $_POST['code_role_create']="" : "";// a chaque instanciation on s'assure les post soient correctement initialisés
        (empty($_POST['nom_role_create']))? $_POST['nom_role_create']="" : "";
    }   


    /**
     * (M) Fonction qui compte le nombre de roles
     * (O) int du nombre de groupe
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int 
     */
    public function countRoles($search=null){
        $this->table='role';
        ($search!=null)?$sql=" t1.code_role LIKE CONCAT('%',?,'%') OR t1.nom_role LIKE CONCAT('%',?,'%')":$sql=null;
        $countSql=' as t1 INNER JOIN gestion_role AS t2 ON t2.code_role = t1.code_role 
        INNER JOIN groupe as t3 on t3.id_groupe = t2.id_groupe ';
        if(!empty($_SESSION["id"])){
            $centrGroup=$this->getUserCentre($_SESSION["id"]);
            if(!empty($centrGroup) && strtoupper($centrGroup['id_groupe'])!=='ADMIN'){
                $countSql.=' WHERE t3.id_centre LIKE "'.$centrGroup["id_centre"].'"';
            }
        }
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search,$countSql);
    }

     /**
     * (M)Fonction recupère l'ensemble des groupes
     * (O) rien
     * (I) 1 string personnalisation de la requete avec une barre de recherche, les limites
     * @param string search mots clés de recherches
     */
    public function getAllRoles($search=null,$page=null,$sql=null,$data=null){
        $this->table='role';
        $this->sqlViewByCenter= ' INNER JOIN gestion_role as t1 ON role.code_role = t1.code_role 
        INNER JOIN groupe as GROUPES on t1.id_groupe = GROUPES.id_groupe
        WHERE GROUPES.id_centre LIKE :currentCentre ';
        $this->setViewByCentre();
        $this->setSearchAndOffset($this->t1.'code_role','nom_role',$search,$page,$sql,$data,$this->sqlCondSyntaxe);
        return $this->getAll($sql,$data);
    }

      /**
     * (M)Fonction recupère l'ensemble des données de plusieurs entités
     * (O) 1 array
     * (I) 1 string personnalisation de la requete avec une barre de recherche, les limites
     * @param string suite de string de nom de table
     * @return array resultats
     */
    public function getAllDatas(...$tables){
        (empty($tables))? $tables=['role','groupe']:'';
        foreach($tables as $table){// Pour chaque table
            $this->table=$table;// On définit la table
            $this->datas[$table]=$this->getAll();// on récupère les infos et les mets dans $this->datas
            $data[$table]=$this->getAll();// retourne également le tableau de données
        }
        
        return $this->data=$data;
    }

      /**
     * (M) Fonction qui recupère un role
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function getOneRole($champ,$valeur){
        $this->table="role";
        return $this->getBy($champ,$valeur);
    }

    /**
     * (M) Fonction qui recupère les roles d'un groupe 
     * (O) array de résultats
     * (I) array 
     * @param array associatif champ valeurs
     * @return array 
     */
    public function getRolesOutOfGroupe($data){
        
        $sql='SELECT role.code_role FROM role
        WHERE role.code_role not in (SELECT code_role FROM gestion_role
                    WHERE id_groupe=?)';
        $this->testSql($sql,$data);
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        return $req->fetchAll();
    }

      /**
     * (M) Fonction qui ajoute un role à un  groupe 
     * (O) array de résultats
     * (I) 2 strings contenant les valeurs id
     * @param string idGroupe
     * @param string idRole
     * @return array 
     */
    public function addRoleToGroupe($idGroupe,$idRole){
      $this->table="gestion_role";
      $sql='INSERT INTO gestion_role (id_groupe,code_role) VALUES (?,?)';// on établit la requete sql
      $req=$this->connexion->prepare($sql);// on fait une requete preparée
      return $req->execute(array($idGroupe,$idRole));// on execute
    }

    /**
     * (M) Fonction qui récupère tous les roles d'un  groupe 
     * (O) array de résultats
     * (I) 1 array ssociatif contenant les valeurs champs
     * @param array data
     * @return array 
     */
    public function getAllRolesFromOneGroupe($data){
        $sql='SELECT gestion_role.code_role,nom_role FROM gestion_role
                INNER JOIN role ON role.code_role=gestion_role.code_role
                WHERE id_groupe = ?';

        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        return $req->fetchAll();
    }

      /**
     * (M) Fonction qui supprime un role à un  groupe 
     * (O) booleen
     * (I) 2 strings contenant les valeurs id
     * @param string idGroupe
     * @param string idRole
     * @return boolean
     */
    public function deleteRoleFromGroupe($idGroupe,$idRole){
        $sql="DELETE FROM gestion_role WHERE id_groupe =? AND code_role=?";
        $req=$this->connexion->prepare($sql);
        return $req->execute(array($idGroupe,$idRole));
    }


      /**
     * (M) Fonction qui supprime un role 
     * (O) booleen
     * (I) 2 strings contenant les valeurs, champs
     * @param string champ
     * @param string valeur
     * @return boolean
     */
    public function deleteOneRole($champ,$valeur){
        $this->table='role';
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->table='gestion_role';
                $this->deleteBy($champ,$valeur);
                $this->table='role';
                $results=$this->deleteBy($champ,$valeur);
            $this->connexion->commit();
        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false;
        }
        return $results;
    }


    /**
     * (M)Fonction qui insère un role
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function insertOneRole($data=null){
        $this->table='role';
        if($data==null){// si data n'est pas mis
            $data=$this->data;// on récupère dans les this->data
        }
       
        return $this->insertOne($data);
        
    }

    /**
     * (M) Fonction insere un element
     * (O) array 
     * (I) data de données 
     * @param array data 
     * @return  array resultats
     */
    public function insertOneElement($data=null){
        $this->table="role";
        if($data==null){
            $data=[
                'code_role' => $_POST['code_role'.$_POST['validating_edit']], 
                'nom_role' => $_POST['nom_role'.$_POST['validating_edit']]
            ];
        }
        return $this->insertOne($data);
        
    }

    /**
     * (M) Fonction update un role
     * (O) array de reulstat 
     * (I) data de données 
     * @param array data 
     * @return  array resultats
     */
    public function updateOneRole($id,$valeur_id,$data){
        $this->table="role";
        //la table formation contient des clés primaires qui sont des foreign key pour d'autres tables. L'update ne peut se faire directement et doit passer par des sous étapes
        //si la clé primaire est utilisée en foreign key dans une occurence
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->table='role';
                $this->insertOneElement([$id=>'AUCUN','nom_role'=>'AUCUN']);// On insère la modfication comme nouveau role
                $this->table='gestion_role';
                $this->updateBy($id,$valeur_id,[$id=>'AUCUN']);//on update les foreign key des autres tables pour correspondre au nouveau role aucun
                $this->table='role';
                $this->updateBy($id,$valeur_id,$data);//on update notre role à ses nouvelles valeurs
                $this->table='gestion_role';
                $this->updateBy($id,'AUCUN',[$id=>$data[$id]]);//on update les foreign key des autres tables pour correspondre aux nouvelles valeurs
                $this->table='role';
                $this->deleteBy($id,'AUCUN');// on delete le role AUCUN
            $this->connexion->commit();
        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false;
        }
        return $results; 
    }


    public function getLastId(){
        $sql="SELECT MAX(code_role) FROM role";
        $query=$this->connexion->prepare($sql);
        $query->execute();
        $results=$query->fetch();
        $results=$results[0]+1;
        return $results;
    }
}

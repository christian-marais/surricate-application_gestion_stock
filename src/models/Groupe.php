<?php
 namespace Surricate;
 use \Exception;
 use \PDO;

class Groupe extends Model{



      /**
     * (M) Méthode qui définie la table utilisée par défut dans les requetes sql
     * (O) rien
     * (I) rien
    */
    public function __construct(){
        $this->table='groupe';// on met la table à manipuler par défaut
        $this->getConnection();// on initialise la connexion
        $this->datas=[//variable contenant les nom des champs
            'id_groupe','libelle_groupe','id_formation','id_centre'
        ];
        $this->setData();// a chaque instanciation on s'assure les post soient correctement initialisés
        $this->getMetaData();//on obtient le nom retravaillé des champs
        $this->metadatas=[//variable contenant les nom des champs retravaillé
        'ID','Libelle','Formation','Centre'
        ];
    }  

      /**
     * (M) Méthode qui compte le nombre de groupe
     * (O) int du nombre de groupe
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int 
     */
    public function countGroupes($search=null){
        $this->table='groupe';
        ($search!=null)?$sql=" libelle_groupe LIKE CONCAT('%',?,'%') OR id_groupe LIKE CONCAT('%',?,'%')":$sql=null;
        $countSql=null;
        if(!empty($_SESSION["id"])){
            $centrGroup=$this->getUserCentre($_SESSION["id"]);
            if(!empty($centrGroup) && strtoupper($centrGroup['id_groupe'])!=='ADMIN'){
                $countSql=' WHERE id_centre LIKE "'.$centrGroup["id_centre"].'"';
            }
        }
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search,$countSql);
    }

    public function getMetaDatas(){
        return $this->metadatas;
    }
    /**
     * (M) Méthode recupère l'ensemble des groupes
     * (O) rien
     * (I) 1 string personnalisation de la requete avec une barre de recherche, les limites
     * @param string search mots clés de recherches
     */
    public function getAllGroupes($search=null,$page=null){
        $this->table='groupe';
        $this->sqlViewByCenter= ' WHERE id_centre LIKE :currentCentre ';
        $this->setViewByCentre();
        $this->setSearchAndOffset('id_groupe','libelle_groupe',$search,$page,$sql,$data,$this->sqlCondSyntaxe);
        return $this->getAll($sql,$data);
    }


    /**
     * (M) Méthode qui recupère un groupe
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function getOneGroupe($champ,$valeur){
        $this->table='groupe';
        return $this->getBy($champ,$valeur);
    }
   


      /**
     * (M) Méthode qui supprime un groupe
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     * @return boolean
     */
    public function deleteOneGroupe($champ,$valeur){
        $this->table='groupe';
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->table='user';
                $this->updateBy('id_groupe',$valeur,['id_groupe'=>null]);// on update l'id_groupe de user à null; la transaction peutse faire ainsi car user accepte groupe en null
                $this->table='gestion_role';
                $this->deleteBy('id_groupe',$valeur);// on delete l'occurence dans gestion de role
                $this->table='groupe';
                $this->deleteBy($champ,$valeur);// on supprime l'occurence de groupe
            $this->connexion->commit();// on fait un commit
        }catch(Exception $e){
            $results=false;
            $this->connexion->rollback();// on rollback en cas d'erreur
        }
        return $results;// on retourne  un booleen
    }


       /**
     * (M) Méthode qui insère un groupe
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function insertOneGroupe($data=null){
        $this->table = 'groupe';
        $this->setData();
        
        if($data==null){
            $data=$this->data;
        }
        return $this->insertOne($data);
    }
    
      /**
     * (M) Méthode qui recupère un groupe sans besoin de parametre si le champ id a pour nom juste id
     * (O) array 
     * (I) rien
     * @param array resultats
     */
    public function getGroupe(){
        return $this->getOne();
    }
    
    public function updateOneGroupe($id,$valeur_id,$data){
        $this->setData();
        //la table groupe contient des clés primaires qui sont des foreign key pour d'autres tables. L'update ne peut se faire directement et doit passer par des sous étapes
        //si la clé primaire est utilisée en foreign key dans une occurence
        $results=true;
        $datas=$data;
        $datas['id_groupe']='AUCUN';
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->insertOneGroupe($datas);// On insère la modfication comme nouvelle groupe
                $this->table='user';
                $this->updateBy('id_groupe',$valeur_id,['id_groupe'=>$datas['id_groupe']]);
                $this->table='gestion_role';
                $this->updateBy('id_groupe',$valeur_id,['id_groupe'=>$datas['id_groupe']]);///on update les foreign key des autres tables pour correspondre à la nouvelle groupe
                
                $this->table='groupe';
                $this->updateBy('id_groupe',$valeur_id,$data);
                $this->table='user';
                $this->updateBy('id_groupe',$datas['id_groupe'],['id_groupe'=>$_POST['id_groupe'.$_POST['validating_edit']]]);
                $this->table='gestion_role';
                $this->updateBy('id_groupe',$datas['id_groupe'],['id_groupe'=>$_POST['id_groupe'.$_POST['validating_edit']]]);///on update les foreign key des autres tables pour correspondre à la nouvelle groupe
                $this->table='groupe';
                $this->deleteBy($id,$datas['id_groupe']);//on supprime l'ancienne occurrence de groupe
            $this->connexion->commit();
        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false;
        }
        
        return $results;   
    }
    /* Version originale d'update decommenter et commenter l'autre méthode pour revenir changer son comportement

    public function updateOneGroupe($id,$valeur_id,$data){
        $this->setData();
        //la table groupe contient des clés primaires qui sont des foreign key pour d'autres tables. L'update ne peut se faire directement et doit passer par des sous étapes
        //si la clé primaire est utilisée en foreign key dans une occurence
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->insertOneGroupe($data);// On insère la modfication comme nouvelle groupe
                $this->table='user';
                $this->updateBy('id_groupe',$valeur_id,['id_groupe'=>$_POST['id_groupe'.$_POST['validating_edit']]]);
                $this->table='gestion_role';
                $this->updateBy('id_groupe',$valeur_id,['id_groupe'=>$_POST['id_groupe'.$_POST['validating_edit']]]);///on update les foreign key des autres tables pour correspondre à la nouvelle groupe
                $this->table='groupe';
            $this->deleteBy($id,$valeur_id);//on supprime l'ancienne occurrence de groupe
            $this->connexion->commit();
        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false;
        }
        
        return $results;   
    }
    */
    /**
     * Fonction qui retourne un array des centres de formations
     * @return array centres de formations
     */
    public function getAllCentres(){
        $this->table='centre';
        return $this->getAll();
    }

    
  
}

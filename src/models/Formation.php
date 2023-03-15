<?php
 namespace Surricate;
 use \PDO;
 use \Exception;

class Formation extends Model{

    

    public function __construct(){
        $this->table='formation';
        $this->getConnection();
        $this->datas=[
            'id_formation','libelle_formation','id_domaine'
        ];
    }   

     /**
     * (M) Fonction qui compte le nombre de formations
     * (O) int du nombre de formation
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int du nombre de formations selon la requete
     */
    public function countFormations($search=null){
        $this->table='formation';
        $countSql=' AS t1 INNER JOIN domaine as t2 ON t2.id_domaine = t1.id_domaine ';
        if(!empty($_SESSION["id"])){
            $centrGroup=$this->getUserCentre($_SESSION["id"]);
            if(!empty($centrGroup) && strtoupper($centrGroup['id_groupe'])!=='ADMIN'){
                $countSql.=' WHERE t2.id_centre LIKE "'.$centrGroup["id_centre"].'"';
            }
        }
        ($search!=null)?$sql= " t1.id_formation LIKE CONCAT('%',?,'%') OR t1.libelle_formation LIKE CONCAT('%',?,'%')":$sql=null;
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search,$countSql);
    }
    
    public function countCentres($search=null){
        $this->table='centre';
        ($search!=null)?$sql=" id_centre LIKE CONCAT('%',?,'%') OR nom_centre LIKE CONCAT('%',?,'%')":$sql=null;
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search);
    }

    public function countCursus($search=null){
        $this->table='cursus';
        $countSql=null;
        if(!empty($_SESSION["id"])){
            $centrGroup=$this->getUserCentre($_SESSION["id"]);
            if(!empty($centrGroup) && strtoupper($centrGroup['id_groupe'])!=='ADMIN'){
                $countSql=' WHERE id_centre LIKE "'.$centrGroup["id_centre"].'"';
            }
        }
        ($search!=null)?$sql=' id_formation LIKE CONCAT("%",?,"%") OR annee LIKE CONCAT("%",?,"%")':$sql="";
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search,$countSql);
    }

    /**
     * (M) Fonction qui compte les infos de toues les  formations
     * (O) rien
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int du nombre de formations selon la requete
     */
    public function getAllFormations($search=null,$page=null,$sql=null,$data=null){
        $this->table='formation';
        $this->sqlViewByCenter= ' AS t1 INNER JOIN domaine as t2 ON t2.id_domaine = t1.id_domaine
        WHERE t2.id_centre LIKE :currentCentre ';
        $this->setViewByCentre();
        $this->setSearchAndOffset('id_formation','libelle_formation',$search,$page,$sql,$data,$this->sqlCondSyntaxe);
        return $this->getAll($sql,$data);
    }

      /**
     * (M) Fonction qui récupère les infos d'une formation
     * (O) array 
     * (I) 2 string 
     * @param string champ
     * @param string valeur du champs
     * @return array de resultats de la requete
     */
    public function getOneFormation($champ,$valeur){
        $this->table="formation";
        return $this->getBy($champ,$valeur);
    }

    
    /**
     * (M) Fonction qui supprimer une formation
     * (O) array 
     * (I) 2 string 
     * @param string champ
     * @param string valeur du champs
     * @return boolean
     */

    public function deleteOneFormation($champ,$valeur){
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, ERROR_MODE);
            $this->connexion->beginTransaction();// on commence une transaction
            $this->table='groupe';
            $this->updateBy('id_formation',$valeur,['id_formation'=>null]);// on supprime dans la table groupe l'id formation
            $this->table='formation';
            $this->deleteBy($champ,$valeur);// ensuite dans la table formation
            $this->connexion->commit(); // on fait un commit
        }catch(Exception $e){// en cas d'echec
            $results=false;
            $this->connexion->rollback();// on fait un rollback
        }
            return $results; // retourn un booleen true si succès
    }

      /**
     * (M) Méthode qui va inserer une formation 
     * (O) un bool
     * (I) 1 array de data a mettre à inserer
     * @param array data
     * @return boolean
     */
    public function insertOneFormation($data=null){
        $this->table="formation";
        if($data==null){
            $data=[
                'id_formation' => $_POST['id_formation'], 
                'libelle_formation' => $_POST['libelle_formation'],
                'id_domaine'=> $_POST['id_domaine']
            ];// on récupère les données
        }
        return $this->insertOne($data);// on insere les données dans la requete
        
    }
    
     /**
     * (M) Méthode qui va récupérer une formation
     * (O) un array de résultat
     * (I) rien
     * @return array resultats
     */
    public function getFormation(){
        $this->table="formation";
        return $this->getOne();
    }
    

     /**
     * (M) Méthode qui va récupérer un domaine de  formation
     * (O) un array de résultat
     * (I) rien
     * @return array resultats
     */
    public function getDomaine(){
        $this->table='domaine';
        $this->id=$_POST['select_code_domaine'];
        return $this->getOne();
    }

     /**
     * (M) Fonction qui va mettre à jour une formation 
     * (O) un array de résultat
     * (I) 3 params
     * @param string id
     * @param string valeur id
     * @param array data ensemble de données à mettre à jour (sous forme clé valeur)
     * @return array resultats
     */
    public function updateOneFormation($id,$valeur_id,$data){
        $this->table="formation";// on sélectionne la table à manipuler
        
        $results=@$this->updateBy($id,$valeur_id,$data);//si la clé primaire est utilisée en foreign key dans une occurence
        if(!$results){// on ne peut pas update
            try{//la table formation contient des clés primaires qui sont des foreign key pour d'autres tables. L'update ne peut se faire directement et doit passer par des sous étapes
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connexion->beginTransaction();// on démarre une transaction
                    $this->insertOneFormation($data);// On insère la modfication comme nouvelle formation
                    $this->table='groupe';
                    $this->updateBy('id_formation',$valeur_id,['id_formation'=>$_POST['id_formation'.$_POST['validating_edit']]]);//on update les foreign key des autres tables pour correspondre à la nouvelle formation
                    $this->table='formation';
                    $this->deleteBy($id,$valeur_id);//on supprime l'ancienne occurrence de formation
                $this->connexion->commit();// on fait un commit

            }catch(Exception $e){// en cas d'echecs 
                $this->connexion->rollback();//on fait un rollback
                $results=false;// on retourne un false
            }
        }
        return $results;
    }

    /**
     * (M) Fonction qui va recupérer tous les domaines de formation
     * (O) un array de résultat
     * (I) rien
     * @return array resultats
     */
    public function getAllDomaines(){
        $this->table='domaine';
        return $this->getAll();
    }

    /**
     * (M) Fonction qui va inserer un domaine de formation 
     * (O) un array de résultat
     * (I) array associatif facultatif de propriétés à mettre à jour 
     * @param array data
     * @return array resultats
     */
    public function insertOneDomaine($data=null){//on insere un domaine de formation
        $this->table='domaine';
        if($data==null){
            $datas=[
                'id_domaine' => $_POST['id_domaine_create'], 
                'libelle_domaine' => $_POST['libelle_domaine_create'],
                'id_centre'=> $_POST['select_code_centre']
            ];
        }
        ($data==null)?$data=$datas:'';
        return $this->insertOne($data);
    }
    
    
    /**
     * (M) Fonction qui va mettre à jour un domaine de formation 
     * (O) un array de résultat
     * (I) 3 params
     * @param string id
     * @param string valeur id
     * @param array data ensemble de données à mettre à jour (sous forme clé valeur)
     * @return array resultats
     */
    public function updateOneDomaine($id,$valeur_id,$data){
        $this->table='domaine';
        $results=$this->updateBy($id,$valeur_id,$data);//la table formation contient des clés primaires qui sont des foreign key pour d'autres tables.
        
        if(!$results){// on ne peut pas update
            try{//la L'update ne peut se faire directement et doit passer par des sous étapes
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connexion->beginTransaction();
                    $this->insertOneDomaine($data);// On insère la modfication comme nouvelle formation
                    $this->table='formation';
                    $this->updateBy('id_domaine',$valeur_id,['id_domaine'=>$_POST['id_domaine_for_edition']]);//on update les foreign key des autres tables pour correspondre à la nouvelle formation
                    $this->table='domaine';
                    $this->deleteBy($id,$valeur_id);//on supprime l'ancienne occurrence de formation
                $results=$this->connexion->commit();
            }catch(Exception $e){
                $this->connexion->rollback();//on annule la transaction et revient à l'état d'origine
                $results=false;
            }
        }
        return $results;
    }


        /**
     * (M) Fonction qui va supprimer un domaine de formation 
     * (O) un booleen 
     * (I) 2 params
     * @param string champ
     * @param string valeur
     * @return boolean
     */
    public function deleteOneDomaine($champ='id_domaine',$valeur=null){//on supprime un domaine de formation
        $this->table='domaine';
        return $this->deleteBy($champ,$valeur);
    }

    /**
     * (M) Fonction qui compte les infos de toues les  centres
     * (O) rien
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int du nombre de centres selon la requete
     */
    public function getAllCentres($search=null,$page=null,$sql=null,$data=null){
        $this->table='centre';
        $this->sqlViewByCenter= ' AS t1 WHERE t1.id_centre LIKE :currentCentre ';
        $this->setViewByCentre();
        $this->setSearchAndOffset('id_centre','nom_centre',$search,$page,$sql,$data,$this->sqlCondSyntaxe);
        return $this->getAll($sql,$data);
    }

    /**
     * (M) Fonction qui compte les infos de tous les cursus
     * (O) rien
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int du nombre de centres selon la requete
     */
    public function getAllCursus($search=null,$page=null,$sql=null,$data=null){
        $this->table='cursus';
        $this->sqlViewByCenter= ' WHERE id_centre LIKE :currentCentre ';
        $this->setViewByCentre();
        $this->setSearchAndOffset('id_cursus','id_formation',$search,$page,$sql,$data,$this->sqlCondSyntaxe);
        return $this->getAll($sql,$data);
    }



   
       /**
     * (M) Méthode qui va inserer un centre
     * (O) un bool
     * (I) 1 array de data a mettre à inserer
     * @param array data
     * @return boolean
     */
    public function insertOneCentre($data=null){
        $this->table="centre";
        if($data==null){
            $data=[
                'id_centre' => $_POST['id_centre'], 
                'nom_centre' => $_POST['nom_centre']
            ];// on récupère les données
        }
        return $this->insertOne($data);// on insere les données dans la requete
        
    }

          /**
     * (M) Méthode qui va inserer un cursus
     * (O) un bool
     * (I) 1 array de data a mettre à inserer
     * @param array data
     * @return boolean
     */
    public function insertOneCursus($data=null){
        $this->table="cursus";
        
        if($data==null){
            $data=[
                'id_cursus' => $_POST['id_cursus'],
                'id_centre' => $_POST['id_centre'], 
                'id_formation' => $_POST['id_formation'],
                'annee' => $_POST['annee']
            ];// on récupère les données
        }
      
        return $this->insertOne($data);// on insere les données dans la requete
        
    }
      /**
     * (M) Fonction qui update un centre
     * (O) booleen
     * (I) array data de forme paire clef nom champ => valeur valeur du champ
     * @param array data
     * @return boolean 
     */
    public function updateOneCentre($id,$valeurId,$data){
      
        $results=true;
        $emptyData=[$id=>'AUCUN','nom_centre'=>'AUCUN'];
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->table='centre';
                $this->insertOneCentre($emptyData);// on insère un enregistrement fictif 
                $tables=['bordereau_reception','commande','cursus','domaine','groupe','utilisation'];
               
                foreach($tables as $table){
                    $this->table = $table;
                    $this->updateBy($table.'.'.$id,$valeurId,[$table.'.id_centre' =>'AUCUN']);// dans la tables avec la foreign key on met à jour avec cet enregistrement fictif 
                }
                $this->table ='centre';
                $this->updateBy($id,$valeurId,$data);// on update les valeurs de notre centre avec les valeurs envoyées par l'utilisateur
                foreach($tables as $table){
                    $this->table = $table;
                    $this->updateBy($table.'.'.$id,'AUCUN',['id_centre'=>$data['id_centre']]);// on update la table groupe des valeurs envoyés par l'user
                }
                $this->table='centre';
                $this->deleteBy('id_centre','AUCUN');// on supprime de notre table centre l'enregistrement fictif
                
                $this->connexion->commit();
        }catch(Exception $e){// en cas d'erreur
            $this->connexion->rollback();//on rollback. Bien que Mysql l'exécute automatiquement, on préfère l'initier maintenant pour éviter un affichage fantôme le temps du rollback par mysql
            //($e->getMessage());
            $results=false;
        }
        return $results;
    }

         /**
     * (M) Fonction qui update un cursus
     * (O) booleen
     * (I) array data de forme paire clef nom champ => valeur valeur du champ
     * @param array data
     * @return boolean 
     */
    public function updateOneCursus($id,$valeurId,$data){
        $this->table='cursus';
        return $this->updateBy($id,$valeurId,$data);
    }

    public function getLastId(){
        $sql="SELECT MAX(id_cursus) FROM cursus";
        $query=$this->connexion->prepare($sql);
        $query->execute();
        $results=$query->fetch();
        $results=$results[0]+1;
        return $results;
    }
     /**
     * (M) Fonction qui supprime un centre
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function deleteOneCentre($champ,$valeur){
        $results=true;
        $this->table='centre';
        if(!$this->deleteBy($champ,$valeur)){
            try{
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met PDO en mode exception pour qu'il renvoie une erreur. Important au fonctionnement du rollback
                $this->connexion->beginTransaction();
                    $this->table='groupe';
                    $this->updateBy($champ,$valeur,[$champ =>NULL]);// on supprime de la table groupe le centre en mettant son id à null 
                    $this->table='centre';
                    $this->deleteBy($champ,$valeur);// on supprime le centre
                $this->connexion->commit();// on commit
            }catch(Exception $e){// en erreur
                $this->connexion->rollback();//on rollback. Bien que Mysql l'exécute automatiquement, on préfère l'initier maintenant pour éviter un affichage fantôme le temps du rollback par mysql
                $results=false;
            }
        }
        return $results;
    }

    /**
     * (M) Fonction qui supprime un cursus
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function deleteOneCursus($champ,$valeur){
        $results=true;
        $this->table='centre';
        if(!$this->deleteBy($champ,$valeur)){
            try{
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met PDO en mode exception pour qu'il renvoie une erreur. Important au fonctionnement du rollback
                $this->connexion->beginTransaction();
                    $this->table='user';
                    $this->updateBy($champ,$valeur,[$champ =>NULL]);// on supprime de la table groupe le centre en mettant son id à null 
                    $this->table='cursus';
                    $this->deleteBy($champ,$valeur);// on supprime le centre
                $this->connexion->commit();// on commit
            }catch(Exception $e){// en erreur
                $this->connexion->rollback();//on rollback. Bien que Mysql l'exécute automatiquement, on préfère l'initier maintenant pour éviter un affichage fantôme le temps du rollback par mysql
                $results=false;
            }
        }
        return $results;
    }

     /**
     * (M) Fonction qui récupère les infos d'une formation
     * (O) array 
     * (I) 2 string 
     * @param string champ
     * @param string valeur du champs
     * @return array de resultats de la requete
     */
    public function getOneCentre($champ,$valeur){
        $this->table="centre";
        return $this->getBy($champ,$valeur);
    }
    
    
     /**
     * (M) Fonction qui récupère les infos d'un cursus de formation
     * (O) array 
     * (I) 2 string 
     * @param string champ
     * @param string valeur du champs
     * @return array de resultats de la requete
     */
    public function getOneCursus($champ,$valeur){
        $this->table="cursus";
        return $this->getBy($champ,$valeur);
    }


}

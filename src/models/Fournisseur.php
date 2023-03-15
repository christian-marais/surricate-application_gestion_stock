<?php
 namespace Surricate;
 use \PDO;
 use \Exception;

class Fournisseur extends Model{
  
    public function __construct(){
        $this->table='fournisseur';
        $this->getConnection();// on initialise la connexion 
        /** propriété contenant le nom des champs à utiliser*/
        $this->datas=[// indiquer ici les propriétés de fournisseur à insérer,afficher ou mettre à jour
            'id_fournisseur',
            'nom_fournisseur',
            'adresse_fournisseur',
            'cp_fournisseur',
            'nom_contact',
            'prenom_contact',
            'fonction',
            'email',
            'tel',
            'active'
        ];

    }   


   /**
     * (M) Fonction qui compte le nombre de fournisseurs
     * (O) int du nombre de fournisseurs
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int du nombre de fournisseurs selon la requete
     */
    public function countFournisseurs($search=null){
        $this->table='fournisseur';
        ($search!=null)?$sql=" nom_fournisseur LIKE CONCAT('%',?,'%') OR id_fournisseur LIKE CONCAT('%',?,'%')":$sql=null;
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search);
    }

     /**
     * (M) Fonction qui compte les infos de toues les  fournisseurs
     * (O) rien
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int du nombre de fournisseurs selon la requete
     */
    public function getAllFournisseurs($search=null,$page=null){
        $this->table='fournisseur';
        $this->setSearchAndOffset('id_fournisseur','nom_fournisseur',$search,$page,$sql,$data);
        return $this->getAll($sql,$data);
    }


       /**
     * (M) Fonction qui récupère les infos d'un fournisseur
     * (O) array 
     * (I) 2 string 
     * @param string champ
     * @param string valeur du champs
     * @return array de resultats de la requete
     */
    public function getOneFournisseur($champ,$valeur){
        $this->table='fournisseur';
        return $this->getBy($champ,$valeur);
    }

     
    /**
     * (M) Fonction qui supprimer un fournisseur
     * (O) array 
     * (I) 2 string 
     * @param string champ
     * @param string valeur du champs
     * @return boolean
     */
    public function deleteOneFournisseur($champ,$valeur){
        $this->table='fournisseur';
        return $this->deleteBy('id_fournisseur',$valeur);
    }

     /**
     * (M) Méthode qui va inserer un fournisseur
     * (O) un bool
     * (I) 1 array de data a mettre à inserer
     * @param array data
     * @return boolean
     */
    public function insertOneFournisseur($data=null){
        $this->table='fournisseur';
        $this->setData();
       
        if($data==null){
            $data=$this->data;
        }
        try{
            $results=(!empty($data['email']))?$this->insertOne($data):'';
        }catch(Exception $e){
            $results=false;
        }
        return $results;
        
    }
    
    /**
     * (M) Méthode qui va récupérer un fournisseur
     * (O) un array de résultat
     * (I) rien
     * @return array resultats
     */
    public function getFournisseur(){
        $this->table='fournisseur';
        return $this->getOne();
    }
    

     /**
     * (M) Fonction qui va mettre à jour un fournisseur
     * (O) un array de résultat
     * (I) 3 params
     * @param string id
     * @param string valeur id
     * @param array data ensemble de données à mettre à jour (sous forme clé valeur)
     * @return array resultats
     */
    public function updateOneFournisseur($id,$valeur_id,$data=null){
        $this->setData();
        ($data==null)?$data=$this->data:'';
        (empty($data['active']))?$data['active']=0:'';//si nous n'avons données envoyées par la checkbox(donc décohée) on définit sa valeur de active à 0 pour la mise à jour
        //la table fournisseur contient des clés primaires qui sont des foreign key pour d'autres tables. L'update ne peut se faire directement et doit passer par des sous étapes
        $results=true;
        $datas=$data;
        $datas['id_fournisseur']='AUCUN';
            try{
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $this->connexion->beginTransaction();// on démarre une transaction
                $this->insertOneFournisseur($datas);// On essaie un update par nouvel insert qui ne marche pas quand on ne modifie pas l'identifiant. On insère la modfication comme nouvelle fournisseur
                $tables=['commande','article'];// on selectionne les tables liées
                foreach($tables as $table){// on les mets à jour avec les nouvelles valeurs
                    $this->table=$table;
                    $this->updateBy('id_fournisseur',$valeur_id,['id_fournisseur'=>$datas['id_fournisseur']]);
                }
                $this->table='fournisseur';
                $this->updateBy('id_fournisseur',$valeur_id,$data);
                $tables=['commande','article'];// on selectionne les tables liées
                foreach($tables as $table){// on les mets à jour avec les nouvelles valeurs
                    $this->table=$table;
                    $this->updateBy('id_fournisseur',$datas['id_fournisseur'],['id_fournisseur'=>$_POST['id_fournisseur']]);
                }
                $this->table='fournisseur';
                $this->deleteBy($id,$datas['id_fournisseur']);//on supprime l'ancienne occurrence de fournisseur
                $this->connexion->commit();// on fait un commit
            }catch(Exception $e){
                $this->connexion->rollback();// en echec on rollback
                $results=false;
            }
            
        return $results;
    }

}

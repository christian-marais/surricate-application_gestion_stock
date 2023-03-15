<?php
 namespace Surricate;
 use \PDO;
 use \Exception;

class Categorie extends Model{
 
    public function __construct(){
        $this->table='famille_article';// on dédinit par défaut la table par défaut comme famille_article pour les requetes
        $this->getConnection();// on établit la connexion
        (empty($_POST['id_famille_article']))? $_POST['id_famille_article']="" : "";//si les posts ne sont pas déclaré à chaque instance de classe on les mets à vide
        (empty($_POST['libelle_famille']))? $_POST['libelle_famille']="" : "";//idem
        $this->datas=[// nom des champs de tables à compléter en cas de rajout sert à initialiser des posts, lors de l'intégration de données dans les view...
            'id_famille_article',
            'libelle_famille'   
          ];
      
    }   

     /**
     * (M) Fonction qui retourne toutes les catégories d'articles.
     * (O) 1 array 
     * (I) rien
     * @return array de catégorie d'articles
    */
    public function getAllCategories(){
        return $this->getAll();
    }

       /**
     * (M) Fonction qui retourne une catégorie d'article
     * (O) 1 array
     * (I) 2 string
     * @param string champ 
     * @param string valeur du champ
     * @return array des resultats pdo
    */
    public function getOneCategorie($champ,$valeur){
        return $this->getBy($champ,$valeur);
    }


        /**
     * (M) Fonction qui supprime une catégorie d'article
     * (O) array de resultat
     * (I) 2 string
     * @param string champ
     * @param string valeur 
     * @return  error si echec
    */
    public function deleteOneCategorie($champ,$valeur){
        
        $this->table='article';
        $this->deleteBy('id_famille_article',$valeur);
        $this->table='famille_article';
        return $this->deleteBy('id_famille_article',$valeur);
    }

    /**
     * (M) Fonction qui insère une catégorie d'article
     * (O) array de resultat
     * (I)array des propriétés à insérer sous forme clef =>valeur
     * @param array des propriétés à inserer
     * @return  error si echec
    */
    public function insertOneCategorie($data=null){
        $this->data=[// array contenant les données à envoyer à pdo
            'id_famille_article' =>$_POST['id_categorie_create'],
            'libelle_famille' =>$_POST['libelle_categorie_create']
        ];
        if($data==null){// si pas de données en parametre
            $data=$this->data;// on récupère les données générés automatiquement par setData dans this->data
        }
       
        return $this->insertOne($data);// on insère
        
    }

    /**
     * (M) Fonction qui recupère les infos d'une catégorie d'article
     * (O) array de resultat
     * (I) rien
     * @return  array de resultat
    */
    public function getCategorie(){
     
        $this->id=$_POST['select_code_categorie'];
        return $this->getOne();
    }

    /**
     * (M) Méthode qui met à jour  une catégorie d'article
     * (O) boolean 
     * (I) 2 string
     * @param string champ
     * @param string valeur 
     * @param array propriétés à met à jour sous forme clés => valeurs
     * @return  error si echec
    */
    public function updateOneCategorie($id,$valeur_id,$data){
        $this->table='famille_article';
        $this->setData();
        //la table catégorie contient des clés primaires qui sont des foreign key pour d'autres tables. L'update ne peut se faire directement et doit passer par des sous étapes
        //si la clé primaire est utilisée en foreign key dans une occurence
        $results=$this->updateBy($id,$valeur_id,$data);// On met à jour
        if(!$results){// Si on ne peut pas update
            try{
                $this->connexion->setAttribute(PDO::ATTR_ERRMODE, ERROR_MODE);// pour l affichage des erreurs
                $this->connexion->beginTransaction();// on commence une transaction
                    $this->insertOneCategorie($data);// On insère la modfication comme nouvelle catégorie
                    $this->table='article';
                    $this->updateBy('id_famille_article',$valeur_id,['id_famillearticle'=>$_POST['id_categorie_for_edition']]);//on update les foreign key des autres tables pour correspondre à la nouvelle catégorie
                    $this->table='famille_article';
                    $this->deleteBy($id,$valeur_id);//on supprime l'ancienne occurrence de catégorie
                $this->connexion->commit();// on fait un commit
            }catch(Exception $e){// si echec 
                $this->connexion->rollback();// on rollback
                $results=false; // Et retourne un booleen false
            } 
        } 
        return $results; // retourne un booleen
    }

}

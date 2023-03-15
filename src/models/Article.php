<?php
namespace Surricate;
use \PDO;
use \Exception;

class Article extends Model{

     /**
     * (M) Méthode qui définie la table utilisée par défut dans les requetes sql
     * (O) rien
     * (I) rien
    */
    public function __construct(){
        $this->table='article';
        $this->getConnection();
        $this->datas=[
            'reference_article',
            'lien_image',
            'description_article',
            'unite',
            'pu',
            'stock_de_securite',
            'id_famille_article',
            'id_fournisseur',
            'active'   
        ]; //variables contenant les nom des champs
        $this->metadatas=[ //variables contenant les nom des champs retravaillé pour l'affichage
            'Reference',
            'lien_image',
            'description_article',
            'unite',
            'pu',
            'stock_de_securite',
            'id_famille_article',
            'id_fournisseur',
            'active'   
        ];
    
    }
    /**
     * (M) Méthode recupère un article
     * (O) rien
     * (I) rien
    */
    public function getArticle(){
        $this->table='article';
        return $this->getOne();
    }
    /**
     * (M) Méthode qui compte le nombre d'article
     * (O) int du nombre d'article
     * (I) 1string personnalisation de la requete avec une barre de recherche
     * @param string search mots clés de recherches
     * @return int 
     */
    public function countArticles($search=null){
        $this->table='article';
        ($search!=null)?$sql=" reference_article LIKE CONCAT('%',?,'%') OR description_article LIKE CONCAT('%',?,'%')":$sql=null;
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search);
    }

    /**
     * (M) Méthode recupère l'ensemble des articles
     * (O) rien
     * (I) 1 string personnalisation de la requete avec une barre de recherche, les limites
     * @param string search mots clés de recherches
     */
    public function getAllArticles($search=null,$page=null){
        $this->table='article';// on définit la table à utiliser 
        $this->setSearchAndOffset('description_article','reference_article',$search,$page,$sql,$data);// on personnalise la requete avec les limites et le search
        return $this->getAll($sql,$data);// on envoie la requete personnalisé à la mthode getAll de model
    }

     /**
     * (M) Méthode qui recupère un article
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function getOneArticle($champ,$valeur){
        $this->table='article';
        return $this->getBy($champ,$valeur);
    }


     /**
     * (M) Méthode qui recupère tous les articles
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function getAllArticlesBy($champ,$valeur){
        $this->table='article';
        return $this->getAllBy($champ,$valeur);

    }

       /**
     * (M) Méthode qui insère un article
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function insertOneArticle($file=null){
        $this->table='article';
        $this->setData();
        ($file!=null)?$this->data['lien_image']=$file['filename'].$file['extendedName'].'.'.$file['type']:'';
        return $this->insertOne($this->data);
    }


    /**
     * (M) Méthode qui update un article
     * (O) rien
     * (I) 3 champs
     * @param string champ ou nom de colonne de l'id
     * @param string valeur du champs
     * @param array file facultatif contenant l'ensemble des propiétés du fichier image uploadé.
     */
    public function updateOneArticle($id,$valeur_id,$file=null){
        $this->table='article';// on choisit a table article
   
        $this->setData();// on configure les données à utiliser. Post non déclarés sont mis à vide et int on leur met une valeur 0 
        
        ($file!=null)?$this->data['lien_image']=$file['filename'].$file['extendedName'].'.'.$file['type']:'';// on définit l'adresse de l'image associé à l'article
        if($_POST['pu']>=0){// Sécurité sur le prix si le prix n'est pas correcte on refuse la mise à jour
            (empty($this->data['active']))?$this->data['active']=0:'';
            $results= $this->updateBy($id,$valeur_id,$this->data);// on met à jour les données

            if(!$results){// Si on ne peut pas update
                try{
                    $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
                    $this->connexion->beginTransaction();//     on commence une transaction
                        $this->insertOneArticle($this->data);// On insère la modfication comme nouvel article
                        $tables=['approvisionnement','details_commande','details_reception','details_utilisation']; // on définit les tables à modifier avec les nouvelles valeurs
                        $lastId=$this->getLastId();// on récupère l'id de la nouvelle ligne insérée

                        foreach($tables as $table){// pour chaque table à mettre à jour
                            $this->table=$table;
                            $this->updateBy('id_article',$_POST['validating_edit'],['id_article'=>$lastId]);// on met à jour les données
                        }
                        $this->table='article';// on revient dans article
                        $this->deleteBy($id,$valeur_id);//on supprime l'ancienne occurrence d'article 
                    $this->connexion->commit();// on fait un commit
                }catch(Exception $e){// si la transaction echoue
                    $this->connexion->rollback();// on fait un rollback
                    $results=false;// on retourne un false pour echec
                }
            }
               
        }else{// si le prix est inférieur à 0
            $results=false;// on notifie un echec pour l'opération
        }
        return $results;//on retourne false ou true 
    }
    

       /**
     * (M) Méthode qui supprime un article
     * (O) rien
     * (I) 2 champs
     * @param string champ ou nom de colonne
     * @param string valeur du champs
     */
    public function deleteOneArticle($champ,$valeur){
        $this->table='article';
        return $this->deleteBy($champ,$valeur);// on appelle à la méthode delete qui supprime si les contraites sont respectés
    }


       /**
     * (M) Méthode qui recupère le dernier id insérer par un max dans la table de l'insert (fonctionne qu'avec les id autoincrémenté)
     * (O) rien
     * (I) rien
     * @return int de l'id
     */
    public function getLastId(){
        $sql="SELECT MAX(id_article) FROM article";
        $query=$this->connexion->prepare($sql);
        $query->execute();
        $results=$query->fetch();
        $results=$results[0]+1;
        return $results;
    }

}

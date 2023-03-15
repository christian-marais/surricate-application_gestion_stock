<?php
 namespace Surricate;
 use \PDO;
 use \Exception;

class Stocks extends Model{

    public function __construct(){
        $this->getConnection();//initialise la connexion
        $this->getAllDatas();
        $this->setDataStocks();
    }   


    public function allArticleStockStatus($data){
        (!empty($data['id_groupe']))?$sql= ' AND user.id_groupe = ? ':$sql='';
        (!empty($data['id_user']))?$sql.= ' AND utilisation.id_user = ? ':'';
        (!empty($data['dateDeb']) && !empty($data['dateFin']))?$sql.= 'AND utilisation.date_utilisation BETWEEN  ? AND ? ':'';
        (!empty($data['dateDeb']) && empty($data['dateFin']))?$sql.= 'AND utilisation.date_utilisation > ? ':'';
        if(empty($data['dateDeb'])) unset($data['dateFin']);
        
        $sql='
        SELECT article.id_article,reference_article,description_article,unite,pu,t2.TotalReception as Livraison,
        IF((t3.TotalUtilisation-(if(t1.Sorties is null,0,t1.Sorties))) IS NULL,0,(t3.TotalUtilisation-(if(t1.Sorties is null,0,t1.Sorties)))) as EntréesDivers,
        if(t1.Sorties IS NULL,"0",t1.Sorties) as Sorties,
        stock_de_securite,
        (Select stock_by_article_by_centre(article.id_article,?)) as Marge_beneficiaire, 
        IF(((t2.TotalReception+t3.TotalUtilisation-2*(if(t1.Sorties is null,0,t1.Sorties)))- stock_de_securite)  IS NULL,0,((t2.TotalReception+t3.TotalUtilisation-2*(if(t1.Sorties is null,0,t1.Sorties)))-stock_de_securite)) as "Besoin de securité"
        FROM article 
        INNER JOIN (SELECT details_utilisation.id_article,SUM(details_utilisation.qte) as Sorties 
                FROM details_utilisation
                INNER JOIN utilisation ON utilisation.id_utilisation = details_utilisation.id_utilisation
                INNER JOIN user ON user.id_user=utilisation.id_user
                WHERE utilisation.code_utilisation IN ("produit_deteriore","regul_inv Sortie","sortie",	
        "transfert Sortie")
                '.$sql.'
                GROUP BY details_utilisation.id_article
                ) AS t1 ON t1.id_article=article.id_article
        INNER JOIN (SELECT details_reception.id_article, SUM(details_reception.qte) as TotalReception FROM details_reception  GROUP BY details_reception.id_article) AS t2 ON t2.id_article=article.id_article
        INNER JOIN (SELECT details_utilisation.id_article, SUM(details_utilisation.qte) as TotalUtilisation FROM details_utilisation  GROUP BY details_utilisation.id_article) AS t3 ON t3.id_article = article.id_article
        ;';
        //$data =implode(',',$data);
        //$data.=','.$data.','.$data;
        //var_dump(explode(',',$data));
        //$data=(is_array($data))?$data:array($data);
        $this->testSql($sql,$data);//on fait un log du sql
        $req=$this->connexion->prepare($sql);
        (!empty($data))?$req->execute(array_values($data)):$req->execute();//on transmet les valeurs si data n'est pas vide
        return $req->fetchAll();
    }

    
    public function getAllUtilisations($data){
        $this->table='utilisation';
        (!empty($data['id_groupe']))?$sql= ' AND user.id_groupe = ? ':$sql='';
        (!empty($data['id_user']))?$sql.= ' AND utilisation.id_user = ? ':'';
        if(!empty($data['dateDeb']) && !empty($data['dateFin'])){
            $sql.= 'AND utilisation.date_utilisation BETWEEN  ? AND ? ';
        }else{
            unset($data['dateDeb']);
            unset($data['dateFin']);
        }
        $sql= 'SELECT DISTINCT utilisation.id_utilisation, date_utilisation, type_utilisation.code_utilisation,t2.Qte as qte,t1.Montant, user.id_user,nom_user,prenom_user,groupe.id_groupe,groupe.libelle_groupe FROM utilisation
        INNER JOIN (SELECT id_utilisation, SUM(article.pu* details_utilisation.qte) as Montant
                        FROM details_utilisation,article
                        WHERE details_utilisation.id_article = article.id_article
                        GROUP BY id_utilisation) as t1 ON t1.id_utilisation= utilisation.id_utilisation
        INNER JOIN (SELECT id_utilisation, SUM(details_utilisation.qte) as Qte
                        FROM details_utilisation,article
                        WHERE details_utilisation.id_article = article.id_article
                        GROUP BY id_utilisation) as t2 ON t2.id_utilisation= utilisation.id_utilisation
        INNER JOIN user ON user.id_user = utilisation.id_user
        LEFT JOIN groupe ON groupe.id_groupe = user.id_groupe
        INNER JOIN details_utilisation ON details_utilisation.id_utilisation = utilisation.id_utilisation
        INNER JOIN type_utilisation ON type_utilisation.code_utilisation = utilisation.code_utilisation '.$sql;
        
        $this->testSql($sql,$data);//on fait un log du sql
        $req=$this->connexion->prepare($sql);
        (!empty($data))?$req->execute(array_values($data)):$req->execute();//on transmet les valeurs si data n'est pas vide
        return $req->fetchAll();
    }
      
    public function getTypeUtilisation(){
        $this->table ="type_utilisation";
        $this->getAll();
    }
    public function getAllDatas(...$tables){
        (empty($tables))? $tables=['bordereau_reception']:'';
        foreach($tables as $table){
            $this->table=$table;
            $this->datas[$table]=array_values($this->getAll());
            $data[$table]=$this->getAll();
        }
        
        return $this->data=$data;
    }

    public function countCommandes($search=null){
        $this->table='commande';
        ($search!=null)?$sql=" num_com LIKE CONCAT('%',?,'%') OR date_Com LIKE CONCAT('%',?,'%')":$sql=null;
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search);
    }
    public function countReceptions($search=null){
        $this->table='bordereau_reception';
        ($search!=null)?$sql=" date_reception LIKE CONCAT('%',?,'%') OR id_fournisseur LIKE CONCAT('%',?,'%')":$sql=null;
        ($search!=null)?$search=array_merge(array($search),array($search)):'';
        return $this->countAll($sql,$search);
    }
    public function getAllCommandes($search=null,$page=null){
        $this->table='commande';
        $sql='SELECT commande.num_com,commande.date_Com,commande.id_fournisseur,fournisseur.nom_fournisseur,user.id_user,user.nom_user,user.prenom_user,IF(t.MontantTTC IS NULL,0,t.MontantTTC) AS "Montant TTC",IF(t.QteCommande IS NULL,0,t.QteCommande) as "Qte Commandée",IF(t.Qtelivre IS NULL,0,t.Qtelivre) as "Qte livree",IF(t.statut IS NULL,"Commande vide",t.statut) as statut From commande LEFT JOIN
        (SELECT commande.num_com as numero,t1.Montant as MontantTTC,t3.QteCommande,IF(t2.Qtelivre IS NULL,0,t2.Qtelivre) as Qtelivre,IF(t3.QteCommande=t2.Qtelivre,"Terminée",IF(t3.QteCommande>(IF(t2.Qtelivre IS NULL,0,t2.Qtelivre)),"En cours","Trop perçu de marchandise")) as statut
                    FROM commande
                    LEFT JOIN (SELECT num_com, SUM(article.pu* details_commande.qte) as Montant
                                FROM details_commande,article
                                WHERE details_commande.id_article = article.id_article
                                GROUP BY num_com) as t1 ON t1.num_com = commande.num_com
                    LEFT JOIN (SELECT  bordereau_reception.num_com AS NUMT2, sum(details_reception.qte) as "Qtelivre" 
                               FROM bordereau_reception
                               LEFT JOIN  details_reception ON details_reception.id_reception = bordereau_reception.id_reception
                               WHERE bordereau_reception.num_com IS NOT NULL
                               GROUP BY  bordereau_reception.num_com
                              )as t2 ON t2.NUMT2 = commande.num_com
                    INNER JOIN (SELECT  commande.num_com as NUM, sum(details_commande.qte) as "QteCommande" 
                                FROM commande
                                INNER JOIN  details_commande ON details_commande.num_com = commande.num_com 
                                GROUP BY  commande.num_com) as t3 ON t3.NUM = commande.num_com) as t on t.numero = commande.num_com
                    INNER JOIN fournisseur on fournisseur.id_fournisseur = commande.id_fournisseur
                    INNER JOIN user on user.id_user = commande.id_user';
        $this->sqlViewByCenter= ' WHERE commande.id_centre LIKE :currentCentre ';
        $this->setViewByCentre();
        $sql.=$this->sqlViewByCenter;
        $this->setSearchAndOffset('commande.num_com','commande.date_Com',$search,$page,$addSql,$data,$this->sqlCondSyntaxe);
        (!empty($addSql) && !empty($data))?$sql.=$addSql:$data=null;
        $req=$this->connexion->prepare($sql);
        (!empty($this->param)&&!empty($this->sqlViewByCenter))?$req->bindParam(':currentCentre',$this->param,PDO::PARAM_STR):'';
        $this->bindParamSearchAndOffset($req,$addSql,$data);
        $req->execute();
       
        return $req->fetchAll();
    }
    
   
    public function getAllReceptions($search=null,$page=null){
        $this->table='bordereau_reception';
        $req=' LEFT JOIN (SELECT num_com, SUM(article.pu* details_commande.qte) as Montant
        FROM details_commande,article
        WHERE details_commande.id_article = article.id_article
        GROUP BY num_com) as t1
        ON bordereau_reception.num_com = t1.num_com
        INNER JOIN fournisseur ON fournisseur.id_fournisseur = bordereau_reception.id_fournisseur
        INNER JOIN user ON user.id_user = bordereau_reception.id_user ';
        //$this->sqlViewByCenter= ' WHERE id_centre LIKE :currentCentre ';
       // $this->setViewByCentre();
        $this->setSearchAndOffset(' bordereau_reception.date_reception',' bordereau_reception.id_fournisseur',$search,$page,$sql,$data);
     
        $sql=$req.$sql;
        return $this->getAll($sql,$data);
    }
    //original
    public function getAllUtilisationss(){
        $this->table='utilisation';
        $sql= 'SELECT DISTINCT utilisation.id_utilisation, date_utilisation, type_utilisation.code_utilisation, user.id_user,nom_user,prenom_user,t2.Qte as qte,t1.Montant FROM utilisation
        INNER JOIN (SELECT id_utilisation, SUM(article.pu* details_utilisation.qte) as Montant
                        FROM details_utilisation,article
                        WHERE details_utilisation.id_article = article.id_article
                        GROUP BY id_utilisation) as t1 ON t1.id_utilisation= utilisation.id_utilisation
        INNER JOIN (SELECT id_utilisation, SUM(details_utilisation.qte) as Qte
                        FROM details_utilisation,article
                        WHERE details_utilisation.id_article = article.id_article
                        GROUP BY id_utilisation) as t2 ON t2.id_utilisation= utilisation.id_utilisation
        INNER JOIN user ON user.id_user = utilisation.id_user
        INNER JOIN details_utilisation ON details_utilisation.id_utilisation = utilisation.id_utilisation
        INNER JOIN type_utilisation ON type_utilisation.code_utilisation = utilisation.code_utilisation';
        $req=$this->connexion->prepare($sql);
        $req->execute();
        return $results=$req->fetchAll();
    
    }

    

    public function getAllArticlesFromOneCommande($data){
        $sql='SET @num_com = ? ;';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));

        $sql= "SELECT details_commande.num_com,details_commande.id_article,description_article AS Libelle, reference_article  AS Référence, pu as Prix, qte as Qte,pu * qte as 'Montant TTC', (select qte_recep_from_one_com_and_art(details_commande.id_article,details_commande.num_com)) as 'Qte livrée'  FROM details_commande 
                INNER JOIN commande ON details_commande.num_com =commande.num_com
                INNER JOIN article ON article.id_article = details_commande.id_article
                WHERE details_commande.num_com = @num_com";

                   // AND (details_commande.qte > t1.qteLivre or t1.qteLivre IS NULL)';
        $req=$this->connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }
    public function getAllArticlesFromOneUtilisation($data){
        $sql='SET @id_utilisation = ? ;';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        $sql= 'SELECT article.id_article, reference_article, description_article, pu, qte,(pu*qte) as Montant FROM details_utilisation
        INNER JOIN article ON article.id_article = details_utilisation.id_article
        WHERE id_utilisation = @id_utilisation';
        $req=$this->connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }
    
    public function getAllArticlesFromOneCommandeEdit($data){
        $sql='SET @num_com = ? ;';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        $sql= "SELECT details_commande.num_com,details_commande.id_article,description_article, reference_article, pu, qte,pu * qte as 'Montant TTC', (select qte_recep_from_one_com_and_art(details_commande.id_article,details_commande.num_com)) as 'Qte livrée',(select qte_recep_from_one_com_and_art(details_commande.id_article,details_commande.num_com))-qte as marge
                FROM details_commande 
                INNER JOIN commande ON details_commande.num_com =commande.num_com
                INNER JOIN article ON article.id_article = details_commande.id_article
                WHERE details_commande.num_com = @num_com";
                    //AND (details_commande.qte > t1.qteLivre or t1.qteLivre IS NULL)';
        $req=$this->connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll();
    }

    public function getAllArticlesFromOneReception($data){
        $sql= 'SELECT distinct details_reception.id_article,
        description_article as Libelle,
        reference_article as Référence,
        bordereau_reception.id_fournisseur,
        pu as Prix,
        pu * (SELECT qte_com_from_one_recep_and_art(details_reception.id_article,details_reception.id_reception)) as "Montant TTC" ,
        details_reception.qte as "Qte livrée",
        (SELECT qte_com_from_one_recep_and_art(details_reception.id_article,details_reception.id_reception)) as "Qte commandée",
         (SELECT (CASE 
                            WHEN (SELECT qte_recep_from_one_com_and_art(details_reception.id_article,bordereau_reception.num_com)) > (SELECT qte_com_from_one_recep_and_art(details_reception.id_article,details_reception.id_reception))
                            THEN "Trop perçu de marchandise"
                            WHEN (SELECT qte_recep_from_one_com_and_art(details_reception.id_article,bordereau_reception.num_com))   = (SELECT qte_com_from_one_recep_and_art(details_reception.id_article,details_reception.id_reception))
                            THEN "Livraison complète"
                            WHEN (SELECT qte_recep_from_one_com_and_art(details_reception.id_article,bordereau_reception.num_com))  < (SELECT qte_com_from_one_recep_and_art(details_reception.id_article,details_reception.id_reception))  && (SELECT qte_recep_from_one_com_and_art(details_reception.id_article,bordereau_reception.num_com)) >0 
                            THEN "Livraison partielle"
                            WHEN (SELECT qte_recep_from_one_com_and_art(details_reception.id_article,bordereau_reception.num_com))< (SELECT qte_com_from_one_recep_and_art(details_reception.id_article,details_reception.id_reception)) 
                            THEN "En cours de Livraison"
                        END))as Statut,CONCAT(nom_user," ",prenom_user) as "Chargé de commande"
        FROM details_reception INNER JOIN bordereau_reception ON bordereau_reception.id_reception = details_reception.id_reception
        INNER JOIN article ON details_reception.id_article=article.id_article
        INNER JOIN commande ON bordereau_reception.num_com = commande.num_com
        INNER JOIN user ON user.id_user = commande.id_user
        WHERE details_reception.id_reception = ?';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        return $req->fetchAll();
    }
    public function getOneReception($data){
        $sql='SELECT bordereau_reception.id_reception, bordereau_reception.num_com,commande.date_Com, bordereau_reception.date_reception, bordereau_reception.id_fournisseur, nom_fournisseur,user.id_user, nom_user,prenom_user FROM bordereau_reception
                INNER JOIN fournisseur ON fournisseur.id_fournisseur = bordereau_reception.id_fournisseur
                INNER JOIN user ON user.id_user = bordereau_reception.id_user
                INNER JOIN commande ON commande.num_com = bordereau_reception.num_com
                WHERE bordereau_reception.id_reception = ?
                ORDER BY bordereau_reception.id_reception asc';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        return $req->fetch();
    }
    public function getOneUtilisation($data){
        $sql='SELECT utilisation.id_utilisation, utilisation.date_utilisation, type_utilisation.code_utilisation, libelle_utilisation, utilisation.id_user, nom_user,prenom_user FROM utilisation
        INNER JOIN type_utilisation ON type_utilisation.code_utilisation = utilisation.code_utilisation
        INNER JOIN user ON user.id_user = utilisation.id_user
        WHERE utilisation.id_utilisation = ?
        ORDER BY utilisation.id_utilisation asc';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        return $req->fetch();
    }

    public function getOneTypeUtilisation($champ,$valeur){
        $this->table="type_utilisation";
        return $this->getBy($champ,$valeur);
    }
  
    public function getOneCommande($data){
        
        $this->table='commande';
        $sql= 'SELECT * FROM commande 
        INNER JOIN user on commande.id_user = user.id_user
        INNER JOIN fournisseur on commande.id_fournisseur = fournisseur.id_fournisseur
        WHERE num_com = ?';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($data));
        return $req->fetch();
    
    }

    public function insertOneCommande($data,$dataArticles){
        $results=true;
       
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->table='commande';
                $this->insertOne($data);
                $this->table='details_commande';
                foreach($dataArticles as $article){
                    $article['num_com']=implode($this->getLastId('num_com','commande'));
                    $results=$this->insertOne($article);
                }
            $this->connexion->commit();  
        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false; 
        }
    return $results;
    }
    
    public function insertOneLivraison($data,$dataArticles){
        $results=true;
        
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// on met le mode erreur de PDO en Exception pour qu'il retourne le message d'erreur
            $this->connexion->beginTransaction();//     on commence une transaction
                $this->table='bordereau_reception';
                $this->insertOne($data);
                $this->table='details_reception';
                foreach($dataArticles as $article){
                    $article['id_reception']=implode($this->getLastId('id_reception','bordereau_reception'));
                    $this->insertOne($article);
                }
            $this->connexion->commit();
        }catch(Exception $e){
            $this->message(preg_replace('/(SQLSTATE)*(\[*\]*:*\<*\>*)(45000)*(1644)*(Unknown error)*/','',$e->getMessage()),'SQL-ERROR');
            $this->connexion->rollback();
            $results=false;
        }  
       
        return $results;
    }

    public function insertOneUtilisation($data,$dataArticles){
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->connexion->beginTransaction();
                $this->table='utilisation';
                $this->insertOne($data);
                $this->table='details_utilisation';
                foreach($dataArticles as $article){
                    $article['id_utilisation']=implode($this->getLastId('id_utilisation','utilisation'));
                    $this->insertOne($article);
                }
            $this->connexion->commit();

        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false;
        }
        return $results;
    }

    public function updateOneCommande($numCom,$commande,$articles=null){
      
        $this->table="commande";
        if($this->updateBy('num_com',$numCom,$commande)){
            $this->table="details_commande";
            $results=$this->deleteBy('num_com',$numCom);
            if($articles!=null){// si on n'a pas d'article on ne rajoute rien
                foreach($articles as $article){
                    $sql='INSERT INTO details_commande (id_article,qte,num_com) VALUES(?,?,?)';
                    $this->testSql($sql,array_values($article));
                    $req=$this->connexion->prepare($sql);
                    $req->execute(array_values($article));
                }
            }
        }
        return $results;
    }

    public function getArticleStockByCentre($idArticle,$idCentre){
        $sql= 'SELECT stock_by_article_by_centre(?,?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idArticle,$idCentre));
        return $req->fetch();
    }
/* Abandonné pour des raisons de cohérence de données. On ne doit pas pouvoir modifier une livraison ou une utilisation. Décommenter et implémenter les fonctions pour les utiliser
    public function updateOneReception(){
        $this->setDataStocks($data);
        return $this->updateOne($id,$valeur_id,$data);
    }
    public function updateOneUtilisation(){
        $this->setDataStocks($data);
        return $this->updateOne($id,$valeur_id,$data);
    }
*/

    public function deleteOneCommande($champ,$valeur){
        $results=true;
        try{
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connexion->beginTransaction();
                $this->table='details_commande';
                $this->deleteBy($champ,$valeur);
                $this->table='commande';
                $this->deleteBy($champ,$valeur);
            $this->connexion->commit();
        }catch(Exception $e){
            $this->connexion->rollback();
            $results=false;
        }
    return $results;
    }

    public function deleteOneReception($champ,$valeur){
        $this->deleteBy($champ,$valeur);
    }
    public function deleteOneUtilisation($champ,$valeur){
        $this->deleteBy($champ,$valeur);
    }


    public function getLastId($id,$table){
        $sql="SELECT MAX($id) FROM $table";
        $query=$this->connexion->prepare($sql);
        $query->execute();
        $results=$query->fetch();
        //$results=$results[0];
        return $results;
    }

    /**
     * M initialiser les champs vides à une valeur" "
     * O rien
     * I un tableau de string contenant le nom des champs de table, une variable pour initialiser un tableau de champs 
     */
    private function setDataStocks(){//envoie à la fonction data du model qui initialise les $_POST des champs transmis dans le tableau du premier parametre
        array_map([$this,'setDatas'],$this->datas,$this->data);
    }

    /* original correspondant à setDatas 1
    private function setData(){
       $this->setDatas($this->datas,$this->data);
    }
    */

    public function getMetaData($type='name'){
       
        foreach($this->datas as $data){
            if(!empty($data)){
                str_replace(['_commande','_reception','_utilisation','id_'],'',$data[0]);
                ($type==null)?$data=$this->datas:'';
            }
        }
        return $data;
    }
 
}

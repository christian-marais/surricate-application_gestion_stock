<?php
 namespace Surricate;
 use \PDO;
 use \Exception;

class Statistique extends Model{

    public function __construct(){
        $this->getConnection();//initialise la connexion
        
    }   

    public function statisticsInfosByArticle($idArticle){
        $sql= 'call statuts_des_articles_par_id(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idArticle));
        return $req->fetch();
    }

    public function statisticsInfosAllArticles(){
        $sql= 'call statuts_des_articles()';
        return $this->connexion->query($sql);
    }

    public function fluxCommandeCurrentYear(){
        $sql= 'call flux_commande_current_year()';
        return $this->connexion->query($sql);
    }
    public function fluxComCurrYearByUser($idUser){
        $sql= 'call flux_commande_current_year_by_user(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }

    public function fluxComCurrYearByUserGrpByArticle($idUser){
        $sql= 'call flux_commande_current_year_by_user_by_article(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }


    public function fluxComByCentreDate($idCentre,$dateDeb,$dateFin){
        $sql= 'call flux_reception_par_centre_date(?,?,?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idCentre,$dateDeb,$dateFin));
        return $req->fetchAll();
    }


    public function sortieCurMonthByUser($idUser){
        $sql= 'call flux_sortie_current_month_by_user(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }

    public function utilisationByCentreDate($idCentre,$dateDeb,$dateFin){
        $sql= 'call flux_utilisation_par_centre_date(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idCentre,$dateDeb,$dateFin));
        return $req->fetchAll();
    }

    
    public function utilisationByCentreDateTypeUtilis($idCentre,$mouvement,$dateDeb,$dateFin){
        $sql= 'call flux_utilisation_par_centre_date_par_mouvement(?,?,?,?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idCentre,$mouvement,$dateDeb,$dateFin));
        return $req->fetchAll();
    }

    public function utilisationByCentreDateTypeUtilisUser($idUser,$centre,$typeUtilisation,$dateDeb,$dateFin){
        $sql= 'call flux_utilisation_par_centre_date_par_mouvement(?,?,?,?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser,$centre,$typeUtilisation,$dateDeb,$dateFin));
        return $req->fetchAll();
    }

    public function comCurrentMonthByUser($idUser){
        $sql= 'call flux_commande_current_month_by_user(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }

    public function comCurrentMonthByUserGrpByArticle($idUser){
        $sql= 'call flux_commande_current_month_by_user_by_article(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }
   
    public function fluxUtilisationMensuelleUser($idUser){
        $sql= 'call flux_utilisation_month_user(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }

    public function preferedArticleByUser($idUser){
        $sql= 'call prefered_article_month_user(?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($idUser));
        return $req->fetchAll();
    }

    public function fluxUtilisation($centre,$dateDeb,$dateFin){
        $sql= '	call flux_utilisation_par_centre_date(?,?,?)';
        $req=$this->connexion->prepare($sql);
        $req->execute(array($centre,$dateDeb,$dateFin));
        var_dump($req);
        return $req->fetchAll();
    }
}   
   
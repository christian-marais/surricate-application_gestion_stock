<?php

namespace Surricate;


class Admin extends Controller{
    
    use ArticlesController;
    use FournisseursController;
    use UtilisateursController;
    use FormationsController;
    use CursusController;
    use GroupesController;
    use CentreController;
    use RolesController;

    public function index(){
        define('PAGE','espaces membres');
        
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission('INDEX',['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel('Statistique');
            if(!empty($idUser=$_SESSION['id'])){
                $commandesCurrentYear=$this->Statistique->fluxComCurrYearByUser($idUser);
                $comUserGrpByArt=$this->Statistique->fluxComCurrYearByUserGrpByArticle($idUser);
                $sortieCurrMonthUser= $this->Statistique->sortieCurMonthByUser($idUser);
                $comMonthUser = $this->Statistique-> comCurrentMonthByUser($idUser);
                $comMonthUserGrpByArt = $this->Statistique-> comCurrentMonthByUserGrpByArticle($idUser);
                $fluxMensuel = $this->Statistique->fluxUtilisationMensuelleUser($idUser);
                (!empty($preferedArticle =$this->Statistique->preferedArticleByUser($idUser)))?$_POST['preferedArticle']=$preferedArticle:$_POST['preferedArticle']=[];
            }
            $this->render('espaceperso',compact('fluxMensuel','preferedArticle','commandesCurrentYear','comUserGrpByArt','sortieCurrMonthUser','comMonthUser','comMonthUserGrpByArt'),'admin');
        }else{
            $this->redirection('auth/login');
        }
    }
    public function menus(){
        define('PAGE',__FUNCTION__);
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->render('index',[],'admin');
        }else{
            $this->redirection('auth/login');
        }
    }

    public function articles(){
        $this->manageArticles(__FUNCTION__);
    }

    public function fournisseurs(){
        $this->manageFournisseurs(__FUNCTION__);
    }

    public function utilisateurs(){
        $this->manageUtilisateurs(__FUNCTION__);
    }
   
    public function formations(){
        $this->manageFormations(__FUNCTION__);
    }

    public function groupes(){
        $this->manageGroupes(__FUNCTION__);
    }

    public function centres(){
        $this->manageCentres(__FUNCTION__);
    }

    public function cursus(){
        $this->manageCursus(__FUNCTION__);
    }

    public function roles(){
        $this->manageRoles(__FUNCTION__);
    }
   
}

?>
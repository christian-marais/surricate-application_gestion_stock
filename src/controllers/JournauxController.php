<?php

namespace Surricate;

trait JournauxController{

    public function manageJournaux($function,$slug='articles'){
        define('PAGE',$function);
        if($slug=='articles'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel('Stocks','Groupe','Utilisateur','Statistique');
            $centre=(!empty($_SESSION['id']))?$this->Utilisateur->getCentreByUserId($_SESSION['id']):'';
            (empty($_POST['id_centre']))?$_POST['id_centre']=$centre['id_centre']:"";
            $data=['id_centre','id_groupe','id_user','dateDeb','dateFin'];
            $datas=[];
            foreach($data as $s){
                (empty($_POST[$s]))?$_POST[$s]='':'';
            }
            $groupes=$this->Groupe->getAllGroupes();
            $utilisateurs=$this->Utilisateur->getUsersByGroup($_POST['id_groupe']);
            (empty($utilisateurs) && !in_array($_POST['id_user'],$utilisateurs))?$_POST['id_user']='':'';
            foreach($data as $s){
                (empty($_POST[$s]))?$_POST[$s]='':$datas[$s]=$_POST[$s];
            }
            $stockStatus=$this->Stocks->allArticleStockStatus($datas);
            if(!empty($centre) && !empty($_POST['dateDebUtil']) && !empty($_POST['dateFinUtil'])){
                $fluxUtilisations=$this->Statistique->fluxUtilisation($centre['id_centre'],$_POST['dateDebUtil'],$_POST['dateFinUtil']);
                $this->render('journal',compact("stockStatus",'fluxUtilisations','groupes','utilisateurs'));
            }
            $this->render('journal',compact("stockStatus",'groupes','utilisateurs'));
        }
    }
}
?>
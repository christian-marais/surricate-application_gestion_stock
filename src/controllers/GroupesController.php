<?php

namespace Surricate;

trait GroupesController{
    public function manageGroupes($function){//méthode qui gère les groupes
        
        define('PAGE',$function);// definit le nom de la page
    
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Groupe","Formation");//on charge les modèles utilisés par la méthode groupes
            $datas=$this->Groupe->getAllGroupes();// on récupère les infos sur tous les groupes
            $formations=$this->Formation->getAllFormations();// on récupère les formations existantes
            $centres = $this->Groupe->getAllCentres();
            $metadatas_name=$this->Groupe->getMetaDatas();// on récupère le nom des champs pour l'entete des tableaux
            $metadatas=$this->Groupe->getMetaData();// on récupère les noms cdes champs pour les boucles

            if(!empty($_POST['delete'])){//si on valide la suppresion du groupe 
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Groupe->deleteOneGroupe('id_groupe',$_POST['delete'])){//si la suppression réussit
                    $this->message("Le groupe a bien été supprimé",$function); //on envoie une notification de succès
                    $this->redirection('admin/'.$function);// on redirige vers la page d'accueil des groupes
                }    
            }elseif(isset($_POST['create'])&&!empty(($_POST['id_groupe']))&& !empty(($_POST['libelle_groupe']))){//si on valide la création d'une groupe non vide
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Groupe->insertOneGroupe()){// si le rajout du nouveau groupe réussit
                    $this->message("La groupe a été créée",$function); // on envoie une notification de succès
                    
                }else{
                    $this->message("La groupe n'a pas été créé. Renouveler avec un autre nom",$function); // on envoie une notification d'echec
                }
                $this->redirection('admin/'.$function);
            }elseif(!empty($_POST['validating_edit'])){//si on valide l'édition d'un groupe
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                $groupes=$this->Groupe->getAllGroupes();// on récupère les informations sur les groupes 
                
                $data=[// on récupère les informations mises à jour du groupe venant du formulaire
                    'id_groupe' =>$_POST['id_groupe'.$_POST['validating_edit']],
                    'libelle_groupe' =>$_POST['libelle_groupe'.$_POST['validating_edit']],
                    'id_formation'=>   $_POST['id_formation'.$_POST['validating_edit']],
                    'id_centre'=>   $_POST['id_centre'.$_POST['validating_edit']],
                ];
                if($this->Groupe->updateOneGroupe('id_groupe',$_POST['validating_edit'],$data)){// si la mise à jour réussit
               
                    $this->message('Le groupe '.$data['libelle_groupe'].' a été mis à jour',$function); // on envoie une notification de succès
                    $this->redirection('admin/'.$function);// on redirige vers l'accueil du groupe
                }else{
                    $this->message("La groupe ".$data['libelle_groupe']." n'a pas été mis à jour. Renouveler avec un autre nom",$function); // on envoie une notification d'echec
                
                }
                $this->redirection('admin/'.$function);
            }   
        }else{//si on n'est pas loggé
            $this->redirection('utilisateurs/login');// on est renvoyé à la page de login
        }
        $this->setPagination('Groupe','countGroupes',$search,$limit,$numberOfPage);
        $datas=$this->Groupe->getAllGroupes($search,$limit);// on récupère les infos actualisées des groupes
        $this->render('groupes',compact('centres','datas','formations','metadatas','metadatas_name','numberOfPage'));    // on affiche d'accueil des groupes avec les liste des groupes
    }
}

?>
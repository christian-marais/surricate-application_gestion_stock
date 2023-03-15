<?php

namespace Surricate;

trait CursusController{

    public function manageCursus($function){//méthode qui gère les groupes
        
        define('PAGE',$function);// definit le nom de la page
    
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Formation");//on charge les modèles utilisés par la méthode groupes
            
            if(!empty($_POST['delete'])){//si on valide la suppresion du groupe 
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Formation->deleteOneCursus('id_cursus',$_POST['delete'])){//si la suppression réussit
                    $this->message("Le cursus a bien été supprimé",$function); //on envoie une notification de succès
                    $this->redirection('admin/'.$function);// on redirige vers la page d'accueil des cursus
                } else{
                    $this->message("Erreur lors de la suppression",$function); //on envoie une notification de succès
                    
                }   
            }elseif(isset($_POST['create']) &&!empty($_POST['id_cursus']) && !empty($_POST['id_formation']) && !empty($_POST['id_centre'])&& !empty($_POST['annee'])){//si on valide la création d'une cursus non vide
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                
                if($this->Formation->insertOneCursus()){// si le rajout du nouveau cursus réussit
                    $this->message("Le cursus a été créé",$function); // on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }else{
                    $this->message("Le cursus n'a pas été créé. Il se peut que l'index soit déjà pris.",$function); // on envoie une notification de succès
                   
                }
                
            }elseif(!empty($_POST['validating_edit'])){//si on valide l'édition d'un cursus
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                $data=[// on récupère les informations mises à jour du cursus venant du formulaire
                    'id_cursus' =>$_POST['validating_edit'],
                    'id_formation' =>$_POST['id_formation'.$_POST['validating_edit']],
                    'id_centre' =>$_POST['id_centre'.$_POST['validating_edit']],
                    'annee' =>$_POST['annee'.$_POST['validating_edit']],
                ];

              
                if($this->Formation->updateOneCursus('id_cursus',$_POST['validating_edit'],$data)){// si la mise à jour réussit
                    
                    $this->message('Le cursus '.$data['id_cursus'].' a été mis à jour',$function); // on envoie une notification de succès
                    $this->redirection('admin/'.$function);// on redirige vers l'accueil du cursus
                }else{
                    $this->message('Le cursus '.$data['id_cursus'].' n\'a pas été mis à jour',$function); // on envoie une notification de succès
                    
                }
                
            }   
        }else{//si on n'est pas loggé
            $this->redirection('utilisateurs/login');// on est renvoyé à la page de login
        }
        $centres=$this->Formation->getAllCentres();
        $formations=$this->Formation->getAllFormations();
        $this->setPagination('Formation','countCursus',$search,$limit,$numberOfPage);
        $datas=$this->Formation->getAllCursus($search,$limit);// on récupère les infos actualisées des cursus
        $this->render('cursus',compact('centres','formations','datas','numberOfPage'));    // on affiche d'accueil des groupes avec les liste des groupes
    }

}

?>
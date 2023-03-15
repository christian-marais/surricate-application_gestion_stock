<?php

namespace Surricate;

trait CentreController{
    public function manageCentres($function){//méthode qui gère les groupes
        
        define('PAGE',$function);// definit le nom de la page
    
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Formation");//on charge les modèles utilisés par la méthode groupes
            
            if(!empty($_POST['delete'])){//si on valide la suppresion du groupe 
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Formation->deleteOneCentre('id_centre',$_POST['delete'])){//si la suppression réussit
                    $this->message("Le centre a bien été supprimé",$function); //on envoie une notification de succès
                    $this->redirection('admin/'.$function);// on redirige vers la page d'accueil des centres
                } else{
                    $this->message("Erreur lors de la suppression",$function); //on envoie une notification de succès
                    
                }   
            }elseif(isset($_POST['create'])&&!empty(($_POST['id_centre']))&& !empty(($_POST['nom_centre']))){//si on valide la création d'une centre non vide
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Formation->insertOnecentre()){// si le rajout du nouveau centre réussit
                    $this->message("La centre a été créé",$function); // on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }else{
                    $this->message("La centre n\'a pas été créé",$function); // on envoie une notification de succès
                   
                }
                
            }elseif(!empty($_POST['validating_edit'])){//si on valide l'édition d'un centre
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                $centres=$this->Formation->getAllcentres();// on récupère les informations sur les centres 
                
                $data=[// on récupère les informations mises à jour du centre venant du formulaire
                    'id_centre' =>$_POST['id_centre'.$_POST['validating_edit']],
                    'nom_centre' =>$_POST['nom_centre'.$_POST['validating_edit']]
                ];
                if($this->Formation->updateOneCentre('id_centre',$_POST['validating_edit'],$data)){// si la mise à jour réussit
                    $this->message('Le centre '.$data['nom_centre'].' a été mis à jour',$function); // on envoie une notification de succès
                    $this->redirection('admin/'.$function);// on redirige vers l'accueil du centre
                }else{
                    $this->message('Le centre '.$data['nom_centre'].' n\'a pas été mis à jour',$function); // on envoie une notification de succès
                    
                }
                
            }   
        }else{//si on n'est pas loggé
            $this->redirection('utilisateurs/login');// on est renvoyé à la page de login
        }
        $this->setPagination('Formation','countCentres',$search,$limit,$numberOfPage);
        $datas=$this->Formation->getAllCentres($search,$limit);// on récupère les infos actualisées des centres
        $this->render('centres',compact('datas','numberOfPage'));    // on affiche d'accueil des groupes avec les liste des groupes
    }

}

?>
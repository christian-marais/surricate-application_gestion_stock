<?php

namespace Surricate;

trait FormationsController{
    public function manageFormations($function){//méthode qui gère les formations
        define('PAGE',$function);
     
     
        if($_SESSION['login']=='logged'){//si on est connecté
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Formation"); // on charge les modèles utilisées par la méthode formation
            $datas=$this->Formation->getAllFormations();// on récupère les données de l'ensemble des formations
            $metadatas_name=$this->Formation->getMetaData('nom');//on récupère les noms des champs sans le nom des tables pour les tableaux
            $metadatas=$this->Formation->getMetaData();// on récupère les champs des tableaux pour les boucles
            $domaines=$this->Formation->getAllDomaines();// on récupères les infos de tous les domaines de formations
            if(!empty($_POST['delete'])){//si on valide le delete
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Formation->deleteOneFormation('id_formation',$_POST['delete'])){// si la suppression de la formation réussit
                    $this->message("La formation a bien été supprimé",$function);// on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }  
               
            }elseif(isset($_POST['create']) && !empty($_POST['id_formation']) && !empty($_POST['libelle_formation'])&& !empty($_POST['id_domaine'])){//si on valide la création d'une formation non vide
               
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Formation->insertOneFormation()){//si le rajout de la nouvelle formation réussit
                    $this->message("La formation a été créée",$function);//on envoie une notification de succès
                    $this->redirection('admin/formations');
                }
                
            }elseif(!empty($_POST['validating_edit'])){//si on valide l'édition d'une formation
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                $formations=$datas;
                $data=[//on recupère les données mises à jour de la formation venant du formulaire
                    'id_formation' =>$_POST['id_formation'.$_POST['validating_edit']],
                    'libelle_formation' =>$_POST['libelle_formation'.$_POST['validating_edit']],
                    'id_domaine' =>$_POST['id_domaine'.$_POST['validating_edit']]      
                ];
                if($this->Formation->updateOneFormation('id_formation',$_POST['validating_edit'],$data)){// si la mise à jour réussit
                    $this->message("La formation a été mise à jour",$function);// on envoie une notification de succès
                    $this->redirection('admin/formations');
                }
                $formations=$this->Formation->getAllformations();// on charge les infos actualisées des formations
            }elseif(!empty($_POST['delete_domaine'])){//si on valide la suppression d'un domaine de formation         
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Formation->deleteOneDomaine('id_domaine',$_POST['id_domaine_for_edition'])){// si la suppression réussit
                    $this->message("Le domaine de formation a bien été supprimé",$function);// on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }    
            }elseif(isset($_POST['create_domaine']) && !empty($_POST['id_domaine_create']) && !empty($_POST['libelle_domaine_create']) &&(!empty($_POST['select_code_centre']))){//si on valide la création d'une formation non vide
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                
                if($this->Formation->insertOneDomaine()){//si le rajout du nouveau domaine de formation réussit
                    $this->message("Le domaine ".$_POST['libelle_domaine_create']." a été créée",$function);
                    $this->redirection('admin/'.$function);
                }
            }elseif(!empty($_POST['validating_edit_domaine'])){//si on valide l'édition d'un domaine de formation
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                $selectedDomaine=$this->Formation->getDomaine();//on récupère les infos du domaine de formation choisi
                $formations=$datas;// on récupère les informations des formations
                $data=[// on récupère les infos mises à jour du domaine de formation venant du formulaire
                    'id_domaine' =>$_POST['id_domaine_for_edition'],
                    'libelle_domaine' =>$_POST['libelle_domaine_edit'],
                    'id_centre'=> $_POST['select_code_centre_edit']
                ];
                if($this->Formation->updateOneDomaine('id_domaine',$_POST['validating_edit_domaine'],$data)){// si la mie à jour du domaine de formation réussit
                    $this->message("Le domaine de formation ".$_POST['validating_edit_domaine']." a été mise à jour",$function);// on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }
            }
        }else{//si on n'est pas loggé
            $this->redirection('/utilisateurs/login');// on redirige vers la page de connexion
        }
        //$datas=$this->Formation->getAllFormations();// on récupère les infos actualisées des formations
        $domaines=$this->Formation->getAllDomaines();// on récupère les infos actualisées des domaines de formation
        $centres=$this->Formation->getAllCentres();
        (!empty($_POST['select_code_domaine']))? $selectedDomaine=$this->Formation->getDomaine():'';//on récupère les infos du domaine de formation choisi si non vide
        (empty($selectedDomaine))?$selectedDomaine=['id_domaine'=>'','libelle_domaine'=>'','id_centre'=>'']:'';// si les infos du domainde de formation sont vide on initialise le domaine de formation à vide
        $this->setPagination('Formation','countFormations',$search,$limit,$numberOfPage);
        $datas=$this->Formation->getAllFormations($search,$limit);// on charge les données actualisées
        $this->render('formations',compact('centres','datas','metadatas','metadatas_name','domaines','selectedDomaine','numberOfPage'));  // on affiche la page d'accueil des formations avec la liste fes formations 
    }
}

?>
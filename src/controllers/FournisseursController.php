<?php

namespace Surricate;

trait FournisseursController{

    public function manageFournisseurs($function){//methode appellé pour la gestion des fournisseurs
        define('PAGE',$function);// on indique le nom de la page d'accueil des fournisseurs
        
        if($_SESSION['login']=='logged'){//si on est connecté 
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Fournisseur");//on charge les modèles utilisées par la méthode fournisseur
            $metadatas_name=$this->Fournisseur->getMetaData('nom');// on récupère le nom des champs sans nom de tables pour l'entete des tableaux
            $metadatas=$this->Fournisseur->getMetaData();//On récupère les noms des champs pour les boucles
           
            if(!empty($_POST['edit'])){//si on veut aller à la page d'édition des fournisseurs
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                
                $datas=$this->Fournisseur->getOneFournisseur($metadatas[0],$_POST['edit']);
                if(!empty($datas) && !empty($metadatas)&&!empty($metadatas_name) ){
                $this->render('editFournisseur',compact('datas','metadatas','metadatas_name'));
                }else{
                    $this->redirection('admin/'.$function);
                }
            }elseif(isset($_POST['validating_edit'])){//si on valide la mise à jour d'un fournisseur
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Fournisseur->updateOneFournisseur('id_fournisseur',$_POST['validating_edit'])){// si la mise à jour réussit
                    $this->message("Le fournisseur ".$_POST['nom_fournisseur']." a été mis à jour.",$function);//on envoie une notification de succès
                }
                $this->redirection('admin/'.$function);
            }elseif(!empty($_POST['delete'])){//si on valide la suppresion d'un fournisseur
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Fournisseur->deleteOneFournisseur('id_fournisseur',$_POST['delete'])){// si la suppresion réussit
                    $this->message("Le Fournisseur ".$_POST[$_POST['delete']]." a bien été supprimé",$function);//on envoit une notification de succès
                }else{
                    $this->message("Suppression interdite. Le fournisseur ".$_POST[$_POST['delete']]." est lié à des articles existants",$function);// on envoie une notification d'echec
                }
                $this->redirection('admin/'.$function);
            }elseif(isset($_POST['create'])){//si on veut aller à la page de création de fournisseurs
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                $this->render('createFournisseur');//on affiche la page de création de nouveaux fournisseurs
           
            }elseif(isset($_POST['validating_create'])){//si on valide le rajout du nouveau fournisseur
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Fournisseur->insertOneFournisseur()){//si le rajout d'un nouveau fournisseur réussit 
                    $this->message("Le fournisseur ".$_POST['nom_fournisseur']." a été créé.",$function); // on envoie le message de succès
                }else{
                    $this->message("Le fournisseur ".$_POST['nom_fournisseur']." n'a pas été créé.",$function);//messages en cas d'érreur
                }
                $this->redirection('admin/'.$function);
            } 
            $this->setPagination('Fournisseur','countFournisseurs',$search,$limit,$numberOfPage);
            $datas=$this->Fournisseur->getAllFournisseurs($search,$limit);// on charge les données actualisées
            $metadatas_name=$this->Fournisseur->getMetaData('nom');// on récupère le nom des champs sans nom de tables pour l'entete des tableaux
            $metadatas=$this->Fournisseur->getMetaData();//On récupère les noms des champs pour les boucles
           
            $this->render('indexFournisseur',compact('datas','metadatas','metadatas_name','numberOfPage'));//on affiche la page index fournisseur
            
            
        }else{// si on n'est pas connecté
            $this->redirection('auth/login');// on est redirigé vers la page de connexion
        }
    }

}

?>
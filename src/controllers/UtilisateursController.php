<?php

namespace Surricate;

trait UtilisateursController{

    public function manageUtilisateurs($function){//methode appellé pour la gestion des équipiers
        define('PAGE',$function);
        
        if($_SESSION['login']=='logged'){// si on est logué
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Utilisateur","Groupe","Formation");// on charge les modèles qui seront utilisés
            
            //$datas=$this->Utilisateur->getAllUsers($search);// on récupère les infos sur les utilisateurs
            $metadatas_name=$this->Utilisateur->getMetaDatas();// on récupère les noms des champs sans le nom de table des utilisateurs
            $metadatas=$this->Utilisateur->getMetaData();// on récupère les noms des champs utilisateurs
            $groupes=$this->Groupe->getAllGroupes();//On récupère les infos des groupes
            $cursus=$this->Formation->getAllCursus();
            if(!empty($_POST['edit'])){//si on veut aller à la page d'édition de l'utilisateur
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
           
                $datas=$this->Utilisateur->getOneUser($metadatas[0],$_POST['edit']);// on charge les infos de l'utilisateur choisi
                (!empty($datas['id_cursus']))?$infosOfOneCursus=$this->Formation->getOneCursus('id_cursus',$datas['id_cursus']):$infosOfOneCursus=['id_formation'=>'','annee'=>''];// on récupère les infos d'un cursus pour l'affichage
                
                $this->render('editUser',compact('infosOfOneCursus','cursus','datas','groupes'));// on affiche la page d'édition

            }elseif(!empty($_POST['validating_edit'])){//si on valide l'édition
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Utilisateur->updateOneUser('id_user',$_POST['validating_edit'])){//si l'édition a réussie
                    $datas=$this->Utilisateur->getOneUser($metadatas[0],$_POST['validating_edit']);// on charge les infos de l'utilisateur choisi
                   (!empty($datas['id_cursus']))?$infosOfOneCursus=$this->Formation->getOneCursus('id_cursus',$datas['id_cursus']):$infosOfOneCursus=['id_formation'=>'','annee'=>''];// on récupère les infos d'un cursus pour l'affichage
            
                    $this->message("L'utilisateur ".$_POST['nom_user']." a été mis à jour.",$function);// on envoie une notification de succès
                    $this->render('editUser',compact('infosOfOneCursus','cursus','datas','groupes'));// on affiche la page d'édition
                }else{
                    $this->message("L'utilisateur ".$_POST['nom_user']." n'a pas été mis à jour. Le mail est déjà pris",$function);// on envoie une notification de succès
                }
                $this->redirection('admin/'.$function);
             

            }elseif(!empty($_POST['delete'])){//si on valide la demande de delete
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                $user=$this->Utilisateur->getOneUser('id_user',$_POST['delete']);//on récupère les infos utilisateur pour les notifications
                if($this->Utilisateur->deleteOneUser('id_user',$_POST['delete'])){//si la suppresion reussit
                    $this->message("L'utilisateur ".$user['nom_user']." a bien été supprimé",$function);// on envoie une notification de suppresion
                    $this->redirection('admin/'.$function);
                }

            }elseif(isset($_POST['create'])){//si on veut aller à la page de création
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                $this->render('createUser',compact('cursus','groupes'));// on affiche la page d'ajout d'user

            }elseif(isset($_POST['validating_create'])){//si on valide l'ajout du nouvel user
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Utilisateur->insertOneUser()){//en cas d'ajout réussi
                    $this->message("L'utilisateur ".$_POST['nom_user']." a été créé.",$function);// message de succès
                }else{
                    $this->message("L'utilisateur ".$_POST['nom_user']." n'a pas été créé.",$function);//messages en cas d'érreur
                }
                $this->redirection('admin/'.$function);
            }
            $this->setPagination('Utilisateur','countUsers',$search,$limit,$numberOfPage);
            $datas=$this->Utilisateur->getAllUsers($search,$limit);//on récupère les données actualisées
            $this->render('indexUser',compact('datas','metadatas','metadatas_name','numberOfPage'));//on affiche la page index user
           
            
        }else{// si on n'est pas connecté
            $this->redirection('auth/login');//on renvoie vers le login
        }
    }
}

?>
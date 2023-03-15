<?php

namespace Surricate;

trait RolesController{
    public function manageRoles($function){//méthode qui gère les roles
        define('PAGE',$function);
    
        if($_SESSION['login']=='logged'){// si on est connecté
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
               
            $this->loadModel("Role","Groupe");// on charge les modèles utilisées par la méthode roles
            $metadatas_name=$this->Role->getMetaData('nom');// on récupère le nom des champs sans nom de table des roles pour l'entete des tableaux
            $metadatas=$this->Role->getMetaData();// on récupère les noms des champs pour les boucles
            $permissionAttribute=json_decode($this->getEmptyPermissionTable());// on initie à vide les permissions avec une table vide
                
            //on initialise les $_POST
            $propertyToSet=['selected_id_groupe','selected_id_role','selected_libelle_groupe','selected_nom_role','role_choice'];
            foreach($propertyToSet as $s){
                (empty($_POST[$s]))?$_POST[$s]="":"";
            }
            (empty($_POST['option'])||$_POST['option']=="")?$_POST['option']="Ajouter":""; //on donne une valeur par défaut à option
            $selectedListRole=null;// on initalise la liste des role à vide

            //partie attribution de role
                //partie ajout de role pour le groupe
            if($_POST['selected_id_groupe']!=""){// si on a choisi un groupe
                $selectedGroupe=$this->Groupe->getOneGroupe('id_groupe',$_POST['selected_id_groupe']);//on recupère les informations du groupe choisie tel que le libellé
                $selectedListRole=$this->Role->getRolesOutOfGroupe($_POST['selected_id_groupe']);//on recupère la liste des roles attachés au groupe selectionné pour l'ajout
                ($_POST['option']==='Supprimer')?$selectedListRole=$this->Role->getAllRolesFromOneGroupe($_POST['selected_id_groupe']):'';//on recupère la liste des roles attachés au groupe selectionné pour l'ajout
                  
                if(empty($selectedListRole) && !in_array($_POST['selected_id_role'],$selectedListRole)){
                   $_POST['selected_id_role']="";
                    $_POST['selected_nom_role']="";
                }

                $_POST['selected_libelle_groupe']=$selectedGroupe['libelle_groupe'];// on recupere le libelle du groupe et on le transmet au formulaire
                $selectedRole=$this->Role->getOneRole('code_role',$_POST['selected_id_role']);//on recupere les informations du role si on en a choisi un
                (!empty($selectedRole))?$_POST['selected_nom_role']=$selectedRole['nom_role']:'';// si on un role correspondant on recupère le libellé du role et le transmet au formulaire
              
            }
            
            if(isset($_POST['add_role'])){//si on veut ajouter un role 
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                if($this->Role->addRoletoGroupe($_POST['selected_id_groupe'],$_POST['selected_id_role'])){// si l'ajout reussie
                    $this->message('Le role '.$_POST["selected_id_role"].' a bien été rajouté au groupe '.$_POST["selected_id_groupe"],$function);// on envoie une notification de succès
                }else{//sinon un message d'échec
                    $this->message("Un probleme est survenue lors de l'ajout",$function);
                    
                }
                $this->redirection('admin/roles');
            }
            if(!empty($_POST['delete_role_from_groupe'])){// si on veut supprimer un role d'un groupe
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Role->deleteRoleFromGroupe($_POST['selected_id_groupe'],$_POST['selected_id_role'])){// si la suppression reussie
                    $this->message('Le role '.$_POST["delete_role_from_groupe"].' a bien été supprimé du groupe '.$_POST["id_groupe"],$function);// on envoie une notification de succès
                }else{// sinon un message d'erreur
                    $this->message("Un probleme est survenue lors de la suppression",$function);
                }
                $this->redirection('admin/roles');
            }
            if(isset($_POST['role_choice'])){//si on choisit un role dont on veu editer les permissions
                $emptyPermissionTable=json_decode($this->getEmptyPermissionTable());//on recupère une table vide des permissions comprenant toutes les pages
                try{
                    $permissionAttribute=@$this->setUserPermissionTable();//on recupère la table vide de permission mise à jour des permission utilisateurs
                }catch(Exception $e){

                }
            }

            if(!empty($_POST['role_choice']) && !empty($_POST['validating_edit_permission'])){//si on a choisit un role et que l'on édite les permission
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                $permissions=[];//on initialise un tableau de permission vide
                foreach(Security::PERMISSION_CONTENU as $idPage){// pour chaque page
                    $score=0;//on met démarre à un score de 0 en permissions
                    foreach(Security::PERMISSION_EDITION as $keyPerm => $valuePerm){//pour chaque permission du tableau de permission
                        if(!empty($_POST[$keyPerm.$idPage])){// si on a un envoi de mise à jour de la permission correspondante de la page correspondante
                            $score= $score + $valuePerm;// on rajoute le score de la permission à notre score en cours
                        }
                    }
                    $permissions[$idPage]=['id'=>$idPage,'permissions'=>$score];// on récupère le score total de la page
                }
                $data=[
                    'code_role'=>$_POST['role_choice'],
                    'permission'=>''.json_encode(array_values($permissions))];// on récupère le score et prépare l'envoi des données pour la requete sql
                
                if($this->Role->updateOneRole('code_role',$_POST['role_choice'],$data)){// on transmet les données. Si la requete réussit
                    $this->message('Les permissions ont été mises à jour.',$function);// on envoie un succès
                }else{// dans le cas contraire 
                    $this->message('Erreur lors de la mise à jour des permissions.',$function);// on envoie un échec

                }
                $this->redirection('admin/roles');
                $permissions=$this->Role->getOneRole('code_role',$_POST['role_choice']);// on recupère les infos de role
            }

            //Partie création,édition et suppression des roles 
            if(!empty($_POST['delete'])){//si on valide la suppresion d'un role
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
               
                if($this->Role->deleteOneRole('code_role',$_POST['delete'])){// si la suppression reussit
                    $this->message("Le role a bien été supprimé",$function);//message de succès
                    $this->redirection('admin/'.$function);// on est redirigé vers l'index Role
                }    
            }elseif(isset($_POST['create_role'])&&!empty(($_POST['create_code_role']))&& !empty(($_POST['create_nom_role']))){//si on valide la création d'un role non vide
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
               
                $data=['code_role'=>$_POST['create_code_role'],"nom_role"=>$_POST['create_nom_role'],"permission"=>$_POST['create_permission']];
                if($this->Role->insertOneRole($data)){// si le rajout réussit
                    $this->message("La role a été créée",$function);//message de succès
                    $this->redirection('admin/'.$function);//redirection vers l'index roles
                }
                
            }elseif(!empty($_POST['validating_edit'])){//si on valide l'édition
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               
                $data=[// on récupère les données du formulaire
                    'code_role' =>$_POST['code_role'.$_POST['validating_edit']],
                    'nom_role' =>$_POST['nom_role'.$_POST['validating_edit']]   // a ne pas supprimer . Sinon lors des mises à jour la valeur ne sera pas récupérée
                ];
                if($this->Role->updateOneRole('code_role',$_POST['validating_edit'],$data)){// si la mise à jour réussit
                    ($_SESSION['role']==$_POST['validating_edit'])?$_SESSION['role']=$_POST['code_role'.$_POST['validating_edit']]:'';//on update le role de la session en cours
                    $this->message("Le role a été mis à jour",$function);// message de succès
                    $this->redirection('admin/roles');
                   
                }
            }   
        }else{//si on n'est pas loggé
            $this->redirection('auth/login');
        }
        $datas['groupe']=$this->Groupe->getAllGroupes();
        $this->setPagination('Role','countRoles',$search,$limit,$numberOfPage);
        $rolesForPermission=$this->Role->getAllRoles();
        $datas['role']=$rolesForPermission;// on récupères les infos sur l'ensemble des rôles
        $roles=$this->Role->getAllRoles($search,$limit);// on charge les données actualisées
        $this->render('roles',compact('rolesForPermission','permissionAttribute','roles','datas','metadatas','selectedListRole','metadatas_name','numberOfPage'));  // on affiche la page index des roles 
    }
}

?>
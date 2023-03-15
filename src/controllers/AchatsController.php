<?php

namespace Surricate;

trait AchatsController{

    public function manageAchats($function){
        define('PAGE',$function);
        if($_SESSION['login']=='logged'){
          
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel('Article','Stocks','Fournisseur','Utilisateur','Formation');
            $allDatas=$this->Stocks->getAllDatas('user','fournisseur','article','famille_article');
            $commandes=$this->Stocks->getAllCommandes();

            if((empty($_POST['selectedDate'])&& empty($_POST['date_Com'])) && (empty($_POST['validating_edit'])&& empty($_POST['validating_create']))){
                unset($_SESSION['selectedListArticles']);
            } 
           
            if(isset($_POST['validating_edit'])){
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
               if(!empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:"";
                        (!empty($_POST['qte_reception'.$article['id_article']]))?$_SESSION['selectedListArticles'][$article['id_article']]['Montant TTC']=$_POST['qte_reception'.$article['id_article']] * $_SESSION['selectedListArticles'][$article['id_article']]['pu']:"";
                    }
                }
                $commande=[
                    "date_Com" =>$_POST['date_Com'],
                    "id_fournisseur" =>$_POST['id_fournisseur'],
                    "id_user" =>$_POST['id_user'],
                    'id_centre'=>$_POST['id_centre']
                ];
                
                foreach($_SESSION['selectedListArticles'] as $article){
                    if(!empty($article['id_article'])){
                        $articles[$article['id_article']]=[
                            'id_article'=>$article['id_article'],
                            'qte'=>$article['qte'],
                            'num_com'=>$_POST['validating_edit']
                        ];
                    }else{
                        $articles=null;
                    }
                }
                if(@$this->Stocks->updateOneCommande($_POST['validating_edit'],$commande,$articles)){
                    $this->message('La commande '.$_POST['validating_edit'].' a bien été enregistrée',$function);
                }else{
                    $this->message('L\'enregistrement de la commande '.$_POST['validating_edit'].'a échouée',$function);
                }
                $this->redirection('stock/'.$function);
            }elseif(!empty($_POST['editCommande'])||!empty($_POST['date_Com'])){
                (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
                if(!empty($_POST['editCommande'])){
                    $commande=$this->Stocks->getOneCommande($_POST['editCommande']);
                    (empty($_POST['id_centre']))?$_POST['id_centre']=$commande['id_centre']:'';
                    (empty($_POST['id_centre']))?$_POST['id_centre']='':'';
                    (empty($_POST['date_Com']))?$_POST['date_Com']=$commande['date_Com']:'';
                    (empty($_POST['id_user']))?$_POST['id_user']=$commande['id_user']:'';
                    (empty($_POST['id_fournisseur']))?$_POST['id_fournisseur']=$commande['id_fournisseur']:'';
                    $articles=$this->Stocks->getAllArticlesFromOneCommandeEdit($_POST['editCommande']);
                    if(!empty($articles)){
                        foreach($articles as $article){
                            $_SESSION['selectedListArticles'][$article['id_article']]=$article;
                        }
                    }
                }
                $selectedUser = $this->Utilisateur->getOneUser('id_user',$_POST['id_user']);
                (empty($selectedUser))?$selectedUser=['nom_user'=>$commande['nom_user'],'prenom_user'=>$commande['prenom_user']]:'';
                $selectedFournisseur = $this->Fournisseur->getOneFournisseur('id_fournisseur',$_POST['id_fournisseur']);
                (empty($selectedFournisseur))?$selectedFournisseur=['nom_fournisseur'=>$commande['nom_fournisseur']]:'';
                 
                if(!empty($_POST['id_famille_article'])){
                    $selectedArticles = $this->Article->getAllArticlesBy('id_famille_article',$_POST['id_famille_article']);
                    $changedIdFamilleArticle=false;
                }else{
                    $_POST['id_famille_article'] ="";
                    $selectedArticles= "";
                }
               
                //*******************logique des variables articles ou entité details_commande*****************
                if(!empty($_POST['select_id_article'])){
                    foreach($selectedArticles as $item){
                        ($item['id_article']==$_POST['select_id_article'])?$changedIdFamilleArticle="true":'';
                    }
                    (!$changedIdFamilleArticle)?$_POST['select_id_article']="":'';
                    $selectedArticle = $this->Article->getOneArticle('id_article',$_POST['select_id_article']);
                    ($selectedArticle===false)?$selectedArticle=['description_article'=>'']:"";
                  
                }else{
                    $_POST['select_id_article']="";
                    $selectedArticle=['description_article'=>''];
                }
                
                if(empty($_SESSION['selectedListArticles'])){
                    $_SESSION['selectedListArticles']['article vide']=[
                        'id_article'=>'',
                        'reference_article'=>'',
                        'description_article'=>'',
                        'pu'=>'',
                        'Qte livrée'=>'',
                        'qte'=>'',
                        'Montant TTC'=>''
                    ];
                }
                if(isset($_POST['add_article']) && !empty($_POST['select_id_article'])&&!empty($selectedArticle)){
                    unset($_SESSION['selectedListArticles']['article vide']);
                    $articleChosen=&$_SESSION['selectedListArticles'][$_POST['select_id_article']];
                    $articleChosen=$selectedArticle;
                    $articleChosen['Qte livrée']=0;
                    $articleChosen['qte']=0;
                    $articleChosen['Montant TTC']=0; 
                }
               
                if(!empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:"";
                        (!empty($_POST['qte_reception'.$article['id_article']]))?$_SESSION['selectedListArticles'][$article['id_article']]['Montant TTC']=$_POST['qte_reception'.$article['id_article']] * $_SESSION['selectedListArticles'][$article['id_article']]['pu']:"";
                    }
                }

                if(!empty($_POST['delete'])){
                    unset($_SESSION['selectedListArticles'][$_POST['delete']]);
                }
                $centres=$this->Formation->getAllCentres();
                $selectedListArticles=$_SESSION['selectedListArticles'];
                $this->render('editCommande',compact('centres','selectedListArticles','allDatas','commandes','selectedArticles','selectedArticle','selectedFournisseur','selectedUser'));
                
            }elseif(!empty($_POST['deleteCommande'])){
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
                if($this->Stocks->deleteOneCommande('num_com',$_POST['deleteCommande'])){
                    $this->message('La suppression de la commande '.$_POST["deleteCommande"].' a réussie',$function);
                }else{
                    $this->message('Suppresion impossible de la commande ('.$_POST["deleteCommande"].') .Elle a déjà été livrée',$function);
                }
                $this->redirection('stock/'.$function);
            }elseif(isset($_POST['validating_create'])){
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                if(!empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:"";
                    }
                }
                $commande=[
                    "date_Com" =>$_POST['selectedDate'],
                    "id_fournisseur" =>$_POST['id_fournisseur'],
                    "id_user" =>$_POST['id_user'],
                    'id_centre' =>$_POST['id_centre']
                ];
                if(!empty($commande) && !empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        $articles[$article['id_article']]=[
                            'id_article'=>$article['id_article'],
                            'qte'=>$article['qte']
                        ];
                    }
                }
            

                if(!empty($articles) && $this->Stocks->insertOneCommande($commande,$articles)){
                    $this->message('La commande a bien été enregistrée',$function);
                    $newMail= new Mail();
                    $newMail->sendMailCopyToRespOrga($commande,$articles);
                }else{
                    $this->message('L\'enregistrement de la commande a échoué',$function);
                }
                $this->redirection('stock/'.$function);
            }elseif(isset($_POST['create'])||isset($_POST['selectedDate'])){
                (empty($_POST['selectedDate']))?Router::setDate('selectedDate'):'';
                //logique des variables

                (empty($_POST['id_user']) && !empty($_SESSION['id']))?$_POST['id_user']=$_SESSION['id']:"";
                    
                if(!empty($_POST['id_user'])){
                    $selectedIdUser = $_POST['id_user'];
                    $user = $this->Utilisateur->getOneUser('id_user',$_POST['id_user']);

                }else{
                    $selectedIdUser ="";
                    (empty($user))?$user=['nom_user' =>"",'prenom_user'=>""]:'';
                }
                if(!empty($_POST['id_fournisseur'])){
                    $selectedIdFournisseur = $_POST['id_fournisseur'];
                    $selectedFournisseur = $this->Fournisseur->getOneFournisseur('id_fournisseur',$selectedIdFournisseur);
                   
                }else{
                    $selectedIdFournisseur ="";
                    (empty($selectedFournisseur))?$selectedFournisseur=['nom_fournisseur' =>""]:'';
                }
                
                if(!empty($_POST['id_famille_article'])){
                    $selectedIdFamilleArticle = $_POST['id_famille_article'];
                    $selectedArticles = $this->Article->getAllArticlesBy('id_famille_article',$selectedIdFamilleArticle);
                    $changedIdFamilleArticle=false;
                    
                }else{
                    $selectedIdFamilleArticle ="";
                    $selectedArticles=array(['id_article'=>'']);
                }
                // logique des variables articles
                if(!empty($_POST['select_id_article'])){
                  
                    foreach($selectedArticles as $item){
                        ($item['id_article']==$_POST['select_id_article'])?$changedIdFamilleArticle="true":'';
                    }
                    (!$changedIdFamilleArticle)?$_POST['select_id_article']="":'';
                    $selectedIdArticle = $_POST['select_id_article'];
                    $selectedArticle = $this->Article->getOneArticle('id_article',$selectedIdArticle);
                    ($selectedArticle===false)?$selectedArticle=['description_article'=>'']:"";
                  
                }else{
                    $selectedIdArticle="";
                    $selectedArticle=['description_article'=>''];
                }
                
                if(empty($_SESSION['selectedListArticles'])){
                    $_SESSION['selectedListArticles'][0]=[
                        'id_article'=>'',
                        'reference_article'=>'',
                        'description_article'=>'',
                        'id_famille_article'=>'',
                        'unite'=>'',
                        'pu'=>'',
                        'qte'=>''];
                }
                if(isset($_POST['add_article']) && !empty($selectedIdArticle)&&!empty($selectedArticle)){
                    unset($_SESSION['selectedListArticles'][0]);
                    $_SESSION['selectedListArticles'][$selectedIdArticle]=$selectedArticle;
                    $_SESSION['selectedListArticles'][$selectedIdArticle]['qte']=0;
                }
                if(!empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:"";
                    }
                }
               
                if(!empty($_POST['delete'])){
                    unset($_SESSION['selectedListArticles'][$_POST['delete']]);
                }
                (empty($_POST['id_centre'])&&!empty($_SESSION['id']))?$userCentre=$this->Utilisateur->getCentreByUserId($_SESSION['id']):'';
                (empty($_POST['id_centre'])&&!empty($userCentre))?$_POST['id_centre']=$userCentre['id_centre']:'';
                $_POST['numero_commande']=intVal(implode('',($this->Stocks->getLastId('num_com','commande'))))+1;
                $centres=$this->Formation->getAllCentres();
                $selectedListArticles=$_SESSION['selectedListArticles'];
                $this->render('createCommande',compact('centres','allDatas','commandes','selectedIdFournisseur','selectedListArticles','selectedIdUser','selectedIdFamilleArticle','selectedIdArticle','selectedArticles','selectedArticle','user','selectedFournisseur','article'));
            }
            $this->setPagination('Stocks','countCommandes',$search,$limit,$numberOfPage);
            $commandes=$this->Stocks->getAllCommandes($search,$limit);
            
            $this->render('achats',compact('allDatas','commandes','numberOfPage'));
        }else{
            $this->redirection('auth/login');
        }
        

    }

}
?>
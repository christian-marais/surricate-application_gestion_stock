<?php

namespace Surricate;

trait LivraisonsController{

    public function manageLivraisons($function){
        define('PAGE',$function);
        (empty($_POST['date_reception']))?Router::setDate('date_reception'):'';
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel('Article','Stocks','Fournisseur','Utilisateur');
            $datas=$this->Stocks->getAllReceptions();
            $allDatas=$this->Stocks->getAllDatas('user','fournisseur','commande','article','famille_article');
            $metadatas=$this->Stocks->getMetadata();
            $numeroDeLivraison=$this->Stocks->getLastId('id_reception','bordereau_reception');
            $numeroDeLivraison=array_values($numeroDeLivraison)[0];
            if(!$numeroDeLivraison){
                $numeroDeLivraison = 1;
            }else{
                $numeroDeLivraison++;
            }
            if(isset($_POST['validating_edit'])){
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                if($this->Stocks->updateOneReception('id_reception',$_POST['validating_edit'])){
                    $this->message("La reception a été mise à jour",'reception');
                }
                $reception=$this->Stocks->getOneReception('id_reception',$_POST['validating_edit']);
                $this->render('editUtilisation',compact('reception','fournisseur','user'));
            }elseif(isset($_POST['validating_create'])){
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                if(!empty($_SESSION['selectedListArticles'])&&$_POST['validating_create']==="ac"){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:$_SESSION['selectedListArticles'][$article['id_article']]['qte']=0;
                    }
                }
                $dataLivraison=[
                    'date_reception'=>$_POST['date_reception'],
                    'id_user' =>$_POST['id_user'],
                    'id_fournisseur'=>$_POST['id_fournisseur'],
                    'num_com' =>$_POST['num_com'],
                    'id_centre'=>$_SESSION['centre']
                ];
               
                if(!empty($dataLivraison) && !empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $item){
                        $dataArticles[$item['id_article']]=[
                            'id_article'=> $item['id_article'],
                            'qte'=>$item['qte']
                        ];
                    }
                }
                if(!empty($dataArticles) && $this->Stocks->insertOneLivraison($dataLivraison,$dataArticles)){
                    $this->message("La livraison a bien été enregistrée",$function);
                }else{
                    $this->message("La livraison ne peut être enregistrée.",$function);
                }
                unset($_SESSION['selectedListArticles']);
                $this->redirection('stock/'.$function);
            }elseif(isset($_POST['option']) && $_POST['option']=="Sans commande"){
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                (empty($_POST['qte_reception']))?$_POST['qte_reception']="":"";
                unset($_SESSION['selectedListArticles'][0]);
                if(!empty($_POST['id_user'])){
                    $selected_id_user = $_POST['id_user'];
                    $user = $this->Utilisateur->getOneUser('id_user',$_POST['id_user']);
                }else{
                    $selected_id_user ="";
                    (empty($user)||$user===false)?$user=['nom_user' =>"",'prenom_user'=>""]:'';
                }
                
                if(!empty($_POST['id_fournisseur'])){
                    $selected_id_fournisseur = $_POST['id_fournisseur'];
                    $selectedFournisseur = $this->Fournisseur->getOneFournisseur('id_fournisseur',$selected_id_fournisseur);
                }else{
                    $selected_id_fournisseur ="";
                    (empty($selectedFournisseur)||$selectedFournisseur===false)?$selectedFournisseur=['nom_fournisseur' =>" "]:'';
                }
                    //logique de selection d'articles
                if(!empty($_POST['id_famille_article'])){
                    $selected_id_famille_article = $_POST['id_famille_article'];
                    $selectedArticles = $this->Article->getAllArticlesBy('id_famille_article',$selected_id_famille_article);
                    $changedIdFamilleArticle=false;
                }else{
                    $selected_id_famille_article ="";
                    $selectedArticles=array(['id_article'=>'']);
                }
                // logique des variables articles
                if(!empty($_POST['select_id_article'])){
                    foreach($selectedArticles as $item){
                        ($item['id_article']==$_POST['select_id_article'])?$changedIdFamilleArticle="true":'';
                    }
                    (!$changedIdFamilleArticle)?$_POST['select_id_article']="":'';
                    $selected_id_article = $_POST['select_id_article'];
                    $selectedArticle = $this->Article->getOneArticle('id_article',$selected_id_article);
                    ($selectedArticle===false)?$selectedArticle=['description_article'=>'']:"";
                  
                }else{
                    $selected_id_article="";
                    $selectedArticle=['description_article'=>''];
                }
                (empty($_POST['qte_reception'.$selected_id_article]))?$_POST['qte_reception'.$selected_id_article]=0:'';
                
                if(isset($_POST['add_article']) && !empty($selected_id_article)&&!empty($selectedArticle)){
                    $_SESSION['selectedListArticles'][$selected_id_article]=$selectedArticle;
                    $_SESSION['selectedListArticles'][$selected_id_article]['qte']=0;
                }
                if(!empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:"";
                    }
                }
               
                if(!empty($_POST['delete'])){
                    unset($_SESSION['selectedListArticles'][$_POST['delete']]);
                }
                (!empty($_SESSION['selectedListArticles']))?$selectedListArticles=$_SESSION['selectedListArticles']:'';
                (empty($selectedListArticles))?$selectedListArticles=
                array([
                    'id_article'=>'',
                    'reference_article'=>'',
                    'description_article'=>'',
                    'id_famille_article'=>'',
                    'unite'=>'',
                    'pu'=>'',
                    'qte'=>''
                ]):'';
                $this->render('createReceptionSC',compact('allDatas','selectedArticles','selectedArticle','selectedListArticles','selected_id_article','selected_id_fournisseur','selectedFournisseur','selected_id_user','user','numeroDeLivraison','selected_id_famille_article'));
            }elseif(isset($_POST['create']) || (isset($_POST['option']) && $_POST['option']=="Avec commande")){
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';   
                unset($_SESSION['selectedListArticles']);
                Router::setDate('date_reception');
                //logique des variables
                if(!empty($_POST['num_com'])){
                    unset($_SESSION['selectedListArticles']);
                    $selected_id_commande = $_POST['num_com'];
                    $commande=$this->Stocks->getOneCommande($selected_id_commande);
                    $articles=$this->Stocks->getAllArticlesFromOneCommande($_POST['num_com']);
                    foreach($articles as $article){
                        $_SESSION['selectedListArticles'][$article['id_article']]=$article; 
                        (!empty($_POST['qte_reception'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_reception'.$article['id_article']]:$_SESSION['selectedListArticles'][$article['id_article']]['qte']=0;// si les quantités livrées à renseigner sont vides, on les met à zéro
                    }
                }else{
                    $selected_id_commande="";
                    (empty($commande))?$commande=['num_com'=>'','id_fournisseur'=>'','nom_fournisseur'=>'']:'';
                    (empty($articles))?$articles[0]=['id_article'=>'','Libelle'=>'','Référence'=>'','Prix'=>'','Qte'=>'','Montant TTC'=>'','Qte livrée'=>'']:'';
                }
                (empty($_POST['id_user']) && !empty($_SESSION['id']))?$_POST['id_user']=$_SESSION['id']:"";
                if(!empty($_POST['id_user'])){
                    $selected_id_user = $_POST['id_user'];
                    $user = $this->Utilisateur->getOneUser('id_user',$_POST['id_user']);
                }else{
                    $selected_id_user ="";
                    (empty($user))?$user=['nom_user' =>"",'prenom_user'=>""]:'';
                }
                if(empty($_SESSION['selectedListArticles'])){
                    $_SESSION['selectedListArticles'][0]=[
                        'id_article'=>'',
                        'Libelle'=>"",
                        'Référence'=>'',
                        'Prix'=>'',
                        'Qte'=>'',
                        'Montant TTC'=>"",
                        'Qte livrée'=>'',
                        'qte'=>''];
                }
                $this->render('createReception',compact('allDatas','selected_id_commande','selected_id_user','user','commande','numeroDeLivraison'));
            }elseif(!empty($_POST['see'])){
                (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
                unset($_SESSION['selectedListArticles']);
                $livraison=$this->Stocks->getOneReception($_POST['see']);
                $selectedListArticles= $this->Stocks->getAllArticlesFromOneReception($_POST['see']);
                $this->render('pop_up_reception',compact('datas','livraison','metadatas','selectedListArticles'));
            }
            $this->setPagination('Stocks','countReceptions',$search,$limit,$numberOfPage);
            $datas=$this->Stocks->getAllReceptions($search,$limit);
            
            $this->render('reception',compact('datas','metadatas','numberOfPage'));
        }else{
            $this->redirection('auth/login');
        }
    }
}
?>
<?php

namespace Surricate;

trait UtilisationsController{

    public function manageUtilisations($function){
        define('PAGE',$function);
        $selected_date_utilisation = Router::localDate();
        if($_SESSION['login']=='logged'){
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel('Article','Stocks','Fournisseur','Utilisateur','Groupe','Formation');
             $centre=(!empty($_SESSION['id']))?$this->Utilisateur->getCentreByUserId($_SESSION['id']):'';
            (empty($_POST['id_centre']))?$_POST['id_centre']=$centre['id_centre']:"";
            $allDatas=$this->Stocks->getAllDatas('user','type_utilisation','utilisation','article','famille_article');
            $metadatas=$this->Stocks->getMetadata();
            $numeroUtilisation=$this->Stocks->getLastId('id_utilisation','utilisation');
            $numeroUtilisation=array_values($numeroUtilisation)[0];
            if(!$numeroUtilisation){
                $numeroUtilisation = 1;
            }else{
                $numeroUtilisation++;
            }
            if(isset($_POST['validating_create'])){
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                if(!empty($_SESSION['selectedListArticles'])&& isset($_POST['validating_create'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_utilisation'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_utilisation'.$article['id_article']]:$_SESSION['selectedListArticles'][$article['id_article']]['qte']=0;// on récupère la quantité renseignée ou la met à zéro si vide
                    }
                }
                $dataUtilisation=[
                    'date_utilisation'=>$_POST['date_utilisation'],
                    'id_user' =>$_POST['id_user'],
                    'code_utilisation'=>$_POST['code_utilisation'],
                    'id_centre'=>$_POST['id_centre']
                ];
                foreach($_SESSION['selectedListArticles'] as $item){
                    $dataArticles[$item['id_article']]=[
                        'id_article'=> $item['id_article'],
                        'qte'=>$item['qte']
                    ];
                }
                $sortie = ['produit_deteriore','sortie','transfert Sortie','regul_inv Sortie'];
                if(in_array($dataUtilisation['code_utilisation'],$sortie)){
                    (!empty($_SESSION['id']))?$userCentre=$this->Utilisateur->getCentreByUserId($_SESSION['id']):$this->message('Session non valide',$function);
                    if(empty($userCentre)){
                        $this->message('Vous ne possédez pas de centre identifié. Accès refusé',$function);
                        $this->redirection('');
                    }
                    foreach($dataArticles as $article){
                        if($article['qte'] >(intVal(implode($this->Stocks->getArticleStockByCentre($article['id_article'],'Saint-Andr'))))){
                            $this->message('Le nombre d\'article retiré, détérioré ou perdu ne peut être supérieur au stock disponible',$function);
                            $this->redirection('stock/utilisations');
                        }
                    }
                }
                if($this->Stocks->insertOneUtilisation($dataUtilisation,$dataArticles)){
                    $this->message("Votre demande a bien été enregistrée",$function);
                }else{
                    $this->message("Erreur. L'enregistrement n'a pas pu aboutir",$function);
                }
                unset($_SESSION['selectedListArticles']);
                $this->redirection('stock/utilisations');
            }elseif(isset($_POST['create']) || isset($_POST['date_utilisation'])){
                (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
                (empty($_POST['qte_utilisation']))?$_POST['qte_utilisation']="":"";
                unset($_SESSION['selectedListArticles'][0]);
                (empty($_POST['id_user']) && !empty($_SESSION['id']))?$_POST['id_user']=$_SESSION['id']:"";
                
                if(!empty($_POST['id_user'])){
                    $selected_id_user = $_POST['id_user'];
                    $user = $this->Utilisateur->getOneUser('id_user',$_POST['id_user']);
            
                }else{
                    $selected_id_user ="";
                    (empty($user)||$user===false)?$user=['nom_user' =>"",'prenom_user'=>""]:'';
                }
                if(!empty($_POST['date_utilisation'])){
                    $selected_date_utilisation = $_POST['date_utilisation'];
                }
                if(!empty($_POST['code_utilisation'])){
                    $selected_code_utilisation = $_POST['code_utilisation'];
                    $selectedTypeUtilisation = $this->Stocks->getOneTypeUtilisation('code_utilisation',$selected_code_utilisation);
                }else{
                    $selected_code_utilisation ="";
                    (empty($selectedTypeUtilisation)||$selectedTypeUtilisation===false)?$selectedTypeUtilisation=['libelle_utilisation' =>" "]:'';
                }
                $_POST['libelle_utilisation'] = $selectedTypeUtilisation['libelle_utilisation']; 
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
                (empty($_POST['qte_utilisation'.$selected_id_article]))?$_POST['qte_utilisation'.$selected_id_article]=0:'';
                
                if(isset($_POST['add_article']) && !empty($selected_id_article)&&!empty($selectedArticle)){
                    $_SESSION['selectedListArticles'][$selected_id_article]=$selectedArticle;
                    $_SESSION['selectedListArticles'][$selected_id_article]['qte']=0;
                }
                if(!empty($_SESSION['selectedListArticles'])){
                    foreach($_SESSION['selectedListArticles'] as $article){
                        (!empty($_POST['qte_utilisation'.$article['id_article']]))? $_SESSION['selectedListArticles'][$article['id_article']]['qte']=$_POST['qte_utilisation'.$article['id_article']]:"";
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
                (empty($_POST['id_centre'])&&!empty($_SESSION['id']))?$userCentre=$this->Utilisateur->getCentreByUserId($_SESSION['id']):'';
                (empty($_POST['id_centre'])&&!empty($userCentre))?$_POST['id_centre']=$userCentre['id_centre']:'';
                $centres=$this->Formation->getAllCentres();
                $this->render('createUtilisation',compact('centres','selected_date_utilisation','allDatas','selectedArticles','selectedArticle','selectedListArticles','selected_id_article','selected_code_utilisation','selectedTypeUtilisation','selected_id_user','user','numeroUtilisation','selected_id_famille_article'));

            }elseif(!empty($_POST['see'])){
                (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
                    
                unset($_SESSION['selectedListArticles']);
                $data=['id_groupe','id_user','dateDeb','dateFin','search','offset-limit'];
                $datas=[];
                $groupes=$this->Groupe->getAllGroupes();
                $utilisateurs=$this->Utilisateur->getUsersByGroup($_POST['id_groupe']);
                (empty($utilisateurs) && !in_array($_POST['id_user'],$utilisateurs))?$_POST['id_user']='':'';
                foreach($data as $s){
                (empty($_POST[$s]))?$_POST[$s]='':$datas[$s]=$_POST[$s];
                }
                $datas=$this->Stocks->getAllUtilisations($datas);

                if($utilisations=$this->Stocks->getOneUtilisation($_POST['see'])){
                    $selectedListArticles= $this->Stocks->getAllArticlesFromOneUtilisation($_POST['see']);
                    $this->render('pop_up_utilisation',compact('datas','utilisations','metadatas','selectedListArticles'));
                }else{
                    $this->redirection('pages/erreur404');
                }
               
            }
            $data=['id_groupe','id_user','dateDeb','dateFin','search','offset-limit'];
            $datas=[];
            $groupes=$this->Groupe->getAllGroupes();
            $utilisateurs=$this->Utilisateur->getUsersByGroup($_POST['id_groupe']);
            (empty($utilisateurs) && !in_array($_POST['id_user'],$utilisateurs))?$_POST['id_user']='':'';
            foreach($data as $s){
            (empty($_POST[$s]))?$_POST[$s]='':$datas[$s]=$_POST[$s];
            }
            $datas=$this->Stocks->getAllUtilisations($datas);
           
            $this->render('utilisation',compact('datas','metadatas','groupes','utilisateurs'));
        }else{
            $this->redirection('auth/login');
        }
        

    }
}
?>
<?php

namespace Surricate;

trait ArticlesController{

    public function manageArticles($function){
        define('PAGE',$function);// on indique le nom de la page d'accueil des articles
            
        if($_SESSION['login']=='logged'){
    
            (!$this->checkUserContenuPermission(PAGE,['LECTURE']))?$this->redirection('pages/blocked'):'';
            $this->loadModel("Article","Categorie","Fournisseur");// on charge l'ensemble des modèles utilisées par la méthode articles
            $metadatas_name=$this->Article->getMetaData('nom');// on charge les nom des champs pour l'entete des tableaux
            $metadatas=$this->Article->getMetaData();//on charge les nom des champs pour réaliser des boucles
            $categories=$this->Categorie->getAllCategories();// on récupère toutes les catégories d'articles 
            $articles=$this->Article->getAllArticles();// on récupère tous les articles
            $fournisseurs=$this->Fournisseur->getAllFournisseurs();// on récupère tous les fournisseurs
    
            if(!empty($_POST['edit'])){//si on veut accéder à la page d'édition d'un article
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                $article=$this->Article->getOneArticle('id_article',$_POST['edit']);//on recupère les données de l'article
                $this->render('editArticle',compact('article','metadatas','categories','fournisseurs'));// on affiche la page d'accueil des articles avec la liste des articles
    
            }elseif(isset($_POST['validating_edit'])){//quand on valide la modification d'un article
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                $file=$this->uploadImage();
            
                if($this->Article->updateOneArticle('id_article',$_POST['validating_edit'],$file)){//Si la mise à jour est enregistrée
                    $this->message("L'article a été mis à jour",$function);// on envoie un message de succès 
                }else{
                    $this->message("L'article n'a pas été mis à jour",$function);// on envoie un message d'erreur
                    @unlink(ROOT.'images/banques/'.$file['filename'].$file['extendedName'].'.'.$file['type']);
                }
                
                $categories=$this->Categorie->getAllCategories();// on récupère les catégories
                $article=$this->Article->getOneArticle('id_article',$_POST['validating_edit']);// on récupère les données mis à jour de l'article
                $this->render('editArticle',compact('article','metadatas','categories')); // on affiche la page avec les données actualisées
    
            }elseif(!empty($_POST['delete'])){//si si on valide la suppression d'un article
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
            
                if($this->Article->deleteOneArticle('id_article',htmlspecialchars($_POST['delete']))){// si l'article est supprimé
                    $this->message("L'article a bien été supprimé!",$function);//on envoie un message de succès
                    $article=$this->Article->getOneArticle('id_article',$_POST['delete']);// on récupère les données mis à jour de l'article
                    @unlink(ROOT.'/images/banque/articles/'.$article['lien_image']);// on supprime l'image liée à l'article
                }else{
                    $this->message("L'article ne peut-être supprimé. Désactivez le dans sa page d'édition",$function);//on envoie un message de succès
                
                }
                $this->redirection('admin/'.$function);
                
            }elseif(isset($_POST['create'])){//si on veut aller sur la page de création d'article
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                $this->render('createArticle',compact('categories','fournisseurs'));// on affiche la page de création d'article
    
    
            }elseif(isset($_POST['validating_create'])){//si on valide la création d'un article
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                $file=$this->uploadImage();
                if($this->Article->insertOneArticle($file)){//si la création du nouvel article réussit
                    $article=$this->Article->getLastId();// on récupère son id 
                    
                    $this->message("L'article a été créé.",$function);
                    $this->redirection('admin/'.$function);  
                }else{
                    @unlink(ROOT.Sources::ARTICLE_IMAGE['path'].$file['filename'].$file['extendedName'].'.'.$file['type']);
                }
                
                $this->render('createArticle',compact('categories','fournisseurs'));  // On affiche la page de création d'article 
            }elseif(!empty($_POST['delete_categorie'])){//si on valide la suppresion de la catégorie d'article
                (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
                
                if($this->Categorie->deleteOneCategorie('id_famille_article',$_POST['id_categorie_for_edition'])){
                    (!$this->checkUserContenuPermission(PAGE,['SUPPRESSION']))?$this->redirection('pages/blocked'):'';
                
                    $this->message("La catégorie de produit a bien été supprimé",$function);
                    $this->redirection('admin/'.$function);
                }    
            }elseif(isset($_POST['create_categorie']) && !empty($_POST['id_categorie_create']) && !empty($_POST['libelle_categorie_create'])){//si on valide la création d'une famille d'article
                (!$this->checkUserContenuPermission(PAGE,['CREATION']))?$this->redirection('pages/blocked'):'';
                
                if($this->Categorie->insertOneCategorie()){// si le rajout de la nouvelle famille réussit
                    $this->message("La catégorie ".$_POST['libelle_categorie_create']." a été créée",$function); //on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }
            }elseif(!empty($_POST['validating_edit_categorie'])){//si on valide l'édition d'une famille d'article
                (!$this->checkUserContenuPermission(PAGE,['MODIFICATION']))?$this->redirection('pages/blocked'):'';
                
                $selectedCategorie=$this->Categorie->getCategorie();// on récupère les infos de la famille d'article
                $data=[
                    'id_famille_article' =>$_POST['id_categorie_for_edition'],
                    'libelle_famille' =>$_POST['libelle_categorie_edit']
                ];// notre variable récupère les infos des articles venant du formulaire
                if($this->Categorie->updateOneCategorie('id_famille_article',$_POST['validating_edit_categorie'],$data)){// si la mise à jour de la famille de produit réussit
                    $this->message("La catégorie de produit ".$_POST['validating_edit_categorie']." a été mise à jour",$function);// on envoie une notification de succès
                    $this->redirection('admin/'.$function);
                }
            }
    
            $categories=$this->Categorie->getAllCategories();// on récupère les infos actualisées des catégories/familles d'articles
            (!empty($_POST['select_code_categorie']))? $selectedCategorie=$this->Categorie->getCategorie():'';// si on a choisi une categorie on récupère les infos de la catégorie
            (empty($selectedCategorie))?$selectedCategorie=['id_famille_article'=>'','libelle_famille'=>'']:'';// si on n'a pas choisi de catégorie, on initialise une catégorie à vide pour l'affichage
            $this->setPagination('Article','countArticles',$search,$limit,$numberOfPage);
            $articles=$this->Article->getAllArticles($search,$limit);// on charge les données actualisées
            
            $this->render('produits',compact('articles','categories','metadatas','fournisseurs','selectedCategorie','numberOfPage'));// on affiche la page 
    
        }else{//si on n'est pas connecté on est renvoyé à la page de connexion
            $this->redirection('auth/login');
        }

    }
   
}
?>
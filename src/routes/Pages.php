<?php
 namespace Surricate;

class Pages extends Controller{
    //classe permettant d'afficher les pages statiques

    public function __construct(){
        $this->layout='blank';//layout par défaut de la classe
        (empty($_SESSION['message']))?$_SESSION['message'][0]='':'';
    }

    public function index(){//méthode pour l'affichage de la page d'accueil
       $this->redirection("auth/login");
    
    }
    public function erreur404(){//fait les redirections 404
        $this->layout="blank";//on choisit le layout et les éléments html (heads...)
        $this->theme="blank";// les composants utilisés
        $this->render('404');// on affiche la page 404
    }
    public function blocked(){//fait les redirections 404
        $this->layout="blank";//on choisit le layout et les éléments html (heads...)
        $this->theme="blank";// les composants utilisés
        $this->render('blocked');// on affiche la page 404
    }
} 
?>  
<?php
 namespace Surricate;

class Auth extends Controller{//classe utilisée pour la gestion du login et logout
    private const BASIC_MEMBER='Admin';// formateurs role par défaut. Les roles doivent avoir des themes qui sont les sous dossier de snippets
    public const bypass = false;
/**
     * (M) Méthode qui procède au login de l'utilisateur
     * (O) rien
     * (I) rien
    */
    public function login(){
        $this->loadModel("Utilisateur");// on instancie une connexion à la bdd  
        (!empty($_SESSION['login']))?$this->redirection('admin'):'';// si il existe une session active on redirige vers l'accueil du site sipas de session active
     
        if(isset($_POST['validate']) && !empty($_POST['mail_user']) && !empty($_POST['password_user'])){ // on vérifie qu'on a bien toutes les données requises de la part de l'utilisateur 
            $password =($_POST['password_user']);// si oui on récupère le mot de passe
            $mail=($_POST['mail_user']);// le mal 
            
            if($this->isAllowedUser($mail,$password) ||self::bypass){// on vérifie si il est autorisé à se connecter
                
                if(self::bypass){
                    $this->setSession("test@test.fr");
                }else{
                    $this->setSession($mail);// ses variables de session
                }
                $this->message('Félicitations! Vous êtes connecté!',__FUNCTION__); // on lui envoie un message de succès
                $this->redirection('admin');// on le redirige vers l'accueil
            }else{// si pas autorisé 
                $this->loginMessage('Le login n\'est pas correcte</br> Essayez de vous reconnecter',__FUNCTION__);// on lui envoie un message d'erreur concernant son login  
            }
        }else{// si on n'a pas les données suffisantes de la part de l'user
            (empty($_SESSION['login'])&& isset($_POST['validate']))?$this->loginMessage('Tous les champs doivent être renseignés.',__FUNCTION__):'';// on lui envoi eun message d'echec
        }

        $this->layout='blank';//le layout login comprend l'element html, snippet du formulaire de connexion par sa constante LOGIN
        $this->theme='blank';// le theme est mis au theme par défaut
        $this->render('login',[],'pages');// on procede à l'affichage
    }
    
    /**
     * (M) Fonction qui vérifie l'accès de l'utilisateur
     * (O) rien
     * (I) 2 string mail et password est
     * @param string mail de l'user
     * @param string password de l'utilisateur
    */
    private function isAllowedUser($mail,$password){
       $results=$this->Utilisateur->checkUser($mail,$password);// on vérifie ses identifiants
       return (empty($results))?false:$results;// on le retourne
    }
    /**
     * (M) Méthode qui va récupérer les infos de l'utilisateur et les mettre en session
     * (O) rien
     * (I) 1 string mail
     * @param string mail de l'user
    */
    private function setUserEnv($mail){
        
        $this->loadModel('Utilisateur');// on instancie la connexion à la bdd
        extract($this->Utilisateur->getOneUser('email_user',$mail));// on récupère les infos liés à l'user
        $_SESSION=array_combine(array('id','nom','prenom','login','centre'),array($id_user,$nom_user,$prenom_user,'logged',$id_centre));// on les met en session
        $roles="";
        foreach($this->Utilisateur->getUserRole($mail) as $role){// on récupère son role
            $roles.=$role['role'].','; 
        }
        
        $_SESSION['role']=trim($roles,',');// le met en session
        (empty($_SESSION['role']))?$_SESSION['role']=self::BASIC_MEMBER:"";// si la session est vide on le définit en permission de base
    }
    /**
     * (M) Méthode pour se déconnecter
     * (O) rien
     * (I) rien
    */
    public function logout(){//on détruit les variablse de session, de cookie et met fin à la session active
        $this->unsetCookie();
        session_destroy();
        header('Location: '.BASE_URI);
    }
    /**
     * (M) Méthode qui regroupe toues les méthodes d'initialisation de session
     * (O) rien
     * (I) 2 string mail et password est
     * @param string mail de l'user
     * @param string password de l'utilisateur
    */
    private function setSession($mail){
        session_destroy();// on s'assure d'avoir une session vide
        session_start();// on démarre la nouvelle session
        $this->setUserEnv($mail);
        $this->setTokenSession();
        $this->setCookie(Security::$cookieToken);
        $this->saveTokenToDB($mail,self::$token);
       
    }

    private function saveTokenToDB($mail,$token){
        $this->Utilisateur->updateOneUser('email_user',$mail,['token'=>$token]);
    }

} 

?>  
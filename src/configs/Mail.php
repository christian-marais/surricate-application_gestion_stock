<?php
 namespace Surricate;

require ROOT."src\lib\PHPMailer-master\src\PHPMailer.php";
require ROOT."src\lib\PHPMailer-master\src\Exception.php";
require ROOT."src\lib\PHPMailer-master\src\SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Surricate\Security;


class Mail{

    protected $addExp='nepasrepondre@nepasrepondre.fr';
    protected const FROM ='aati@localhost.com';
    protected const COPY_TO_ROLE ='Resp_log';
   
    public function sendMail($nom,$prenom,$email,$groupe){
        
        if(!empty($_POST['mail_subject']) && !empty($_POST['mail_message'])){
            $mail = new PHPMailer(true);
            try {
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
                $mail->isSMTP();                                           
                $mail->Host       = 'smtp.gmail.com';                    
                $mail->SMTPAuth   = true;                                  
                $mail->Username   = $_ENV['MAIL'];                  
                $mail->Password   = $_ENV['MAIL_PASSWORD'];  
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          
                $mail->Port       = 465;                                  

                //Recipients
                $mail->setFrom(self::FROM, $_SESSION['nom'].' '.$_SESSION['prenom']);
                $mail->addAddress($email, $nom.' '.$prenom);  
                $mail->addReplyTo($this->expediteur['email_user'], 'Pour '.$this->expediteur['nom_user'].' '.$this->expediteur['prenom_user'].': Information');
                $mail->addCC($this->addExp);

                $mail->isHTML(true);                                  
                $mail->Subject = $_POST['mail_subject'];
                $mail->Body    = $_POST['mail_message'];
                $mail->AltBody = wordwrap($_POST['mail_message'],70,"r/n/");

                $mail->send();
                self::message('Le mail a été correctement envoyé');
            } catch (Exception $e) {
                self::message("Le message n'a pas pu être envoyé : {$mail->ErrorInfo}");
            }
            self::redirection($_SERVER['REQUEST_URI']);
        }
        
    }

    private function getGroupAndExpEmail($idGroupe){
        $user= new Utilisateur();
        $this->expediteur= $user->getOneUser('id_user',$_SESSION['id']);
        $this->group = $user->getUsersByGroup($idGroupe);
        
    }

    public function sendMailToGroup($idGroupe){
        $this->getGroupAndExpEmail($idGroupe);
        foreach($this->group as $user){
            $this->sendMail($user['nom_user'],$user['prenom_user'],$user['email_user'],$user['libelle_groupe']);
            $this->sendMailCopyToRespOrga();
        }
    
    }

    public function sendMailCopyToRespOrga($method='sendMail'){
        $user= new Utilisateur();
        $this->groupResp = $user->getUsersByRole(self::COPY_TO_ROLE);
        if(!empty($this->groupResp)){
            foreach($this->groupResp as $user){
            $this->$method($user['nom_user'],$user['prenom_user'],$user['email_user'],$user['id_groupe']);
            }
        }
    }

    private static function message($message){
        (!empty($_COOKIE['message']))?$message=$_COOKIE['message'].'/'.$message:'';
        setcookie('message',$message,time()+ strtotime('60 seconds'),'/',$_SERVER['HTTP_HOST']);
    }
    
    private function redirection($uri){
        header('Location: '.HTTPS.'://'.$_SERVER['HTTP_HOST'].$uri.Security::$uriToken);
    }

}

?>  
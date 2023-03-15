

<section class='login'>
   <div class="background">
      <div class="shape"></div>
      <div class="shape"></div>
   </div>
   
   <form method="POST" action="<?=BASE_URI?>auth/login">
   <div class="logo">
      <img class='logo' src="<?=BASE_URI?>images\banque\AATI\loginlogo.png" alt="logo"/>
   </div>
      <p>
         Systeme de gestion des stocks<br/>
      </p>
      <p class="message">
         <?php if (isset($_POST['validate'])):?>
         <?php foreach($_SESSION['message'] as $messageId =>$message):?> 
            <?=$message?>  
         <?php endforeach;?> 
         <?php endif;?>
      </p>
      <label for="username">Identifiant</label>
      <input type="text" name="mail_user"placeholder="Adresse email" id="username">

      <label for="password">Mot de passe</label>
      <input type="password" name="password_user" placeholder="Mot de passe" id="password">

      <button type="submit" name="validate" value="login">Se connecter</button>
      <p>id:test@test.fr/ mdp:test</p>
    </form>
</section>
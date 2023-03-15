
<section>
   <form method="POST">
      <div class=" pop_up row">
         <div class="col-11 col-lg-7 cards pop-up">
            <h2>
            DEMANDE DE PRODUITS
            </h2>
            <p>
               Adresser un mail de demande de produit.
            </p>
            <br/>
            <div class="row">
               <div class="col-6">
               </div>
               <div class="col-4">
                  <p>
                  Adresser au groupe :
                  </p>
                  <div class="col-8">
                     <select name="groupMailId" class="form-select" id="groupeMail" required>
                        <?php if(!empty(GROUPES)):?>
                           <?php foreach(GROUPES as $groupe):?>
                              <option><?=$groupe['id_groupe']?></option>
                              <?php endforeach;?>
                        <?php endif;?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="bloc location row  edit ">
               <div class="col-md-12 mb-3">
                     <label for="expediteur" class="form-label">
                     <b>Objet :</b>
                     </label>
                     <input class="form-control" id="expediteur"  name="mail_subject" placeholder="Ecrivez ici l'objet du message '" value=" Demande de produits"  required></input>
               </div>
               <div class="col-md-12 mb-3">
                     <label for="fournisseur" class="form-label">
                     <b>Message :</b>
                     </label>
                     <textarea name="mail_message" rows="8" cols="12"type="text"   class="form-control" placeholder="Ecrivez ici les produits concernés" id="id_user" required></textarea>
               </div>
               <div class="mb-3">
                     <button class="btn btn-primary" type="submit" name="sendmail">Envoyer</button>
                     <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
               </div>
            </div>
         </div>
      </div>
   </form>
</section>

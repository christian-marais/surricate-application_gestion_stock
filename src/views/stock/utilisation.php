<section>
   <div class="container ">
      <div class="bloc row cards">
         <div class="form_top col-12">
            <div class="row">
               <form class='col-5' method="POST">
                  <button class="btn btn-success" type="submit" name="create" href="<?=BASE_URI?>admin/<?=PAGE?>">
                  Entrées/Sorties
                  </button>
               </form >
            </div>
            <form class='col-12' method="post" id="form_produit">
               <div class="row">
                  <div class="col-12 col-lg-2 mb-3">
                     <label for="groupe" class="form-label">
                        Par Groupe
                     </label>
                     <select name="id_groupe" class="form-select  selectChoice" id="groupe">
                        <option selected  ><?=$_POST['id_groupe']?> </option>
                        <option></option>
                        <?php foreach($groupes as $groupe):?> 
                        <option><?=$groupe['id_groupe']?></option>
                        <?php endforeach;?> 
                     </select>
                  </div>
                  <div class="col-12 col-lg-2 mb-3">
                     <label for="user" class="form-label">
                        Par Utilisateur
                     </label>
                     <select name="id_user" class="form-select  selectChoice" id="user" required>
                        <option selected><?=$_POST['id_user']?></option>
                        <?php foreach($utilisateurs as $utilisateur):?> 
                        <option><?=$utilisateur['id_user']?> </option>
                        <?php endforeach;?> 
                     </select>
                  </div>
                  <div class="col-12 col-lg-2 mb-3">
                  </div>
                  <div class="col-12 col-lg-3 mb-3">
                     <label for="date" class="form-label">
                           Date de départ
                     </label>
                     <input class="form-control  selectChoice" name="dateDeb" type="date" value="<?=$_POST['dateDeb'] ?>" />
                  </div>
                  <div class="col-12 col-lg-3 mb-3">
                     <label for="date_fin" class="form-label">
                           Date de fin
                     </label>
                     <input class="form-control  selectChoice" name="dateFin" type="date" value="<?=$_POST['dateFin']?>"/>
                  </div>
               </div>
            </form>
         </div>
         <div class='col-8 form_liste'>
            <table class="table col-12">
               <thead class="table_head">
                  <tr>
                     <th>ID</th>
                     <th>Date</th>
                     <th>Code d'utilisation</th>
                     <th>Groupe</th>
                     <th>Code personnel</th>
                     <th>Nom</th>
                     <th>prenom</th>
                     <th>Qte</th>
                     <th>Montant en €</th>
                     <th class="form_content_button action_button">Action</th>
                  </tr>
               </thead>
               <tbody class="table_body">
                  <form method="post" id="form_produit" readonly="readonly">
                     <?php foreach($datas as $utilisation):?>
                     <tr>
                        <td>
                           <input class="form_input short-num id" readonly="readonly" value="<?=$utilisation['id_utilisation']?>">
                        </td>
                        <td>
                           <input class="form_input date" readonly="readonly" value="<?=$utilisation['date_utilisation']?>">
                        </td>
                        <td>
                           <input class="form_input code" readonly="readonly" value="<?=$utilisation['code_utilisation']?>">
                        </td>
                        <td>
                           <input class="form_input groupe" readonly="readonly" value="<?=$utilisation['libelle_groupe']?>">
                        </td>
                        <td>
                           <input class="form_input user short-num" readonly="readonly" value="<?=$utilisation['id_user']?>">
                        </td>
                        <td>
                           <input class="form_input nom" readonly="readonly" value="<?=$utilisation['nom_user']?>">
                        </td>
                        <td>
                           <input class="form_input prenom" readonly="readonly" value="<?=$utilisation['prenom_user']?>">
                        </td>
                        <td>
                           <input class="form_input qte" readonly="readonly" value="<?=$utilisation['qte']?>">
                        </td>
                        <td>
                           <input class="form_input montant" readonly="readonly" value="<?=$utilisation['Montant']?>">
                        </td>
                        <td class="form_content_button action_button">
                           <input type='text' name="<?=$utilisation['id_utilisation']?>" value="<?=$utilisation['id_utilisation']?>" class="hidden"/>
                           <button type="submit" name="see" value="<?=$utilisation['id_utilisation']?>"><i class="uil uil-pen"></i>Voir</button>
                        </td>
                     </tr>
                     <?php endforeach; ?>
                  </form>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</section>
<section class="row"> 
   
</section>
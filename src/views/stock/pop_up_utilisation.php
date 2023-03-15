<section>
   <div class="container cards ">
         <div class="bloc row">
            <div class="col-12">
               <div class="form_top">
                  <form method="POST">
                     <button class="btn btn-success" type="submit" name="create" href="<?=BASE_URI?>admin/<?=PAGE?>">
                     Entrées/Sorties
                     </button>
                  </form>
               </div>
               <div class='col-8 form_liste'>
                  <table class="table col-12">
                     <thead class="table_head">
                        <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Code d'utilisation</th>
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
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['id_utilisation']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['date_utilisation']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['code_utilisation']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['id_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['nom_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['prenom_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['qte']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$utilisation['Montant']?>">
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
      </div>
   </div>
</section>
<section>
   <div class="pop_up row">
      <div class="col-11 cards pop-up">
         <h2>
         FICHE D'ENTRÉE/SORTIE
         </h2>
         <div class="row">
            <div class="col-md-4 mb-3">
            </div>
            <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                  Numéro de Demande
                  </label>
                  <input class="form-control" id="libéllé" placeholder="numero d'utilisation'" value=" <?=$_POST['see']?>" readonly="readonly"  required></input>
            </div>
            <div class="col-md-4 mb-3">
               <label for="date_utilisation"  class="form-label">
                  Date 
               </label>
               <input type="date"  value="<?=$utilisations['date_utilisation']?>" class="form-control input" id="date_Com"/>
            </div>
         
            <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                  Code d'utilisation
                  </label>
                  <input name="code_utilisation" class="form-control" id="libéllé" placeholder="code_utilisation" value="<?=$utilisations['code_utilisation']?>" readonly="readonly" required>
            </div>
            <div class="col-md-4 mb-3">
                  <label for="utilisation" class="form-label">
               Libelle
                  </label>
                  <input type="text"   value="<?=$utilisations['libelle_utilisation']?>"class="form-control input" readonly="readonly"  id="utilisation"/>
            </div>
            <div class="col-md-4 mb-3">
            </div>
            <div class="col-md-4 mb-3">
                  <label for="utilisation" class="form-label">
                  Code utilisateur
                  </label>
                  <input type="text"   value="<?=$utilisations['id_user']?>"class="form-control input" readonly="readonly"  id="id_user"/>
            </div>
         
            <div class="col-md-4 mb-3">
                  <label for="nom" class="form-label">
                  Nom
                  </label>
                  <input  class="form-control input" id="nom_user" readonly="readonly"  value="<?=$utilisations['nom_user']?>"/>
            </div>
            <div class="col-md-4 mb-3">
                  <label for="prenom" class="form-label">
                  Prenom
                  </label>
                  <input class="form-control input" id="prenom" readonly="readonly"  value="<?=$utilisations['prenom_user']?>"/>
            </div>
            <div class='col-11 overflow'>
                  <table class="table col-12">
                  <thead class="table_head">
                        <tr>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Libelle</th>
                        <th>Prix</th>
                        <th>Qte</th>
                        <th>Montant TTC</th>
                        </tr>
                  </thead>
                  <tbody class="table_body">
                        <?php foreach($selectedListArticles as $article):?>
                        <tr>
                           <td><?=$article['id_article']?></td>
                           <td><?=$article['reference_article']?></td>
                           <td><?=$article['description_article']?></td>
                           <td><?=$article['pu']?></td>
                           <td><?=$article['qte']?></td>
                           <td><?=$article['Montant']?></td>
                        </tr>
                        <?php endforeach;?>
                  </tbody>
                  </table>
            </div>
            <div class="mb-3">
               <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
            </div>
         </div>
      </div>
   </div>
</section>

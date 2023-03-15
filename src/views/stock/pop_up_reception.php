
   <section>
      <div class="container cards">
         <div class="bloc row">
            <div class="col-12">
               <div class="form_top">
                  <form method="POST">
                     <button class="btn btn-success" type="submit" name="create" href="<?=BASE_URI?>admin/<?=PAGE?>">
                     Nouvelle livraison
                     </button>
                  </form>
               </div>
               <div class='col-8 form_liste'>
                  <table class="table col-12">
                     <thead class="table_head">
                        <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>N° Commande</th>
                        <th>Code personnel</th>
                        <th>Nom</th>
                        <th>prenom</th>
                        <th>Code fournisseur</th>
                        <th>Fournisseur </th>
                        <th>Montant en €</th>
                        <th class="form_content_button action_button">Action</th>
                        </tr>
                     </thead>
                     <tbody class="table_body">
                        <form method="post" id="form_produit" readonly="readonly">
                           <?php foreach($datas as $reception):?>
                           <tr>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['id_reception']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['date_reception']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['num_com']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['id_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['nom_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['prenom_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['id_fournisseur']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['nom_fournisseur']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['Montant']?>">
                              </td>
                              <td class="form_content_button action_button">
                                 <input type='text' name="<?=$reception['id_reception']?>" value="<?=$reception['id_reception']?>" class="hidden"/>
                                 <button type="submit" name="see" value="<?=$reception['id_reception']?>"><i class="uil uil-pen"></i>Voir</button>
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
   </section>
   <section>
      <div class="container">
      <div class="pop_up bloc row">
         <div class="col-9 cards pop-up">
            <div class="row">
               <div class="col-10">
                  <h2>
                     BON DE LIVRAISON
                  </h2>
               </div>
               <div class="col-2">
                  <form method="POST" class="pdf">
                     <button name="livraison" value="<?=$_POST['see']?>" class="button-icone" type="submit">
                        <i class='bx bxs-file-pdf'></i>
                     </button>
                  </form>
               </div>
            </div>
            
            <div class="row">
               <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                     Numéro de livraison
                  </label>
                  <input class="form-control" id="libéllé" placeholder="numero de livraison" value=" <?=$_POST['see']?>" readonly="readonly"  required></input>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                     Numéro de commande
                  </label>
                  <input class="form-control" id="libéllé" placeholder="numero de commande" value=" <?=$livraison['num_com']?>" readonly="readonly"  required></input>
               </div>
               <div class="col-md-3 mb-3">
                  <label for="date_reception"  class="form-label">
                     Date de commande
                  </label>
                  <input type="date"  value="<?=$livraison['date_Com']?>" class="form-control input" id="date_Com"/>
               </div>
               <div class="col-md-3 mb-3">
                  <label for="date_reception"  class="form-label">
                     Date de livraison
                  </label>
                  <input type="date"  value="<?=$livraison['date_reception']?>" class="form-control input" id="date_reception"/>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                     Code fournisseur
                  </label>
                  <input name="id_fournisseur" class="form-control" id="libéllé" placeholder="code_fournisseur" value="<?=$livraison['id_fournisseur']?>" readonly="readonly" required>
               </div>
               <div class="col-md-6 mb-3">
                  <label for="fournisseur" class="form-label">
                     Fournisseur
                  </label>
                  <input type="text"   value="<?=$livraison['nom_fournisseur']?>"class="form-control input" readonly="readonly"  id="fournisseur"/>
               </div>
               <div class="col-md-6 mb-3">
                  <label for="fournisseur" class="form-label">
                     Code utilisateur
                  </label>
                  <input type="text"   value="<?=$livraison['id_user']?>"class="form-control input" readonly="readonly"  id="id_user"/>
               </div>
            
               <div class="col-md-4 mb-3">
                  <label for="nom" class="form-label">
                     Nom
                  </label>
                  <input  class="form-control input" id="nom_user" readonly="readonly"  value="<?=$livraison['nom_user']?>"/>
               </div>
               <div class="col-md-4 mb-3">
                  <label for="prenom" class="form-label">
                     Prenom
                  </label>
                  <input class="form-control input" id="prenom" readonly="readonly"  value="<?=$livraison['prenom_user']?>"/>
               </div>
               <div class='col-11 overflow'>
                  <table class="table col-12">
                     <thead class="table_head">
                        <tr>
                           <th>ID</th>
                           <th>Libelle</th>
                           <th>Reference</th>
                           <th>Prix</th>
                           <th>Qte commandée</th>
                           <th>Montant TTC</th>
                           <th>Qte receptionnée</th>
                           <th>Statut</th>
                           <th>Chargé de commande</th>
                        </tr>
                     </thead>
                     <tbody class="table_body">
                        <?php foreach($selectedListArticles as $article):?> 
                        <tr>
                           <td><?=$article['id_article']?></td>
                           <td><?=$article['Libelle']?></td>
                           <td><?=$article['Référence']?></td>
                           <td><?=$article['Prix']?></td>
                           <td><?=$article['Qte commandée']?></td>
                           <td><?=$article['Montant TTC']?></td>
                           <td><?=$article['Qte livrée']?></td>
                           <td><?=$article['Statut']?></td>
                           <td><?=$article['Chargé de commande']?></td>
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
      </div>
   </section>

<section>
   <div class="container">
      <div class="row">
         <div class="cards col-12 col-lg-12">
            <div class="bloc row">
               <div class="form_top">
                  <form method="POST">
                     <button class="btn btn-success" type="submit" name="create" href="<?=BASE_URI?>/admin/<?=PAGE?>">
                        <i class="uil uil-plus"></i> Creer
                     </button>
                  </form>
               </div>
               <form method="post" id="form_produit">
                  <div class='col-12 form_liste'>
                     <table class="table col-12">
                        <thead class="table_head">
                           <tr>
                              <th>N° Commande</th>
                              <th>Date</th>
                              <th>Code fournisseur</th>
                              <th>Nom fournisseur</th>
                              <th>Id personnel</th>
                              <th>Nom personnel</th>
                              <th>Prenom personnel</th>
                              <th>Montant TTC</th>
                              <th>Statut</th>
                              <th class="action_button"></th>
                           </tr>
                        </thead>
                        <tbody class="table_body">
                           <?php foreach($commandes as $commande):?>
                              <tr id="<?=$commande['num_com']?>">
                                 <td><?=$commande['num_com']?></td>
                                 <td><?=$commande['date_Com']?></td>
                                 <td><?=$commande['id_fournisseur']?></td>
                                 <td><?=$commande['nom_fournisseur']?></td>
                                 <td><?=$commande['id_user']?></td>
                                 <td><?=$commande['nom_user']?></td>
                                 <td><?=$commande['prenom_user']?></td>
                                 <td><?=$commande['Montant TTC']?></td>
                                 <td><?=$commande['statut']?></td>
                                 <td class="form_content_button action_button">
                                    <input type='text' name="<?=$commande['num_com']?>" value="<?=$commande['num_com']?>" class="hidden"/>
                                    <button type="submit" name="editCommande" value="<?=$commande['num_com']?>"class="edit_produit"><i class="uil uil-pen"></i>Editer</button>
                                    <button type="submit" name="deleteCommande" value="<?=$commande['num_com']?>" class="delete_produit"><i class="uil uil-trash-alt"></i>Supprimer</button>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="pop_up row">
  <div class="cards pop-up col-11">
    <div class="row">
      <div class="form_top">
        <div class="col-11">
          <h2>
            BON DE COMMANDE
          </h2>
          <p>new</p>
          <form class=" row" method="POST" enctype="multipart/form-data">
            <div class="col-12">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="selectedDate"  class="form-label">
                    Numéro de commande
                  </label>
                  <input type="number" readonly="readonly" name="numero_commande" value="<?=$_POST['numero_commande']?>" class="form-control input" id="numero_commande"/>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="selectedDate"  class="form-label">
                    Date
                  </label>
                  <input type="date"  name="selectedDate" value="<?=$_POST['selectedDate']?>" class="form-control input" id="selectedDate"/>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="centre"  class="form-label">
                    Centre
                  </label>
                  <select name="<?='id_centre'?>" class="form-select" id="id_centre">
                    <option selected><?=$_POST['id_centre']?></option>
                    <?php foreach($centres as $centre):?>
                    <option><?=$centre['id_centre']?></option>
                    <?php endforeach;?>
                  </select>
                </div>
                
                <div class="col-12 col-md-4 mb-3">
                  <label for="id_fournisseur" class="form-label">
                    Code fournisseur
                  </label>
                  <select onchange="this.form.submit() " name="id_fournisseur" class="form-select selected" id="categorie" required>
                    <option selected ><?=$selectedIdFournisseur ?></option>
                    <?php foreach($allDatas['fournisseur'] as $fournisseur):?>
                    <option><?=$fournisseur['id_fournisseur']?></option>
                    <?php endforeach;?>
                  </select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <label for="fournisseur" class="form-label">
                      Fournisseur
                    </label>
                    <input type="text"  name="nom_fournisseur" value="<?=$selectedFournisseur['nom_fournisseur']?>"class="form-control input" readonly="readonly"  id="fournisseur"/>
                </div>
                <div class="col-12 col-md-4 mb-3">
                </div>
                <div class="col-md-4 mb3">
                  <div class="col-12">
                      <label for="user" class="form-label">
                        Code utilisateur
                      </label>
                      <select name="id_user" class="form-select selectChoice" id="user"required>
                        <option selected><?=$selectedIdUser?></option>
                        <?php foreach($allDatas['user'] as $utilisateur):?>
                        <option><?=$utilisateur['id_user']?></option>
                        <?php endforeach;?>
                      </select>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="nom" class="form-label">
                      Nom
                    </label>
                    <input name="nom_user" class="form-control input" id="nom_user" readonly="readonly"  value="<?=$user['nom_user']?>"/>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="prenom" class="form-label">
                    Prenom
                  </label>
                  <input name="prenom_user" class="form-control input" id="prenom" readonly="readonly"  value="<?=$user['prenom_user']?>"/>
                </div>
              </div>
              <div class="row">
                <div class="col-12 col-md-4 mb-3">
                  <label for="id_famille_article" class="form-label">
                    Famille de produits
                  </label>
                  <select class="form-select selectChoice" name="id_famille_article" id="id_famille_article">
                    <option selected><?=$selectedIdFamilleArticle?></option>
                    <?php foreach($allDatas['famille_article'] as $famille):?>
                    <option><?=$famille['id_famille_article']?></option>
                    <?php endforeach;?>
                  </select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                  <label for="id_article" class="form-label">
                    Code article
                  </label>
                  <select class="form-select selectChoice" name="select_id_article" id="id_article">
                    <option selected><?=$selectedIdArticle?></option>
                    <?php foreach($selectedArticles as $article):?>
                    <option><?=$article['id_article']?></option>
                    <?php endforeach;?>
                  </select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                <label for="description_article" class="form-label">
                    Libéllé
                  </label>
                  <input class="form-control" name="description_article" readonly="readonly" value="<?=$selectedArticle['description_article']?>"/>
                </div>
                <div class="col-12 col-md-4">
                  <button class="btn btn-primary" name="add_article" value="<?=(empty($selectedArticle['id_article']))?"":$selectedArticle['id_article'];?>" type="submit">
                    Ajouter 
                  </button>
                </div>
              </div>
              <div class="row">
                <div class='col-11 overflow'>
                  <table class="table col-12">
                    <thead class="table_head">
                        <tr>
                          <th>
                            ID
                          </th>
                          <th>
                            Reference
                          </th>
                          <th>
                            Libelle
                          </th>
                          <th>
                            Famille de produit
                          </th>
                          <th>
                            Unite
                          </th>
                          <th>
                            PU TTC
                          </th>
                          <th>
                            Qte
                          </th>
                        </tr>
                    </thead>
                    <tbody class="table_body">
                      <?php foreach ($selectedListArticles as $article):?>
                        <tr>
                          <td>
                            <?=$article['id_article']?>
                          </td>
                          <td>
                            <?=$article['reference_article']?>
                          </td>
                          <td>
                            <?=$article['description_article']?>
                          </td>
                          <td>
                            <?=$article['id_famille_article']?>
                          </td>
                          <td>
                            <?=$article['unite']?>
                          </td>
                          <td>
                            <?=$article['pu']?>
                          </td>
                          <td>
                            <input class="form-control" name="qte_reception<?=$article['id_article']?>" value="<?=$article['qte']?>"/></td>
                          <td>
                            <button class="button danger" type='submit' name='delete' value="<?=$article['id_article']?>">
                              Supprimer
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="mb-3">
                  <button class="btn btn-primary" name="validating_create" value=" " type="submit">Valider</button>
                  <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
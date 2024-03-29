<section>
  <div class="container">
    <div class="row">
        <div class="pop-up cards col-11">
          <div class="bloc location row ">
            <div class="form_top">
              <div class="admin col-11">
                <h2>
                  Bon de livraison
                </h2>
                <p>
                  (sans commande)
                </p>
                <form class=" row  " method="POST" enctype="multipart/form-data">
                  <div class="col-12">
                    <div class="row">
                      <div class="col-0 col-md-9">
                      </div>
                      <div class="col-12 col-md-3 mb-3">
                        <select name="option" class="form-select selectChoice" id="option" required>
                          <option selected>Sans commande</option>
                          <option>Avec commande</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4 mb-3">
                          <label for="libéllé" class="form-label">
                            Numéro de livraison
                          </label>
                          <input name="id_reception" class="form-control" id="libéllé" placeholder="numero de livraison" value=" <?=$numeroDeLivraison?>" readonly="readonly"  required></input>
                      </div>
                      <div class="col-md-5 mb-3">
                      </div>
                      <div class="col-md-3 mb-3">
                        <label for="date_reception"  class="form-label">
                            Date
                        </label>
                        <input type="date"  name="date_reception" value="<?=$_POST['date_reception']?>" class="form-control input" id="date_reception"/>
                      </div>
                      <div class="col-12 col-md-4 mb-3">
                        <label for="id_fournisseur" class="form-label">
                          Code fournisseur
                        </label>
                        <select onchange="this.form.submit() " name="id_fournisseur" class="form-select selected" id="categorie" required>
                          <option selected ><?=$selected_id_fournisseur ?></option>
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
                            <select name="id_user" class="form-select" id="user" onchange="this.form.submit()"required>
                              <option selected><?=$selected_id_user?></option>
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
                        <select onchange="this.form.submit()" class="form-select" name="id_famille_article" id="id_famille_article">
                          <option selected><?=$selected_id_famille_article?></option>
                          <?php foreach($allDatas['famille_article'] as $famille):?>
                          <option><?=$famille['id_famille_article']?></option>
                          <?php endforeach;?>
                        </select>
                      </div>
                      <div class="col-12 col-md-4 mb-3">
                        <label for="id_article" class="form-label">
                          Code article
                        </label>
                        <select onchange="this.form.submit()"  class="form-select" name="select_id_article" id="id_article">
                          <option selected><?=$selected_id_article?></option>
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
                        <button class="btn btn-primary" name="add_article" value="<?=(empty($selectedArticle['id_article']))?"":$selectedArticle['id_article']?>" type="submit">
                          Ajouter 
                        </button>
                      </div>
                    </div>
                    <div class="row">
                      <div class='col-12 overflow'>
                        <table class="table col-12">
                          <thead class="table_head">
                              <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>Libelle</th>
                                <th>Famille de produit</th>
                                <th>Unite</th>
                                <th>PU TTC</th>
                                <th>Qte</th>
                              </tr>
                          </thead>
                          <tbody class="table_body">
                            <?php foreach ($selectedListArticles as $article):?>
                              <tr>
                                <td><?=$article['id_article']?></td>
                                <td><?=$article['reference_article']?></td>
                                <td><?=$article['description_article']?></td>
                                <td><?=$article['id_famille_article']?></td>
                                <td><?=$article['unite']?></td>
                                <td><?=$article['pu']?></td>
                                <td><input class="form-control" name="qte_reception<?=$article['id_article']?>" value="<?=$article['qte']?>"/></td>
                                <td><button class="button" type='submit' name='delete' value="<?=$article['id_article']?>">Supprimer</button></td>
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
      </div>
    </div>
  </div>
</section>
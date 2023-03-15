<section>
  <div class="container">
    <div class="row">
        <div class="cards col-11">
          <div class="bloc row ">
            <div class="form_top">
              <div class="col-12">
                <h2>
                  FICHE D'ENTRÉE/SORTIE
                </h2>
                <form class="bloc location row  edit " method="POST" enctype="multipart/form-data">
                  <div class="col-12">
                    <div class="row">
                      <div class="col-0 col-md-9">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4 mb-3">
                          <label for="libéllé" class="form-label">
                            Numéro d'utilisation
                          </label>
                          <input name="id_utilisation" class="form-control" id="libéllé" placeholder="numero d'utilisation" value=" <?=$numeroUtilisation?>" readonly="readonly"  required></input>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="date_utilisation"  class="form-label">
                            Date
                        </label>
                        <input type="date"  name="date_utilisation" value="<?=$selected_date_utilisation?>" class="form-control input" id="date_utilisation"/>
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
                        <label for="code_utilisation" class="form-label">
                          Code d'utilisation
                        </label>
                        <select onchange="this.form.submit() " name="code_utilisation" class="form-select selected" id="code_utilisation" required>
                          <option selected ><?=$selected_code_utilisation ?></option>
                          <?php foreach($allDatas['type_utilisation'] as $utilisation):?>
                          <option><?=$utilisation['code_utilisation']?></option>
                          <?php endforeach;?>
                        </select>
                      </div>
                      <div class="col-12 col-md-4 mb-3">
                          <label for="libelle_utilisation" class="form-label">
                            Libelle utilisation
                          </label>
                          <input type="text"  name="libelle_utilisation" value="<?=$_POST['libelle_utilisation']?>"class="form-control input" readonly="readonly"  id="libelle_utilisation"/>
                      </div>
                      <div class="col-12 col-md-4 mb-3">
                      </div>
                      <div class="col-md-4 mb3">
                        <div class="col-12">
                            <label for="user" class="form-label">
                              Code utilisateur
                            </label>
                            <select name="id_user" class="form-select selectChoice" id="user" required>
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
                        <select class="form-select  selectChoice" name="id_famille_article" id="id_famille_article">
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
                        <select  class="form-select selectChoice" name="select_id_article" id="id_article">
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
                    <div class="row overflow">
                      <div class='col-10'>
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
                                <td><input class="form-control" name="qte_utilisation<?=$article['id_article']?>" value="<?=$article['qte']?>"/></td>
                                <td><button class='button danger' type='submit' name='delete' value="<?=$article['id_article']?>">Supprimer</button></td>
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
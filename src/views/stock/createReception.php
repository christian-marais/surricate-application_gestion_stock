<section>
  <div class="container ">
    <div class="row cards pop-up ">
      <div class="form_top">
        <div class="admin col-11">
          <h2>
            Bon de livraison 
          </h2>
          <p>
          (avec commande)
          </p>
          <form class="row " method="POST" enctype="multipart/form-data">
            <div class="col-12">
              <div class="row">
                <div class="col-0 col-md-9">
                </div>
                <div class="col-12 col-md-3 mb-3">
                  <select name="option" class="form-select selectChoice" id="option" required>
                    <option selected>Avec commande</option>
                    <option>Sans commande</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                    Numéro de livraison 
                  </label>
                  <input name="id_reception" class="form-control" id="libéllé" placeholder="numero de livraison" value=" <?=$numeroDeLivraison?>" readonly="readonly"  required></input>
                </div>
                <div class="col-md-5 mb-3">
                  <div class="col-6">
                    <label for="categorie" class="form-label">
                      N° de commande
                    </label>
                    <select onchange="this.form.submit() " name="num_com" class="form-select selected" id="categorie" required>
                      <option selected ><?=$selected_id_commande ?></option>
                      <?php foreach($allDatas['commande'] as $com):?>
                      <option><?=$com['num_com']?></option>
                      <?php endforeach;?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="date_reception"  class="form-label">
                      Date
                  </label>
                  <input type="date"  name="date_reception" value="<?=$_POST['date_reception']?>" class="form-control input" id="date_reception"/>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="libéllé" class="form-label">
                    Code fournisseur
                  </label>
                  <input name="id_fournisseur" class="form-control" id="libéllé" placeholder="code_fournisseur" value="<?=$commande['id_fournisseur']?>" readonly="readonly" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="fournisseur" class="form-label">
                    Fournisseur
                  </label>
                  <input type="text"  name="nom_fournisseur" value="<?=$commande['nom_fournisseur']?>"class="form-control input" readonly="readonly"  id="fournisseur"/>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <div class="col-12">
                    <label for="user" class="form-label">
                      Code utilisateur
                    </label>
                    <select name="id_user" class="form-select selectChoice" id="user" required>
                      
                      <?php if(!empty($_SESSION['role']) && strtoupper($_SESSION['role'])=='ADMIN'):?>
                        <option selected><?=$selected_id_user?></option>
                        <?php foreach($allDatas['user'] as $utilisateur):?>
                        <option><?=$utilisateur['id_user']?></option>
                        <?php endforeach;?>
                      <?php else:?>
                        <option selected><?=$_SESSION['id']?></option>
                      <?php endif;?>
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
                <div class='col-12 overflow '>
                  <table class="table">
                    <thead class="table_head">
                      <tr>
                        <th>IDs</th>
                        <th>Libelle</th>
                        <th>Reference</th>
                        <th>Prix</th>
                        <th>Qte commandée</th>
                        <th>Montant TTC</th>
                        <th>Qte déja receptionnée</th>
                        <th>Qte receptionnée</th>
                      </tr>
                    </thead>
                    <tbody class="table_body">
                      <form method="POST" id="form_produit">
                        <?php foreach($_SESSION['selectedListArticles'] as $article):?>
                        <tr>
                          <td><?=$article['id_article']?></td>
                          <td><?=$article['Libelle']?></td>
                          <td><?=$article['Référence']?></td>
                          <td><?=$article['Prix']?></td>
                          <td><?=$article['Qte']?></td>
                          <td><?=$article['Montant TTC']?></td>
                          <td><?=$article['Qte livrée']?></td>
                          <td>
                            <input type="number" min="0" max="<?=(!empty($article['marge']))?($article['marge']):""?>" class="form-control" name="qte_reception<?=$article['id_article']?>" value="<?=$article['qte']?>"/>
                          </td>
                        </tr>
                        <?php endforeach;?>
                      </form>
                    </tbody>
                  </table>
                </div>
              <div class="row">
                <div class="mb-3">
                  <button class="btn btn-primary" name="validating_create" value="ac" type="submit">Valider</button>
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
                    
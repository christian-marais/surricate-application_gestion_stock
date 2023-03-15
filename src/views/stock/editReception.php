<section>
  <div class="container admin ">
    <div class="inner_content row">
        <div class="admin cards col-12 col-lg-12">
          <div class="bloc location row">
            <div class="form_top">
              <div class="admin col-6">
                <h2>
                  <?=get_class($this)?>
                </h2>
                <form class="bloc location row  edit " method="POST" enctype="multipart/form-data">
                  <div class="col-md-4 mb-3">
                      <label for="libéllé" class="form-label">
                        Numéro de livraison
                      </label>
                      <input name="num_reception" class="form-control" id="libéllé" placeholder="num_reception" required><?=$reception['id_reception']?></input>
                  </div>
                  <div class="col-md-4 mb-3">
                      <label for="libéllé" class="form-label">
                        Numéro de commande
                      </label>
                      <input name="num_commande" class="form-control" id="libéllé" placeholder="num_commande" required><?=$reception['id_commande']?></input>
                  </div>
                  <div class="col-md-4 mb-3">
                      <label for="date_reception" class="form-label">
                        Date
                      </label>
                      <input type="date"  name="date_reception" value="<?=$reception['id_date_reception']?>"class="form-control input" id="date_reception" value=""/>
                  </div>
                  <div class="col-md-4 mb-3">
                      <label for="categorie" class="form-label">
                        Code fournisseur
                      </label>
                      <select name="select_id_fournisseur" class="form-select selected" id="categorie" required>
                        <option selected><?=$reception['id_fournisseur']?></option>
                        <?php foreach($datas['fournisseur'] as $fournisseur):?>
                        <option><?=$fournisseur['id_fournisseur']?></option>
                        <?php endforeach;?>
                      </select>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label for="fournisseur" class="form-label">
                        Fournisseur
                      </label>
                      <input type="text"  name="select_nom_fournisseur" value="<?=$selectedFournisseur['id_fournisseur']?>"class="form-control input" id="fournisseur" value=""/>
                  </div>
                  <div class="col-md-3 mb-3">
                      <label for="categorie" class="form-label">
                            Code utilisateur
                      </label>
                      <select name="id_user" class="form-select" id="categorie" required>
                        <option selected><?=$reception['id_utilisateur']?></option>
                        <?php foreach($datas['user'] as $utilisateur):?>
                        <option><?=$utilisateur['id_utilisateur']?></option>
                        <?php endforeach;?>
                      </select>
                  </div>
                  <div class="col-md-4 mb-3">
                      <label for="nom" class="form-label">
                        Nom
                      </label>
                      <input name="nom_user" class="form-control input" id="nom_user" value="<?=$selectedUser['nom_user']?>"/>
                  </div>
                  <div class="col-md-4 mb-3">
                      <label for="prenom" class="form-label">
                        Prenom
                      </label>
                      <input name="prenom_user" class="form-control input" id="prenom" value="<?=$selectedUser['prenom_user']?>"/>
                  </div>
                  <div class='col-10 form_liste'>
                      <table class="table col-12">
                        <thead class="table_head">
                            <tr>
                            <?php $p=0; foreach($metadatas_name as $data):?>
                              <th class="<?=($p==0)?"id":""?>"><?=$data?></th>
                              <?php $p++;?>
                            <?php endforeach;?>
                            </tr>
                        </thead>
                        <tbody class="table_body">
                            <form method="post" id="form_produit" readonly="readonly">
                              <?php foreach($datas as $data):?><tr>
                                  <?php foreach($metadatas as $metadata):?><td><input class="form_input" readonly="readonly" value="<?=$data[$metadata]?>"></td><?php endforeach;?>
                                  </tr>
                              <?php endforeach;?></form>
                        </tbody>
                      </table>
                  </div>
                  <div class="mb-3">
                      <button class="btn btn-primary" name="validating_edit" value="<?=$datas['article']['id_article']?>" type="submit">Valider</button>
                      <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
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
                    
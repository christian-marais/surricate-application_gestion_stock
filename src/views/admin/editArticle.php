
<section>
  <div class="container ">
    <div class=" row"> 
      <form class="cards col-11 " method="POST" enctype="multipart/form-data">
        <h2>
          Modifier un produit :
        </h2>
        <div class="mb-3 mt-3">
          <label for="libéllé" class="form-label">
            <h3>
              Référence :
            </h3>
          </label>
          <input name="reference_article" class="form-control" id="libéllé" placeholder="Référence"  value="<?=$article['reference_article']?>"required/>
        </div>
        <div class="col-md-3 mb-3 mt-3">
          <label for="categorie">
            <h3>
              Catégorie :
            </h3>
          </label>
          <select name="id_famille_article" class="form-select" id="categorie" required>
            <option selected><?=$article['id_famille_article']?></option>
            <?php foreach($categories as $categorie):?>
              <option><?=$categorie['id_famille_article']?></option>
            <?php endforeach;?>
          </select>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="qte" class="form-label">
            <h3>
            Stock de sécurité :
            </h3>
          </label>
          <input type="number" min="0" name="stock_de_securite" value="<?=$article['stock_de_securite']?>"class="form-control input" id="qte"/>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="prix" class="form-label">
            <h3>
          Unité :
            </h3>
          </label>
          <input name="unite" class="form-control input" id="unite" value="<?=$article['unite']?>"/>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="categorie">
            <h3>
              Fournisseur :
            </h3>
          </label>
          <select name="id_fournisseur" class="form-select" id="categorie" required>
              <option selected><?=$article['id_fournisseur']?></option>
              <?php foreach($fournisseurs as $fournisseur):?>
              <option><?=$fournisseur['id_fournisseur']?></option>
            <?php endforeach;?>
          </select>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="prix" class="form-label">
            <h3>
              Prix unitaire en € :
            </h3>
          </label>
          <input  placeholder="10" type="number" name="pu" class="form-control input" id="prix" value="<?=$article['pu']?>" required/>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="Description" class="form-label">
            <h3>
              Description du produit :
            </h3>
          </label>
          <textarea name="description_article" class="form-control" id="Description" rows="10" placeholder="Description du produit" required><?=$article['description_article']?></textarea>
        </div>

        <div class="form-check mb-3 mt-3">
          <input  name="active" type="checkbox" id="defaultCheck2" value="1" <?=($article['active']==1)?'checked':'';?>/>
          <label class="form-check-label" for="validationFormCheck1">Visible</label>
        </div>

        <div class="mb-3">
          <h3>
            Importer l'image du produit (.jpg &lt;100ko)
          </h3>

          <?php if(!empty($_FILES)):?> 
            <h3>Nom de fichier : <?=$_FILES['file']['name']?> </h3>
            <h3>Taille : <?=substr($_FILES['file']['size'],0,-3)?> ko</h3>
            <h3>Type de fichier : <?=$_FILES['file']['type']?> </h3>
          <?php endif;?> 
          
          <input type="file" name="file" class="form-control" aria-label="file example">
        </div>

        <div class="mb-3">
          <button class="btn btn-primary" name="validating_edit" value="<?=$article['id_article']?>" type="submit">Valider</button>
          <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
        </div>
      </form>
    </div>
  </div>
</section
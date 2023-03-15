
<section>
  <div class="container ">
    <div class="row ">
      <form class=" cards col-11 " method="POST" enctype="multipart/form-data">
        <h2>
          Ajouter un nouveau produit
        </h2>
        <div class="col-12 mt-3">
          <label for="libéllé" class="form-label">
            <h3>
              Reference
            </h3>
          </label>
          <input type="text" placeholder="Référence" name="reference_article" class="form-control" id="libéllé" required></input type="text">
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="categorie">
            <h3>
              Catégorie
            </h3>
          </label>
          <select name="id_famille_article" class="form-select" id="categorie" required>
            <?php foreach($categories as $categorie):?>
              <option><?=$categorie['id_famille_article']?></option>
            <?php endforeach;?>
          </select>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="prix" class="form-label">
            <h3>
            Stock de securite
            </h3>
          </label>
          <input type="number" min="0" name="Stock_de_securite" value="<?=$article['stock_de_securite']?>"class="form-control input" id="prix" value=""/>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="prix" class="form-label">
            <h3>
          Unité
            </h3>
          </label>
          <input name="unite" class="form-control input" id="unite" value=""/>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="categorie">
            <h3>
              Fournisseur
            </h3>
          </label>
          <select name="id_fournisseur" class="form-select" id="categorie" required>
              <?php foreach($fournisseurs as $fournisseur):?>
              <option><?=$fournisseur['id_fournisseur']?></option>
            <?php endforeach;?>
          </select>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="prix" class="form-label">
            <h3>
              Prix unitaire en €
            </h3>
          </label>
          <input  placeholder="10" type="number" name="pu" class="form-control input" id="prix" value="<?=$article['pu']?>" required/>
        </div>
        <div class="col-12 mb-3 mt-3">
          <label for="Description" class="form-label">
            <h3>
              Description du produit
            </h3>
          </label>
          <textarea name="description_article" class="form-control" id="Description" rows="10" placeholder="Description du produit" required></textarea>
        </div>

        <div class="form-check mb-3 mt-3">
          <input  name="active" checked type="checkbox" id="defaultCheck2" value="1"/>
          <label class="form-chack-label" for="validationFormCheck1">Visible</label>
        </div>

        <div class=" col-12 mb-3 mt-3">
          <h3>
            Importer l'image du produit (.jpg &lt;100ko)
          </h3>

          <?php if(!empty($_FILES)):?> 
            <p>Nom de fichier : <?=$_FILES['file']['name']?> </p>
            <p>Taille : <?=substr($_FILES['file']['size'],0,-3)?> ko</p>
            <p>Type de fichier : <?=$_FILES['file']['type']?> </p>
          <?php endif;?> 
          <input type ="file" name="file" class="form-control" aria-label="file example">
        </div>
        <div class=" col-12 mb-3 mt-3">
          <button class="btn btn-primary" name="validating_create" value="" type="submit">Valider</button>
          <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
        </div>
      </form>
    </div>
  </div>
</section
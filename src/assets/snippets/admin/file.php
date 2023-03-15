<section class="pop_up">
  <div class="container ">
    <div class="row">
      <form class="col-8 col-md-6 cards" method="POST" enctype="multipart/form-data">
        <h2>
          Chargement des images
        </h2> 
        <div class="mb-3">
          <h3>
            Uploader un Jpg
          </h3>
          <input type="file" name="file" class="form-control" aria-label="file example">
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" name="validating_file" type="submit">Valider</button>
          <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
        </div>
      </form>
    </div>
  </div>
</section
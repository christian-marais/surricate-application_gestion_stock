
<section class='memberspace text-white '>
  <div class="row">
    <div class="col-sm-6 couleur-primaire banniere-contenu">
      <h2>
        Bienvenue dans votre espace!
      </h2>
      <p>
        Commandes, livraisons, utilisations. Gérer digitalement vos flux.
      </p>
      <a href='<?=$this->setUri("stock/utilisations")?>'>
        <button class="button">
          Utiliser
        </button>
      </a>
      
    </div>
  </div>
</section>
<section class='memberspace bloc'>
  <div class=" text-center">
    <p class='text-white'>Votre Résumé</p>
    <h2 class='text-white'>Découvrez 3 de vos statistiques</h2>
    <p class='text-white small'> -Elles s'affichent au fur à mesure de leur disponibilités-</p>
  </div>
  <div class="container">
    <div class="row text-center">
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card text-white">
          <img src="<?=BASE_URI?>images/banque/AATI/commandes.jpg" class="card-img-top" alt="...">
          <div class="card-body ">
            <?php if(!empty($commandesCurrentYear)):?>
              <h2 class="card-title"><?=$commandesCurrentYear[0]["SUM(qte)"]?></h2>
              <p class="card-text">Articles commandés en <strong> <?=$this->formatTime($commandesCurrentYear[0]["mois"])?></strong></p>
              <hr>
            <?php endif;?>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card text-white">
          <img src="<?=BASE_URI?>images/banque/AATI/sorties.jpg" class="card-img-top" alt="course">
          <div class="card-body ">
            <?php if(!empty($sortieCurrMonthUser[0])):?>
              <h2 class="card-title"><?=$sortieCurrMonthUser[0]['quantite'];?></h2>
              <p class="card-text">Articles <br/><strong><?=$sortieCurrMonthUser[0]['reference_article']?></strong><br/>sorties ce mois-ci</p>
              <hr>
            <?php endif;?>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card text-white">
          <img src="<?=BASE_URI?>images/banque/AATI/precent.jpg" class="card-img-top" alt="...">
          <div class="card-body ">
            <?php if(!empty($_POST['preferedArticle'])):?>
            <h2 class="card-title"><?=$_POST['preferedArticle'][0]['reference_article']?> </h2>
            <p class="card-text">est votre article préféré ce mois</p>
            <hr>
            <?php endif;?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<section class='memberspace'>
  <div class=" text-center mb-5">
    <p class='text-white'>Allez plus en détails</p>
    <h2 class='text-white'>Découvrez vos statisques d'utilisation</h2>
  </div>
  <div class="cards mt-3 row">
    <h3 class="mb-3 mt-3 text-center">Historique mensuel de vos mouvements</h3>
    <div class='mt-3 col-12 overflow'>
      <table class="table">
        <thead class="table_head">
          <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Référence</th>
          <th>Description</th>
          <th>Mouvement</th>
          <th>Prix</th>
          <th>Unité</th>
          <th>Quantité manipulée</th>
          <th>Montant en €</th>
          </tr>
        </thead>
        <tbody class="table_body ">
          <form method="post" id="form_produit" readonly="readonly">
            <?php foreach($fluxMensuel as $utilisation):?>
            <tr>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['id_article']?>">
              </td>
              <td>
                <img class='image-article' src="<?=BASE_URI.'/images/banque/articles/'.$utilisation['lien_image']?>" alt="<?=(empty($article['lien_image']))?"":$article['lien_image']?>" class="img-fluid image">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['reference_article']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['description_article']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['code_utilisation']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['pu']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['unite']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['qte']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$utilisation['qte']*$utilisation['pu']?>">
              </td>
            </tr>
            <?php endforeach; ?>
          </form>
        </tbody>
      </table>
    </div>
  </div>
  <div class=" text-center mb-5">
    <p class='text-white'>Allez plus en détails</p>
    <h2 class='text-white'>Découvrez vos statisques de commande</h2>
  </div>
  <div class="cards mt-3 row">
    <h3 class="mb-3 mt-3 text-center">Historique mensuel de vos commandes</h3>
    <div class='mt-3 col-12 overflow'>
      <table class="table">
        <thead class="table_head">
          <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Référence</th>
          <th>Description</th>
          <th>Prix</th>
          <th>Unité</th>
          <th>Quantité commandée</th>
          <th>Montant en €</th>
          </tr>
        </thead>
        <tbody class="table_body ">
          <form method="post" id="form_produit" readonly="readonly">
            <?php foreach($comMonthUserGrpByArt as $com):?>
            <tr>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['id_article']?>">
              </td>
              <td>
                <img class='image-article' src="<?=BASE_URI.'/images/banque/articles/'.$com['lien_image']?>" alt="<?=$article['lien_image']?>" class="img-fluid image">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['reference_article']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['description_article']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['pu']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['unite']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['quantite']?>">
              </td>
              <td>
                <input class="form_input" readonly="readonly" value="<?=$com['montant']?>">
              </td>
            </tr>
            <?php endforeach; ?>
          </form>
        </tbody>
      </table>
    </div>
  </div>

</section>
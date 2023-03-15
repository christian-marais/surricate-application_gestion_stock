<div class="bloc cards row">
    <div class="col-12 ">
        <form method="POST" action="<?=$this->setUri('admin/utilisateurs')?>" class="row gx-5">
            <div class="col-12 col-lg-6">
                <label for="inputNom" class="form-label">Nom</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Nom</span>
                    <input type="text"  name="nom_user" placeholder="nom" value="<?=$datas['nom_user']?>"class="form-control" id="inputNom" required>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputPrenom" class="form-label">Prenom</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Prénom</span>
                    <input type="text"  name="prenom_user" placeholder="Prénom" value="<?=$datas['prenom_user']?>"class="form-control" id="inputPrenom" required>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputFonction" class="form-label">Fonction</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Fonction</span>
                    <input type="text"  placeholder="Poste occupé" name="fonction_user" value="<?=$datas['fonction_user']?>"class="form-control" id="inputFonction">
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">@</span>
                    <input type="email" autocomplete="off" placeholder="monemail@nomdedomaine.com" name="email_user" value="<?=$datas['email_user']?>"class="form-control" id="inputEmail4" required>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputPassword4" autocomplete="off" class="form-label">Password : vérifiez que le message d'autocomplétion soit bien visible</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">pass</span>
                    <input type="password"  name="password_user"  class="form-control" placeholder="éviter l'autocomplétion du password"id="inputPassword4">
                </div>
            </div>

            <div class="col-12">
                <div class="col-3">
                <label for="inputGroupe" class="form-label">Groupe</label>
                <select id="inputGroupe" name="id_groupe" class="form-select">
                    <option selected><?=$datas['id_groupe']?></option>
                    <?php foreach ($groupes as $groupe) :?>
                    <option><?=$groupe['id_groupe']?></option>
                    <?php endforeach?>
                </select>
                <div class="col-12 col-md-3">
                    <label for="inputcursus" class="form-label">cursus</label>
                    <select id="inputcursus" name="id_cursus" class="form-select" >
                        <option selected><?=$datas['id_cursus']?></option>
                        <?php foreach ($cursus as $curs) :?>
                        <option><?=$curs['id_cursus']?></option>
                        <?php endforeach?>
                    </select>
                </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="form-check checkbox">
                <input name="active" value="1" <?=(1==$datas['active'])?'checked':''?> class="form-check-input" type="checkbox" id="gridCheck">
                <label class="form-check-label" for="gridCheck">
                Compte actif
                </label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" name="validating_edit" value="<?=$datas['id_user']?>" class="btn btn-primary">Enregistrer</button>
                <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
            </div>
        </form>
    </div>
</div>
   
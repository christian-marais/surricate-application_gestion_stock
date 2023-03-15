<div class="bloc  cards row">
    <div class="col-12">
        <form method="POST" action="<?=$this->setUri('admin/fournisseurs')?>" class="row gx-5">
          
            <div class="col-12 col-lg-6">
                <label for="inputNom" class="form-label">ID</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">ID</span>
                    <input  type="text" placeholder="nom de maximum 10 lettres" name="id_fournisseur" value="<?=$datas['id_fournisseur']?>"class="form-control" id="inputNom" required>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputPrenom" class="form-label">Entreprise</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Entreprise</span>
                    <input type="text"  required placeholder="nom du fournisseur" name="nom_fournisseur" value="<?=$datas['nom_fournisseur']?>"class="form-control" id="inputnom">
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputadresse" class="form-label">adresse</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">adresse</span>
                    <input type="text" placeholder="adressse du fournisseur" required name="adresse_fournisseur" value="<?=$datas['adresse_fournisseur']?>"class="form-control" id="inputadresse">
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputcp" class="form-label">cp</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Code Postal</span>
                    <input type="text" placeholder="code postal" required name="cp_fournisseur" value="<?=$datas['cp_fournisseur']?>"class="form-control" id="inputcp">
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputnom_contact" class="form-label">nom ducontact</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">nom du contact</span>
                    <input placeholder="nom" type="text"  name="nom_contact" value="<?=$datas['nom_contact']?>"class="form-control" id="inputnom_contact">
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputprenom_contact" class="form-label">prenom du contact</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">prenom du contact</span>
                    <input type="text"   placeholder="prenom" name="prenom_contact" value="<?=$datas['prenom_contact']?>"class="form-control" id="inputprenom_contact">
                </div>
            </div>
    
            <div class="col-12 col-lg-6">
                <label for="inputfonction" class="form-label">fonction</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">fonction</span>
                    <input type="text" placeholder="emploi occupé"  name="fonction" value="<?=$datas['fonction']?>"class="form-control" id="inputfonction">
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">@</span>
                    <input placeholder="monemail@nomdedomaine.com" type="email" name="email" value="<?=$datas['email']?>"class="form-control" id="inputEmail4">
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputtel" class="form-label">telephone</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">telephone</span>
                    <input placeholder="0123456789" type="tel"  name="tel" value="<?=$datas['tel']?>"  class="form-control id="inputtel4">
                </div>
            </div>
            <div class="col-12">
                <div class="form-check checkbox">
                <input name="active" value="1" <?=(1==$datas['active'])?'checked':''?> class="form-check-input" type="checkbox" id="gridCheck">
                <label class="form-check-label" for="gridCheck">
                    Disponible
                </label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" name="validating_edit" value="<?=$datas['id_fournisseur']?>" class="btn btn-primary">Enregistrer</button>
                <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
            </div>
        </form>
    </div>
</div>
   
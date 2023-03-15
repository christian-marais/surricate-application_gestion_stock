<div class="bloc cards row">
    <div class="col-12">
        <form method="POST" action="<?=$this->setUri('admin/fournisseurs')?>" class="row gx-5">
            
            <div class="col-12 col-lg-6">
                <label for="inputNom" class="form-label">ID</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">ID</span>
                    <input type="text"  placeholder="nom de maximum 10 lettres" maxlength="10" name="id_fournisseur" value=""class="form-control" id="inputNom" required>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputPrenom" class="form-label">Entreprise</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Entreprise</span>
                    <input type="text"  placeholder="nom du fournisseur" name="nom_fournisseur" value=""class="form-control" id="inputnom" required>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputadresse" class="form-label">adresse</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">adresse</span>
                    <input type="text" placeholder="adressse du fournisseur" name="adresse_fournisseur" value=""class="form-control" id="inputadresse" required>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputcp" class="form-label">CP</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">Code Postal</span>
                    <input type="text"  placeholder="code postal" name="cp_fournisseur" value=""class="form-control" id="inputcp" required>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputnom_contact" class="form-label">Nom du contact</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">nom du contact</span>
                    <input type="text" placeholder="nom" name="nom_contact" value=""class="form-control" id="inputnom_contact">
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <label for="inputprenom_contact" class="form-label">prenom du contact</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">prenom du contact</span>
                    <input type="text"  placeholder="prenom" name="prenom_contact" value=""class="form-control" id="inputprenom_contact">
                </div>
            </div>
    
            <div class="col-12 col-lg-6">
                <label for="inputfonction" class="form-label">fonction</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">fonction</span>
                    <input type="text" placeholder="emploi occupé"  name="fonction" value=""class="form-control" id="inputfonction">
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">@</span>
                    <input type="email" placeholder="monemail@nomdedomaine.com" name="email" value=""class="form-control" id="inputEmail4" required>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <label for="inputtel" class="form-label">telephone</label>
                <div class="input-group mb-3">
                    <span class="input-group-text">telephone</span>
                    <input type="tel"  placeholder="0123456789" name="tel" value=""  class="form-control id="inputtel4">
                </div>
            </div>

            <div class="col-12">
                <div class="form-check checkbox">
                <input class="form-check-input" value="1" type="checkbox" id="gridCheck" checked>
                <label class="form-check-label" for="gridCheck">
                Compte actif
                </label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" name="validating_create" value="" class="btn btn-primary">Enregistrer</button>
                <a class="btn btn-success" href="<?=$_SERVER['HTTP_REFERER']?>">Retour</a>
            </div>
        </form>
    </div>
</div>
   
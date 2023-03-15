<section>
   <div class="container">
      <div class="row">
         <div class=" cards col-11 col-md-7">
            <div class="row">
               <div class='col-6'>
               </div>
               <div class='col-6'>
                  <form method='POST' id="page"class="row" action="#page">
                     <div class='col-7'>
                        <div  name="search-form" class="search-form" >
                           <i class="bx bx-search search"></i>
                           <input type="search" name="search-<?=PAGE?>" value="<?=$_POST['search-'.PAGE]?>"placeholder="id, libelle">
                        </div>
                     </div>
                     <div class='col-4'>
                        <div>
                           <select name="offset-limit" class="form-select offset-limit" id="offset-limit" required>
                              <option selected><?=$_POST['offset-limit']?></option>
                              <option>1</option>
                              <option>10</option>
                              <option>20</option>
                              <option>50</option>
                           </select>
                        </div>
                     </div>
                     <div class='col-12 text-center'>
                        <div class="row">
                           <div class="pagination" name="pagination" method="POST">
                              <button name='page'class="button"  type="submit"value="<?=intval($_POST['page'])-1?>">
                                 Précédent
                              </button>
                              <ul>
                                 <?php $t=0;for($p=1;$p<=$numberOfPage;$p++):?>
                                    <?php  if($p<=$_POST['page']+2 && $p>=$_POST['page']-2):?>
                                       <li>
                                          <button name="page" class="button" type="submit" value="<?=$p?>">
                                             <?=($p==$_POST['page'])?'...':$p?>
                                          </button>
                                       </li>
                                    <?php endif;?>
                                 <?php endfor;?> 
                              </ul>
                              <button name='page'class="button"  type="submit"value="<?=($_POST['page']<$numberOfPage)?intval($_POST['page'])+1:'';?>">
                                 Suivant
                              </button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
            <div class="row">
               <form  class="col-12"  method="POST" id="form_produit">
                  <div class='row form_liste'>
                     <table class="table">
                        <thead class="table_head">
                           <tr>
                              <th>ID</th>
                              <th>Libellé</th>
                              <th>Domaine</th>
                              <th class="action_button">Action</th>
                           </tr>
                        </thead>
                        <tbody class="table_body">
                           <tr>
                              <td>
                                 <input placeholder="ID de 50 caractères maximum" maxlength="50" class="form-control" name="id_formation"/>
                              </td>
                              <td>
                                 <input placeholder="Titre de la formation "class="form-control" name="libelle_formation"/>
                              </td>
                              <td>
                                 <select id="inputdomaine" name="id_domaine" class="form-select">
                                    <?php foreach ($domaines as $domaine) :?>
                                    <option><?=$domaine["id_domaine"]?></option>
                                    <?php endforeach?>
                                 </select>
                              </td>
                              <td class="form_content_button action_button ">
                                 <button type="submit" id="add_button" name="create"  value="createFormation" class="edit_produit">
                                    <i class="uil uil-plus"></i>Créer
                                 </button>
                              </td>
                           </tr>
                           <?php $id=$metadatas[0]; foreach($datas as $data):?>
                           <tr>
                              <?php foreach($metadatas as $metadata):?>
                              <?php if($metadata == 'id_domaine'):?>
                              <td>
                                 <select id="inputdomaine" name="<?=$metadata.$data['id_formation']?>" class="form-select">
                                    <option selected><?=$data[$metadata]?></option>
                                    <?php foreach ($domaines as $domaine) :?>
                                    <option><?=$domaine["id_domaine"]?></option>
                                    <?php endforeach?>
                                 </select>
                              </td>
                              <?php else: ?>
                              <td>
                                 <input class="form-control" name="<?=$metadata.$data['id_formation']?>" value="<?=$data[$metadata]?>">
                              </td>
                              <?php endif;?>
                              <?php endforeach;?>
                              <td class="form_content_button action_button">
                                 <button type="submit" name="validating_edit" value="<?=$data[$id]?>"class="edit_formation"><i class="uil uil-pen"></i>Editer</button>
                                 <button type="submit" name="delete" value="<?=$data[$id]?>" class="delete_formation button danger"><i class="uil uil-trash-alt"></i>Supprimer</button>
                              </td>
                           </tr>
                           <?php endforeach;?>
                        </tbody>
                     </table>
                  </div>
               </form>
            </div>
            <div class="col-12 page-bottom">
            </div>
         </div>
         <div class="col-11 col-md-4">
            <div class="cards col-12">
               <h3>
                Domaines de formations
               </h3>
               <form class="row" method="POST" action="">
                  <h4>
                     Ajouter un domaine
                  </h4>
                  <div class="col-12">
                     <div class="row">
                        <div class="col-4 mb-3 mt-3">
                           <label  for="id_domaine">Code</label>
                           <input placeholder="ID de 5 caractères maximum" maxlength="5" class="form-control"  id="id_domaine_create" name="id_domaine_create" type="text">
                        </div>
                        <div class="col-8 mb-3 mt-3">
                           <label for="libelle_domaine">Libellé</label>
                           <input placeholder="Domaine de formation" class="form-control" name="libelle_domaine_create" id="libelle_domaine_create"type="text">
                        </div>
                     </div>
                     <div>
                        <label for="centre_list" class="form-label mb-3 mt-3">Centre</label>
                        <select id="centre_list" name="select_code_centre" class="form-select">
                           <option selected></selected>
                           <?php foreach ($centres as $centre) :?>
                           <option><?=$centre['id_centre']?></option>
                           <?php endforeach?>
                        </select>  
                     </div>
                     <br/>
                     <div class="row">
                        <div class="col-12 mb-3 mt-3">
                           <button class="button" type="submit" id="" name="create_domaine"  value="createDomaine">
                              <i class="uil uil-plus"></i>Créer
                           </button>
                        </div>
                     </div>
                  </div>
               </form>
               <form class="row" id="domaine" method="POST" action="#domaine">
                  <hr/>
                  <h4>
                     Editer un domaine
                  </h4>
                  <div class="row">
                     <div class="col-12 mb-3 mt-3">
                        <label for="domaine_list" class="form-label">domaine</label>
                        <select id="domaine_list" name="select_code_domaine"  class="form-select selectChoice">
                           <option selected><?=$selectedDomaine['id_domaine']?></selected>
                           <?php foreach ($domaines as $domaine) :?>
                           <option><?=$domaine['id_domaine']?></option>
                           <?php endforeach?>
                        </select>   
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-4 mb-3 mt-3">
                        <label   for="id_domaine">Code</label>
                        <input placeholder="ID de 50 caractères maximum" maxlength="50" class="form-control"  name="id_domaine_for_edition" id="id_domaine" value="<?=$selectedDomaine['id_domaine']?>" type="text">
                     </div> 
                     <div class="col-8 mb-3 mt-3">
                        <label  for="id_domaine">Libelle</label>
                        <input placeholder="Domaine de formation" class="form-control"   value="<?=$selectedDomaine['libelle_domaine']?>" name="libelle_domaine_edit" id="libelle_domaine_edit" type="text">
                     </div>
                     <div>
                        <br/>
                        <label for="centre_list" class="form-label">Centre</label>
                        <br/>
                        <select id="centre_list" name="select_code_centre_edit" class="form-select">
                           <option selected><?=$selectedDomaine['id_centre']?></selected>
                           <?php foreach ($centres as $centre) :?>
                           <option><?=$centre['id_centre']?></option>
                           <?php endforeach?>
                        </select>  
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-12 mb-3 mt-3">
                        <button type="submit" name="validating_edit_domaine" value="<?=(empty($_POST['select_code_domaine']))?"":$_POST['select_code_domaine']?>"class="edit_formation button"><i class="uil uil-pen"></i>Editer</button>
                        <button type="submit" name="delete_domaine" value="<?=(empty($_POST['select_code_domaine']))?"":$_POST['select_code_domaine']?>" class="delete_formation button danger"><i class="uil uil-trash-alt"></i>Supprimer</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>
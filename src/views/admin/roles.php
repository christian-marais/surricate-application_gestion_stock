<section>
   <div class="container">
      <div class="row">
         <div class=" col-12 col-md-8">
            <div class="bloc row cards">
               <div class='col-5'>
               </div>
               <div class='col-5'>
                  <form method='POST' class="row">
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
               <div class='col-12 '>
                  <form class='row form_liste' method="post" id="form_produit">
                     <table class="table">
                        <thead class="table_head">
                           <tr>
                              <th>id</th>
                              <th>Nom de role</th>
                              <th>Niveau du role</th>
                              <th class="action_button">Action</th>
                           </tr>
                        </thead>
                        <tbody class="table_body">
                           <tr>
                              <td><input placeholder="10 caractères max" maxlength="10"name="create_code_role"  class="input" type="text" value=""/></td>
                              <td><input placeholder="nom de role"  name="create_nom_role"  class="input" type="text" value=""/></td>
                              <td><input placeholder="niveau de role"  name="create_permission"  class="input" type="number" min="0" value=""/></td>
                              <td class="form_content_button action_button ">
                                 <button type="submit" id="create" name="create_role"  value="" class="edit_produit">
                                 <i class="uil uil-plus"></i>Créer
                                 </button>
                              </td>
                           </tr>
                           <?php foreach($roles as $role):?>
                           <tr>
                              <td><input name="code_role<?=$role['code_role']?>"  class="input" type="text" value="<?=$role['code_role']?>"/></td>
                              <td><input name="nom_role<?=$role['code_role']?>"  class="input" type="text" value="<?=$role['nom_role']?>"/></td>
                              <td><input name="permission<?=$role['code_role']?>"  class="input" type="text" value='<?=$role['permission']?>'/></td>
                              <td class="form_content_button action_button">
                                 <input type='text' name="<?=$role['code_role']?>" value="<?=$role['code_role']?>" class="hidden"/>
                                 <button type="submit" name="validating_edit" value="<?=$role['code_role']?>"class="edit_produit">
                                    <i class="uil uil-pen"></i>Editer
                                 </button>
                                 <button type="submit" name="delete" value="<?=$role['code_role']?>" class="delete_produit button danger">
                                    <i class="uil uil-trash-alt"></i>Supprimer
                                 </button>
                              </td>
                           </tr>
                           <?php endforeach;?>
                        </tbody>
                     </table>
                  </form>
               </div>
               <div class="col-12 page-bottom">
               </div>
            </div>
         </div>
         <div class="col-12 col-md-4">
            <div class="cards">
               <h3>
                Attribution des roles
               </h3>
               <form class="row" method="POST" action="#attribution">
                     <div class="col-12">
                        <p>
                           Ajouter / supprimer un rôle d'un groupe
                        </p>
                        <div class="row">
                           <div class="col-12 col-lg-7"> 
                           </div>
                           <div id="attribution" class="col-12 col-lg-5">
                           <label for="choice" class="form-label">Option</label>
                              <select id="id_groupe" name="option" onchange="this.form.submit() "class="form-select">
                                 <option selected><?=$_POST['option']?></selected>
                                 <option>Ajouter</option>
                                 <option>Supprimer</option>
                              </select> 
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-12">
                              <label for="id_groupe" class="form-label">Groupe :</label>
                              <select id="id_groupe" name="selected_id_groupe" onchange="this.form.submit() "class="form-select">
                                 <option selected><?=$_POST['selected_id_groupe']?></selected>
                                 <?php foreach ($datas['groupe'] as $groupe) :?>
                                 <option><?=$groupe['id_groupe']?></option>
                                 <?php endforeach?>
                              </select>  
                           </div>
                           <div class="col-12">
                              <label  class="form-label" for="libelle_groupe">Libelle de Groupe :</label>
                              <input placeholder="libelle de groupe" class="form-control"  name="selected_libelle_groupe" id="libelle_groupe" value="<?=$_POST['selected_libelle_groupe']?>" type="text">
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-12">
                              <label for="id_role" class="form-label">Role :</label>
                              <select id="id_role" name="selected_id_role" onchange="this.form.submit() "class="form-select">
                                 <option selected><?=$_POST['selected_id_role']?></selected>
                                 <?php if(!empty($selectedListRole )):?>
                                 <?php foreach ($selectedListRole as $role) :?>
                                 <option><?=$role['code_role']?></option>
                                 <?php endforeach?>
                                 <?php endif;?>
                              </select>   
                           </div>
                           <div class="col-12">
                              <label  class="form-label" for="libelle_role">Libelle du role :</label>
                              <input placeholder="nom de role" class="form-control"   value="<?=$_POST['selected_nom_role']?>" name="selected_nom_role" id="nom_role_edit" type="text">
                           </div>
                        </div>
                           <div class="row">
                              <div class="col-12">
                                 <button class="button" type="submit" id="" name="add_role"  value="add_role">
                                    <i class="uil uil-plus"></i>Ajouter
                                 </button>
                                 <button type="submit" name="delete_role_from_groupe" value="<?=$_POST['selected_id_role']?>" class="delete_formation button danger">
                                    <i class="uil uil-trash-alt"></i>Supprimer
                                 </button>         
                              </div>
                           </div>
                        </div>
                     </div>
               </form>
            </div>
         </div>
      </div>
   </div>
   <div class="container">
      <div class=" cards row">
         <form method='POST' class="col-12 " action="#role_choice">
            <div class='col-5'>
            </div>
            <div class='col-5'>
               <div class="row">
                  <div class='col-7' id="role_choice">
                    <p>
                       Sélectionner un role : 
                    </p>
                  </div>
                  <div class='col-4'>
                     <div>
                        <select name="role_choice" class="form-select selectChoice" id="role-choice" required>
                           <option selected><?=$_POST['role_choice']?></option>
                          <?php foreach($rolesForPermission as $role):?>
                           <option><?=$role['code_role']?></option>
                           <?php endforeach;?>
                        </select>
                     </div>
                  </div>
               </div>
            </div>
            <br/>
            <div class='col-7'>
               <p>
                  Sélectionner les permissions adéquates
               </p>
            </div>
            <div class='col-12 '>
               <div class='row form_liste' method="post" id="form_produit">
                  <table class="table">
                     <thead class="table_head">
                     <tr>
                        <th>Page</th>
                        <?php foreach($this->getPermissionTable() as $securityPermission):?>
                           <th><?=$securityPermission?></th>
                           <th class="action_button">Action</th>
                        <?php endforeach;?>
                        </tr>
                     </thead>
                     <tbody class="table_body">
                        <?php foreach($permissionAttribute as $permission):?>
                        <tr>
                           <td><?=$this->translateIndexValueToString($permission->id)?></td>
                           <?php foreach($permission->permissions as $permissionName => $permissionValue):?>
                              
                              <td>
                                 <div class="form-check form-switch">
                                    <input class="form-check-input" name=" <?=$permissionName.$permission->id?>" type="checkbox" <?=(1===$permissionValue)?'checked':''?>>
                                 </div>
                              </td>
                              <td class="action_button">Action</td>
                           <?php endforeach;?>  
                           <td class="form_content_button action_button">
                              <input type='text' name="<?=$permissionName?>" value="<?=$permissionName?>" class="hidden"/>
                              <button type="submit" name="validating_edit_permission" value="<?=$permission->id?>"class="edit_produit">
                                 <i class="uil uil-pen"></i>Editer
                              </button>
                           </td>
                        </tr>
                        <?php endforeach;?>
                     </tbody>
                  </table>
               </div>
            </div>
         </form>
      </div>
   </div>
</section>
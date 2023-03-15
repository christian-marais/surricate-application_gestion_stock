<section>
   <div class="container">
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
         <form class='col-12' method="post" id="form_produit">
            <div class='row form_liste' >
               <table class="table">
                  <thead class="table_head">
                     <tr>
                        <th>ID CURSUS</th>
                        <th>ID de la formation</th>
                        <th>ID du centre</th>
                        <th>Annee</th>
                        <th class="action_button">Action</th>
                     </tr>
                  </thead>
                  <tbody class="table_body">
                     <tr>
                        <td><input placeholder="ID de 50 caractères maximum" maxlength="50" class="form_control" name="id_cursus"/></td>
                        <td>
                           <select id="id_formation" name="id_formation" class="form-select" >
                              <option selected></option>
                              <?php foreach ($formations as $formation) :?>
                              <option><?=$formation['id_formation']?></option>
                              <?php endforeach?>
                           </select>
                        </td>
                        <td>
                           <select id="id_centre" name="id_centre" class="form-select" >
                              <option selected></option>
                              <?php foreach ($centres as $centre) :?>
                              <option><?=$centre['id_centre']?></option>
                              <?php endforeach?>
                           </select>
                        </td>
                        <td class="">
                           <input type='number' max='2100' min='2022' placeholder="Annee de formation" class="form-control"  name="annee"/>
                        </td>
                        <td class="form_content_button action_button ">
                           <button type="submit" id="add_button" name="create"  value="createCentre" >
                              <i class="uil uil-plus"></i>Créer
                           </button>
                        </td>
                     </tr>
                     <?php foreach($datas as $data):?>
                        <tr>
                           <td>
                              <input class="form_control" name="<?='id_cursus'.$data['id_cursus']?>" value="<?=$data['id_cursus']?>">
                           </td>
                           <td>
                              <select id="id_formation" name="<?='id_formation'.$data['id_cursus']?>" class="form-select" >
                                 <option selected><?=$data['id_formation']?></option>
                                 <?php foreach ($formations as $formation) :?>
                                 <option><?=$formation['id_formation']?></option>
                                 <?php endforeach?>
                              </select>
                           </td>
                           <td>
                              <select id="id_centre" name="<?='id_centre'.$data['id_cursus']?>" class="form-select" >
                                 <option selected><?=$data['id_centre']?></option>
                                 <?php foreach ($centres as $centre) :?>
                                 <option><?=$centre['id_centre']?></option>
                                 <?php endforeach?>
                              </select>
                           </td>
                           <td class="">
                              <input type='number'  name="<?='annee'.$data['id_cursus']?>" value="<?=$data['annee']?>" max='2100' min='2000' placeholder="Annee de formation" class="form-control" />
                           </td>
                           <td class="form_content_button action_button">
                                 <input type='text' name="<?=$data['id_cursus']?>" value="<?=$data['id_cursus']?>" class="hidden"/>
                                 <button type="submit" name="validating_edit" value="<?=$data['id_cursus']?>"class="edit_groupe"><i class="uil uil-pen"></i>Editer</button>
                                 <button type="submit" name="delete" value="<?=$data['id_cursus']?>" class="delete_groupe button danger"><i class="uil uil-trash-alt"></i>Supprimer</button>
                           </td>
                        </tr>
                     <?php endforeach;?>
                  </tbody>
               </table>
            </div>
         </form>
         <div class="col-12 page-bottom">
         </div>
      </div>
   </div>   
</section>

<section>
   <div class="container ">
      <div class="bloc cards row">
         <div class="form_top">
            <div class="row">
               <form class='col-6' method="POST">
                  <button class="button" type="submit" name="create" >
                     <i class="uil uil-plus"></i> Creer
                  </button>
               </form>
               <div class='col-6'>
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
            </div>
         </div>
         <div class='col-8 form_liste'>
            <table class="table col-12">
               <thead class="table_head">
                  <tr>
                     <th>id</th>
                     <th>nom</th>
                     <th>adresse</th>
                     <th>cp</th>
                     <th>nom du contact</th>
                     <th>prenom du contact</th>
                     <th>fonction</th>
                     <th>email</th>
                     <th>tel</th>
                     <th>actif</th>
                     <th class="action_button">Action</th>
                  </tr>
               </thead>
               <tbody class="table_body">
                  <form method="post" id="form_produit" readonly="readonly">
                     <?php  $id=$metadatas[0]; foreach($datas as $data):?>
                        <tr>
                           <?php foreach($metadatas as $metadata):?>
                           <td>
                              <input class="form_input" readonly="readonly" value="<?=$data[$metadata]?>">
                           </td>
                           <?php endforeach;?>
                           <td class="form_content_button action_button">
                              <input type='text' name="<?=$data[$id]?>" value="<?=$data['nom_fournisseur']?>" class="hidden"/>
                              <button type="submit" name="edit" value="<?=$data[$id]?>"class="edit_produit"><i class="uil uil-pen"></i>Editer</button>
                              <button type="submit" name="delete" value="<?=$data[$id]?>" class="delete_produit button danger"><i class="uil uil-trash-alt"></i>Supprimer</button>
                           </td>
                        </tr>
                     <?php endforeach;?></form>
               </tbody>
            </table>
         </div>
         <div class="col-12 page-bottom">
         </div>
      </div>
   </div>
</section>
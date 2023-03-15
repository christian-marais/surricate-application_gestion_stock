<section>
   <div class="container-fluid">
      <div class="bloc cards row">
         <div class="form_top col-12">
            <div class="row">
               <form class="col-6" method="POST">
                  <button class="button" type="submit" name="create" href="<?=BASE_URI?>/admin/<?=PAGE?>">
                     <i class="uil uil-plus"></i> Creer
                  </button>
               </form>
               <div class='col-6'>
                  <form method='POST' class="row">
                     <div class='col-7'>
                        <div  name="search-form" class="search-form" >
                           <i class="bx bx-search search"></i>
                           <input type="search" name="search-<?=PAGE?>" value="<?=$_POST['search-'.PAGE]?>"placeholder="id, date">
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
         <form method="post" id="form_produit">
            <div class='form_liste'>
               <table class="table col-8">
                  <thead class="table_head">
                     <tr>
                        <th>N° Commande</th>
                        <th>Date</th>
                        <th>Code fournisseur</th>
                        <th>Nom fournisseur</th>
                        <th>Id personnel</th>
                        <th>Nom personnel</th>
                        <th>Prenom personnel</th>
                        <th>Montant TTC</th>
                        <th>Statut</th>
                        <th  class="form_content_button action_button taller">Action</th>
                     </tr>
                  </thead>
                  <tbody class="table_body">
                     <?php foreach($commandes as $commande):?>
                        <tr id="<?=$commande['num_com']?>">
                           <td><?=$commande['num_com']?></td>
                           <td><?=$commande['date_Com']?></td>
                           <td><?=$commande['id_fournisseur']?></td>
                           <td><?=$commande['nom_fournisseur']?></td>
                           <td><?=$commande['id_user']?></td>
                           <td><?=$commande['nom_user']?></td>
                           <td><?=$commande['prenom_user']?></td>
                           <td><?=$commande['Montant TTC']?></td>
                           <td><?=$commande['statut']?></td>
                           <td class="form_content_button action_button">
                              <input type='text' name="<?=$commande['num_com']?>" value="<?=$commande['num_com']?>" class="hidden"/>
                              <button type="submit" name="editCommande" value="<?=$commande['num_com']?>"class="edit_produit"><i class="uil uil-pen"></i>Editer</button>
                              <button type="submit" name="deleteCommande" value="<?=$commande['num_com']?>" class="delete_produit button danger"><i class="uil uil-trash-alt"></i>Supprimer</button>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </form>
         <div class="col-12 page-bottom">
         </div>
      </div>
   </div>
</section>
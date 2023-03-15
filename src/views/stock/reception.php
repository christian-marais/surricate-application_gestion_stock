<section>
   <div class="container">
      <div class="row">
         <div class="cards col-11">
            <div class="bloc location row">
               <div class="form_top col-6">
                  <form method="POST">
                     <button class="btn btn-success" type="submit" name="create" href="<?=BASE_URI?>admin/<?=PAGE?>">
                     Nouvelle livraison
                     </button>
                  </form>
               </div>
               <div class='col-6'>
                  <form method='POST' class="row">
                     <div class='col-7'>
                        <div  name="search-form" class="search-form" >
                           <i class="bx bx-search search"></i>
                           <input type="search" name="search-<?=PAGE?>" value="<?=$_POST['search-'.PAGE]?>"placeholder="date, id fournisseur">
                        </div>
                     </div>
                     <div class='col-4'>
                        <div>
                           <select name="offset-limit" class="form-select offset-limit" id="offset-limit"  required>
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
               <div class='col-8 form_liste'>
                  <table class="table">
                     <thead class="table_head">
                        <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>N° Commande</th>
                        <th>Code personnel</th>
                        <th>Nom</th>
                        <th>prenom</th>
                        <th>Code fournisseur</th>
                        <th>Fournisseur </th>
                        <th>Montant en €</th>
                        <th class="form_content_button action_button">Action</th>
                        </tr>
                     </thead>
                     <tbody class="table_body">
                        <form method="post" id="form_produit" readonly="readonly">
                           <?php foreach($datas as $reception):?>
                           <tr>
                              <td>
                                 <input class="form_input short-num" readonly="readonly" value="<?=$reception['id_reception']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['date_reception']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['num_com']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['id_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['nom_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['prenom_user']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['id_fournisseur']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['nom_fournisseur']?>">
                              </td>
                              <td>
                                 <input class="form_input" readonly="readonly" value="<?=$reception['Montant']?>">
                              </td>
                              <td class="form_content_button action_button">
                                 <input type='text' name="<?=$reception['id_reception']?>" value="<?=$reception['id_reception']?>" class="hidden"/>
                                 <button type="submit" name="see" value="<?=$reception['id_reception']?>"><i class="uil uil-pen"></i>Voir</button>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                        </form>
                     </tbody>
                  </table>
               </div>
               <div class="col-12 page-bottom">
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
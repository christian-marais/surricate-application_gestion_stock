<section>
   <div class="container">
      <div class="row">
         <div class="cards col-11 col-lg-7">
            <div class="bloc location row">
               <div class="form_top col-6">
                  <form method="POST">
                     <button class="button" type="submit" name="create" href="<?=BASE_URI?>/admin/<?=PAGE?>">
                        <i class="uil uil-plus"></i> Creer
                     </button>
                  </form>
               </div>
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
               <form method="post" id="form_produit">
                  <div class='col-8 form_liste'>
                     <table class="table col-12">
                        <thead class="table_head">
                           <tr>
                              <th>id</th>
                              <th>image</th>
                              <th>reference</th>
                              <th>description</th>
                              <th>unite</th>
                              <th>pu</th>
                              <th>stock de securite</th>
                              <th>famille de produit </th>
                              <th>fournisseur</th>
                              <th class="form_content_button  action_button">action</th>
                           </tr>
                        </thead>
                        <tbody class="table_body">
                           <?php foreach($articles as $article):?>
                              <?php if($article['active']==1||in_array('admin',$s=explode(',',$_SESSION['role']))):?>
                              <tr id="'<?=$article['id_article']?>">
                                 <td><input class="short-num" type="text" value="<?=$article['id_article']?>" readonly="readonly"/></td>
                                 <td><img class='image-article' src="<?=BASE_URI.'/images/banque/articles/'.$article['lien_image']?>" alt="<?=$article['lien_image']?>" class="img-fluid image"></td>
                                 <td><input readonly="readonly" class="input" type="text" value="<?=$article['reference_article']?>"/></td>
                                 <td><textarea readonly="readonly" class="input" class="" readonly="readonly"><?=$article['description_article']?></textarea></td>
                                 <td><input readonly="readonly" class="input" type="text" value="<?=$article['unite']?>"/></td>
                                 <td><input readonly="readonly" class="input" type="text" value="<?=$article['pu']?>"/></td>
                                 <td><input readonly="readonly" class="input" type="text" value="<?=$article['stock_de_securite']?>"/></td>
                                 <td><input readonly="readonly" class="input" type="text" value="<?=$article['id_famille_article']?>"/></td>
                                 <td><input readonly="readonly" class="input" type="text" value="<?=$article['id_fournisseur']?>"/></td>
                                 <td class="form_content_button  action_button">
                                    <input type='text' name="<?=$article['id_article']?>" value="" class="hidden"/>
                                    <button type="submit" name="edit" value="<?=$article['id_article']?>"class="edit_produit">
                                       <i class="uil uil-pen">Editer</i>
                                    </button>
                                    <button type="submit" name="delete" value="<?=$article['id_article']?>" class="delete_produit button danger">
                                       <i class="uil uil-trash-alt"></i>Supprimer
                                    </button>
                                 </td>
                                 </td>
                              </tr>
                              <?php endif; ?>
                           <?php endforeach; ?>
                        </tbody>
                     </table>
                  </div>
               </form>
               <div class="col-12 page-bottom">
               </div>
            </div>
         </div>
         <div class="sidebar_alt col-12 col-lg-4">
            <div id="edit_categorie" class="cards col-11 col-lg-12">
            <h3>
                Famille de produit
               </h3>
               <form class="row" method="POST" action="#edit_categorie">
                  <h4>
                     Ajouter une categorie
                  </h4>
                  <div class="col-12">
                     <div class="row">
                        <div class="col-4 mb-3 mt-3">
                           <label  for="id_categorie">Code</label>
                           <input class="form-control"  id="id_categorie_create" name="id_categorie_create" type="text">
                        </div>
                        <div class="col-8 mb-3 mt-3">
                           <label for="libelle_categorie">Libellé</label>
                           <input class="form-control" name="libelle_categorie_create" id="libelle_categorie_create"type="text">
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-12 mb-3 mt-3">
                           <button class="button" type="submit" id="" name="create_categorie"  value="createCategorie">
                              <i class="uil uil-plus"></i>Créer
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-12 mb-3 mt-3">
                  <h4>
                     Editer un categorie
                  </h4>
                  <div class="row">
                     <div class="col-12 mb-3 mt-3">
                        <label for="categorie_list" class="form-label">categorie</label>
                        <select id="categorie_list" name="select_code_categorie" onchange="this.form.submit() "class="form-select">
                           <option selected><?=$selectedCategorie['id_famille_article']?></selected>
                           <?php foreach ($categories as $categorie) :?>
                           <option><?=$categorie['id_famille_article']?></option>
                           <?php endforeach?>
                        </select>   
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-4 mb-3 mt-3">
                        <label  for="id_categorie">Code</label>
                        <input class="form-control"  name="id_categorie_for_edition" id="id_categorie" value="<?=$selectedCategorie['id_famille_article']?>" type="text">
                     </div>
                     <div class="col-8 mb-3 mt-3">
                        <label  for="id_categorie">Libelle</label>
                        <input class="form-control"   value="<?=$selectedCategorie['libelle_famille']?>" name="libelle_categorie_edit" id="libelle_categorie_edit" type="text">
                     </div>
                  </div>
                  <div class="row">
                     </div class="col-12 mb-3 mt-3">
                     <button type="submit" name="validating_edit_categorie" value="<?=(empty($_POST['select_code_categorie']))? "":$_POST['select_code_categorie'];?>"class="edit_formation button">
                        <i class="uil uil-pen"></i>Editer
                     </button>
                     <button type="submit" name="delete_categorie" value="<?=(empty($_POST['select_code_categorie']))? "":$_POST['select_code_categorie'];?>" class="delete_formation button danger">
                        <i class="uil uil-trash-alt"></i>Supprimer
                     </button>         
                  </div>
               </form>
            </div>
         </div>
        
      </div>
     
   </div>
</section>
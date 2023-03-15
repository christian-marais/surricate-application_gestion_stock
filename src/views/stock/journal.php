               
               <section>
                  <div class="container-fluid">
                     <div class="cards row">
                        <div class="col-12 ">
                           <div class="col-12 form_top">
                              <h2>
                                 Historique d'entrée/sortie
                              </h2>
                           </div>
                           <form method="post" id="form_produit">
                              <div class="row mt-3">
                                 <div class="col-12 col-lg-2 mb-3 ">
                                    <label for="groupe" class="form-label">
                                       Par Groupe
                                    </label>
                                    <select name="id_groupe" class="form-select  selectChoice" id="groupe">
                                       <option selected  ><?=$_POST['id_groupe']?> </option>
                                       <option></option>
                                       <?php foreach($groupes as $groupe):?> 
                                       <option><?=$groupe['id_groupe']?></option>
                                       <?php endforeach;?> 
                                    </select>
                                 </div>
                                 <div class="col-12 col-lg-2 mb-3">
                                    <label for="user" class="form-label">
                                       Par Utilisateur
                                    </label>
                                    <select name="id_user" class="form-select  selectChoice" id="user"  required>
                                       <option selected><?=$_POST['id_user']?></option>
                                       <?php foreach($utilisateurs as $utilisateur):?> 
                                       <option><?=$utilisateur['id_user']?> </option>
                                       <?php endforeach;?> 
                                    </select>
                                 </div>
                                 <div class="col-12 col-lg-2 mb-3">
                                 </div>
                                 <div class="col-12 col-lg-3 mb-3">
                                    <label for="date" class="form-label">
                                          Date de départ
                                    </label>
                                    <input class="form-control  selectChoice" name="dateDeb" type="date" value="<?=$_POST['dateDeb'] ?>" >
                                 </div>
                                 <div class="col-12 col-lg-3 mb-3">
                                    <label for="date_fin" class="form-label">
                                          Date de fin
                                    </label>
                                    <input class="form-control  selectChoice" name="dateFin" type="date" value="<?=$_POST['dateFin']?>" >
                                 </div>
                              </div>
                              <div class='col-12 overflow'>
                                 <table class="table col-12">
                                    <thead class="table_head">
                                       <tr>
                                          <th>ID</th>
                                          <th>Référence</th>
                                          <th>Libellé</th>
                                          <th>Unité</th>
                                          <th>P.U.</th>
                                          <th class="label">Livraison</th>
                                          <th class="label">Entrées</th>
                                          <th class="label">Sorties</th>
                                          <th class="label">Sécurité</th>
                                          <th class="label">Disponible</th>
                                          <th class="label">Diff Sécurité</th>
                                          <th class="label">Statut</th>
                                       </tr>
                                    </thead>
                                    
                                    <tbody class="table_body">
                                       <?php $p=0;foreach($stockStatus as $stock =>$value):?>  
                                          <tr id="<?=$value['id_article']?> ">
                                             <?php foreach($value as $property =>$propertyValue):?>  
                                             <td class="<?=$property?> <?=$p?>"><?=$propertyValue?> </td>
                                             <?php endforeach;?> 
                                             <td></td>
                                          </tr>
                                       <?php endforeach;?> 
                                    </tbody>
                                 </table>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </section>
               <section class=" cards bloc row"> 
               <div class="overflow col-12 col-md-6 col-lg-4">
                     <canvas id="myChart" width="150" height="150"></canvas>
                  </div>
                  <div class="overflow col-12 col-md-6 col-lg-4">
                     <canvas id="myChartj" width="150" height="150"></canvas>
                  </div>
                  <div class="overflow col-12 col-md-6 col-lg-4">
                     <canvas id="myChartj2" width="150" height="150"></canvas>
                  </div>
                  <div class="overflow col-12">
                     <canvas id="myChartj3" width="750" height="150"></canvas>
                  </div>
               </section>
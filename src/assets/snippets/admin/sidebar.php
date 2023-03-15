      
      <div class="sidebar">
         <div class="logo_content">
            <div class="logo">
            <i class='bx bx-store-alt'></i>
               <div class="logo_name">Surricate <?=VERSION?>
               </div>
            </div>
            <i class='bx bx-menu'id="btn" ></i>
         </div>
         <ul class="sidebar_list">
            <div class="sidebar_list_element">
               <li class="menu">
                  <a>
                     <i class='bx bx-user-circle'></i>
                     <span class="links_name">
                        Utilisateurs
                     </span>
                  </a>
                  <span class="tooltip">Gérer les utilisateurs </span>
               </li> 
               <li class="sub-menu">
                  <div>
                     <i class='bx bx-user-circle'></i>
                     <a href="<?=$this->setUri('admin/utilisateurs')?>">
                           <span class="">
                              Utilisateur
                           </span>
                     </a>
                  </div>
                  <div>
                     <i class='bx bx-group' ></i>
                     <a href="<?=$this->setUri('admin/groupes')?>">
                        <span class="">
                           Groupe
                        </span>
                     </a>
                  </div>
                  <div>
                     <i class='bx bxs-briefcase-alt-2'></i>
                     <a href="<?=$this->setUri('admin/roles')?>">
                        <span class="">
                           Role
                        </span>
                     </a>
                  </div>
               </li>    
            </div>
            <div class="sidebar_list_element">
               <li class="menu">
                  <a>
                     <i class='bx bxs-graduation'></i>
                     <span class="links_name">
                        Formation
                     </span>
                  </a>
                  <span class="tooltip">Gérer les Formations </span>
               </li>
               <li class="sub-menu">
                  <div>
                     <i class='bx bx-map' ></i>
                     <a href="<?=$this->setUri('admin/centres')?>">
                        <span class="">
                           Centres
                        </span>
                     </a>
                  </div>
                  <div>
                     <i class='bx bxs-book-alt' ></i>
                     <a href="<?=$this->setUri('admin/cursus')?>">
                        <span class="">
                           Cursus
                        </span>
                     </a>
                  </div>
                  <div>
                     <i class='bx bxs-graduation'></i>
                     <a href="<?=$this->setUri('admin/formations')?>">
                        <span class="">
                           Formation
                        </span>
                     </a>
                  </div>
               </li>    
            </div>
            <div class="sidebar_list_element">
               <li class="menu">
                  <a href="<?=$this->setUri('admin/fournisseurs')?>">
                  <i class='bx bx-group' ></i>
                     <span class="links_name">
                        Fournisseurs
                     </span>
                  </a>
                  <span class="tooltip">Gérer les fournisseurs </span>
               </li>      
            </div>
            <div class="sidebar_list_element">
               <li class="menu">
                  <a href="<?=$this->setUri('admin/articles')?>">
                  <i class='bx bx-basket'></i>
                     <span class="links_name">
                        Articles
                     </span>
                  </a>
                  <span class="tooltip">Gérer les articles </span>
               </li>     
            </div>
            <div class="sidebar_list_element">
               <li class="menu">
                  <a>
                     <i class='bx bxl-dropbox'></i>
                     <span class="links_name">
                        Stock
                     </span>
                  </a>
                  <span class="tooltip">Gérer les Stock </span>
               </li>
               <li class="sub-menu">
                  <div>
                     <i class='bx bx-user-circle'></i>
                     <a href="<?=$this->setUri('stock/livraisons')?>">
                           <span class="">
                              Livraisons
                           </span>
                     </a>
                  </div>
                  <div>
                  <i class='bx bx-group' ></i>
                  <a href="<?=$this->setUri('stock/achats')?>">
                     <span class="">
                        Commandes
                     </span>
                  </a>
                  </div>
                  <div>
                  <i class='bx bx-group' ></i>
                  <a href="<?=$this->setUri('stock/utilisations')?>">
                     <span class="">
                        Entrées/Sorties
                     </span>
                  </a>
                  </div>
               </li>         
            </div>
            <div>
               <li class="menu">
                  <a href="<?=$this->setUri('stock/journaux/articles')?>">
                     <i class="uil uil-history"></i>
                     <span class="links_name">
                        Journal
                     </span>
                  </a>
                  <span class="tooltip">Consulter le journal </span>
               </li>      
            </div> 
         </ul>
         <div class="profile_content">
            <div class="profile">
               <div class="profile_details">
                  <img src="<?=BASE_URI?>images\banque\AATI\loginlogo.png" alt="logo"/>
                  <div class="name_job">
                        <div class="name">
                           <?=(empty($_SESSION['prenom']))?'John':$_SESSION['prenom'];?> <?=(empty($_SESSION['nom']))?'Doe':$_SESSION['nom'];?>
                        </div>
                  </div>
               </div>
               <a href="<?=$this->setUri('auth/logout')?>"><i class="bx bx-log-out" id="log_out"></i></a>
            </div>
         </div>
      </div>
      
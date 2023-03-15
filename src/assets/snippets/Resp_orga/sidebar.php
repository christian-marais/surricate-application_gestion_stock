<div class="sidebar">
   <div class="logo_content">
      <div class="logo">
      <i class='bx bx-store-alt'></i>
         <div class="logo_name">Surricate v 1.2
         </div>
      </div>
      <i class='bx bx-menu'id="btn" ></i>
   </div>
   <ul class="sidebar_list">
      <li>
         <i class="bx bx-search"></i>
         <form name="search0">
            <input name="search" type="search" placeholder="search">
         </form>
      </li>
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
               <a href="<?=BASE_URI?>admin/utilisateurs">
                     <span class="">
                         Utilisateur
                     </span>
               </a>
            </div>
            <div>
            <i class='bx bx-group' ></i>
            <a href="<?=BASE_URI?>admin/groupes">
               <span class="">
                     Groupe
               </span>
            </a>
            </div>
            <div>
            <i class='bx bx-group' ></i>
            <a href="<?=BASE_URI?>admin/roles">
               <span class="">
                     Role
               </span>
            </a>
            </div>
         </li>    
      </div>
      
      <div class="sidebar_list_element">
         <li class="menu">
            <a href="<?=BASE_URI?>admin/formations">
               <i class='bx bxs-graduation'></i>
               <span class="links_name">
                  Formation
               </span>
            </a>
            <span class="tooltip">Gérer les Formations </span>
         </li>  
      </div>
      <div class="sidebar_list_element">
         <li class="menu">
            <a href="<?=BASE_URI?>admin/fournisseurs">
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
            <a href="<?=BASE_URI?>admin/articles">
            <i class='bx bx-popsicle'></i>
               <span class="links_name">
                  Articles
               </span>
            </a>
            <span class="tooltip">Gérer les articles </span>
         </li>     
      </div>
      <div class="sidebar_list_element">
         <li class="menu">
            <a href="<?=BASE_URI?>admin/articles">
               <i class='bx bx-basket'></i>
               <span class="links_name">
                  Catégories
               </span>
            </a>
            <span class="tooltip">Gérer les familles de produit </span>
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
               <a href="<?=BASE_URI?>stock/livraisons">
                     <span class="">
                        Livraisons
                     </span>
               </a>
            </div>
            <div>
            <i class='bx bx-group' ></i>
            <a href="<?=BASE_URI?>stock/achats">
               <span class="">
                  Commandes
               </span>
            </a>
            </div>
            <div>
            <i class='bx bx-group' ></i>
            <a href="<?=BASE_URI?>stock/utilisations">
               <span class="">
                  Entrées/Sorties
               </span>
            </a>
            </div>
         </li>         
      </div>
      <div>
         <li class="menu">
            <a href="<?=BASE_URI?>stock/journal">
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
            <img src="<?=BASE_URI?>images/AATI/logo.png" alt="logo"/>
            <div class="name_job">
               <div class="name">
                  <?=(empty($_SESSION['username']))?'John Doe':$_SESSION['username'];?>
               </div>
            </div>
         </div>
         <a href="<?=BASE_URI?>auth/logout"><i class="bx bx-log-out" id="log_out"></i></a>
      </div>
   </div>
</div>
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
         <input type="text" placeholder="search">
      </li>
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
            <i class='bx bx-group' ></i>
            <a href="<?=BASE_URI?>stock/utilisations">
               <span class="">
                  Entrées/Sorties
               </span>
            </a>
            </div>
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
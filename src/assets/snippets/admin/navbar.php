   
         <nav class="navbar">
            <ul>
               
               <li>
                  <a class='navbar_link'href="<?=$this->setUri('admin/menus')?>">
                     <i class='bx bxs-package'></i>
                  </a>
               </li>
               <li>
                  <a class='navbar_link'href="<?=$this->setUri('admin')?>">
                     <i class='bx bxs-home-smile'></i>
                  </a>
               </li>
               <li>
                  <form name="sendMail" method="POST">
                     <button type="submit" name="mail" class='navbar_link'>
                        <i class='bx bx-mail-send'></i>
                     </button>
                  </form>
               </li>
              
               <li>
                  <span class="role">
                  Connecté en : <?=(!empty($_SESSION['role']))?str_replace(',','/',$_SESSION['role']):'';?>
                  </span>
               </li>
            </ul>
         </nav>
         
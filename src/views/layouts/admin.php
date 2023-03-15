<!DOCTYPE html>
<html lang="en">
  <head>

    <?=HEAD?>

    <link rel="stylesheet" href="<?=BASE_URI?>src/assets/css/backend-style.css">
  </head>

  <body>

    <?=(isset($_POST['mail']) && !empty(MAIL))? MAIL:""?>

    <header> 
      <?=NAVBAR?>
      <?=SIDEBAR?>  
    </header> 
    
    <main>
      <section>
        <div class="home_content">
          <div class="text top_title">
            <h1>
              Page des <?=PAGE?> 
            </h1>
          </div>
          <div class="content">
            <div class="container"> 
              <?=$content?> 
            </div>
          </div>
        </div>
      </section>
    </main> 
    <?=(!empty(MODAL) && !empty($_COOKIE['message']))?MODAL:"";?>  
    <?=(isset($_POST['upload_file']) && !empty(FILE))? FILE:""?> 
    <script src="<?=BASE_URI?>node_modules/chart.js/dist/chart.min.js"></script>
    <?=FOOTER?> 
  </body>
</html>
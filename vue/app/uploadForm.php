<?php function page (){

    ob_start();

  ?>
  <div id="content">
    <form class="form" id="form" method="post" enctype="multipart/form-data">
      <div class=" w3-card w3-white w3-padding">
        <header class="w3-container">
          <h2> <i class="fa fa-upload"></i> Chargement de fichier.</h2>
          <hr class="w3-border w3-border-deep-orange">
        </header>
        <div class="w3-container w3-white" style="margin-top:25px;margin-bottom:40px;">

          <div class="w3-twothird" style="">

            <div class="w3-row ">
              <div id="alert_rsl"></div>
              <div class="w3-padding">
                <label class="w3-panel w3-padding "><b> Choisir le fichier Relevés: </b> </label>
              </div>
              <div class="w3-padding">
                <input type="file" name="fichier_releves" value="" class="w3-input  w3-margin-left "  accept=".csv , .lis "  ><br>
              </div>
            </div>
            <div class="w3-row ">
              <div class="w3-padding">
                <label class="w3-panel w3-padding "><b> Choisir le fichier En cours: </b> </label>
              </div>
              <div class="w3-padding ">
                <input type="file" name="fichier_encours" value="" class="w3-input  w3-margin-left "  accept=".csv, .lis"  ><br>
              </div>
            </div>
            <br>

          </div>
        </div>
        <div class="w3-container" style="height: 80px;" >
          <div class="w3-quarter w3-center w3-padding">
            <input type="hidden" name="action" value="enregistrer">
            <input type="submit" class="w3-btn w3-orange w3-hover-white w3-border w3-border-deep-orange w3-wide w3-padding-xlarge"
                   name='enregistrer'  value="Enregistrer" >
            <span id="submit" class="w3-margin"></span>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div id="feedback"></div>
<?php

 return ob_get_clean();

}

$page = page();

echo CodeCompressor::compress_html($page);

CodeCompressor::importer('vue/app/uploadFormScript.php' ,  "js" );

?>
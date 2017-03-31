<?php ob_start() ; ?>

<!DOCTYPE html>
<html>
<title> REPORTING </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= URL ?>css/w3css.min.css">
<link rel="stylesheet" href="<?= URL ?>css/app.css">
<link rel="stylesheet" href="<?= URL ?>js/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" href="<?= URL ?>js/jquery-ui/jquery-ui.structure.min.css">
<link rel="stylesheet" href="<?= URL ?>js/jquery-ui/jquery-ui.theme.min.css">
<link rel="stylesheet" href="<?= URL ?>/css/chosen.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="<?= URL; ?>js/jquery.js"></script>
<script type="text/javascript" src="<?= URL; ?>js/app.js"></script>
<script type="text/javascript" src="<?= URL; ?>js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= URL; ?>js/jquery.table2excel.js"></script>
<script type="text/javascript" src="<?= URL ?>js/chosen.jquery.min.js"></script>
<body class="w3-light-grey">
<div class="w3-container w3-top w3-black w3-large w3-padding" style="z-index:4">
    <button class="w3-btn w3-hide-large w3-padding-0 w3-hover-text-grey" onclick="w3_open()"><i class="fa fa-bars"></i> Menu
    </button>
    <span class="w3-right">Logo</span>
</div>
<nav class="w3-sidenav w3-collapse w3-white w3-animate-left" style="z-index:3;width:250px;" id="mySidenav"><br>
    <div class="w3-container w3-row-margin">
        <div class="w3-col s4 w3-center"><img src="<?= URL ?>image/avatar/<?= $_SESSION['user']['icon'] ?>" alt=""
                                              class="w3-circle" style="width:80%">
            <?php if ($_SESSION['service']['id_admin'] == $_SESSION['user']['id'])
                echo " <h6 class=' w3-text-red '> [ Admin ] </h6>";
            ?>
            <h6 class=" w3-tiny "><b><?= $_SESSION['service']['direction'] . "/" . $_SESSION['service']['nom']; ?> </b>
            </h6>

        </div>

        <div class="w3-col s8 w3-center">
            <hr style="margin:10px">
            <b><?= $_SESSION['user']['prenom'] . " " . $_SESSION['user']['nom'] ?> </b>
            <hr style="margin:10px">

            <?php if ($_SESSION['service']['id_admin'] == $_SESSION['user']['id']): ?>
                <a href="<?= URL ?>app/users" class="w3-hover-none w3-hover-text-green w3-show-inline-block"><i
                        class="fa fa-2x fa-user-o "></i></a>
                <a href="<?= URL ?>app/config"
                   class="w3-hover-none w3-hover-text-blue w3-show-inline-block <?= $menu_conf ?> "><i
                        class="fa fa-2x fa-cogs"></i></a>
            <?php endif; ?>

        </div>
    </div>
    <hr class="w3-margin-0">
    <a href="#" class="w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu">
        <i class="fa fa-remove fa-fw"></i>Fermer
    </a>
    <a class="w3-padding-16  w3-grey w3-margin-bottom "> Reporting </a>
    <a href="<?= URL ?>app/reporting/upload" class="w3-padding <?= $menu_up ?>"><i class="fa fa-upload fa-fw"></i>
        Charger Fichier </a>
    <a href="<?= URL ?>app/reporting/nouveau/global" class="w3-padding <?= $menu_ng ?>"><i
            class="fa fa-plus-circle fa-fw"></i> Reporting Global </a>
    <a href="<?= URL ?>app/reporting/nouveau/autre" class="w3-padding <?= $menu_na ?>"><i
            class="fa fa-plus-circle fa-fw"></i> Autre Reporting </a>
    <a href="<?= URL ?>app/reporting/liste/fichier" class="w3-padding <?= $menu_lf ?>"> <i
            class="fa fa-calendar fa-fw"></i> Mes reportings </a>
    <a href="#" class="w3-padding w3-hover-red" onclick="deconnexion()"> <i class="fa fa-sign-out fa-fw"></i>
        Déconnexion </a>
</nav>

<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer"  title="close side menu" id="myOverlay"></div>

<div class="w3-main" style="margin-left:250px;margin-top:43px;">
    <div class="w3-container w3-padding-32">
        <div id='dialog-confirm'></div>
        <?php
        $buffer = ob_get_clean();
        echo CodeCompressor::compress_html($buffer) ;
        echo $content;
        ob_start();
        ?>
    </div>
    <footer class="w3-container w3-padding-16 w3-dark-grey"><h4> </h4></footer>
</div>
<?php

$buffer = ob_get_clean();

echo CodeCompressor::compress_html($buffer) ;

ob_start();
    ?>
     <script>
         var mySidenav = document.getElementById("mySidenav"),
             overlayBg = document.getElementById("myOverlay");

         function w3_open() {
             if (mySidenav.style.display === 'block') {
                 mySidenav.style.display = 'none';
                 overlayBg.style.display = "none";
             } else {
                 mySidenav.style.display = 'block';
                 overlayBg.style.display = "block";
             }
         }

         function w3_close() {
             mySidenav.style.display = "none";
             overlayBg.style.display = "none";
         }

         function deconnexion() {
             $("#dialog-confirm").html("<h4 class='w3-center w3-text-red'>Voulez vrémment vous déconnecter ? </h4>");
             $("#dialog-confirm").dialog({
                 resizable:false,modal:true , title: "Déconnexion",
                 position: { my: "center top", at: "center top", of: $('body') },
                 show: { effect: "fade", duration: 1000 },
                 buttons: {
                     "Continuer": function () {
                         document.location.href= "<?= URL ?>dec/logout" ;
                     } ,
                     "Annuler" : function(){
                         $(this).dialog("close");
                     }
                 }
             })
         }
     </script>

 <?php

$script = ob_get_clean();
 
echo CodeCompressor::compress_js( $script ) ;
 
?>
</body></html>
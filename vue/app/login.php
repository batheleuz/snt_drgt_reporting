<!DOCTYPE html>
<html>
<title> CONNEXION </title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?= URL ?>css/w3css.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    body{
        background-image: url("<?= URL ?>image/12.png");
        background-repeat: no-repeat;
        background-size: auto, 100%;
    }
</style>
<body>
 <!-- img class="w3-image" src="<?= URL ?>image/12.jpg" alt="" style="min-width:500px;width:100%;height:100%;"  -->
    <div class="w3-display-left w3-padding w3-hide-small" style="width:35%">
        <div class="w3-white w3-opacity w3-hover-opacity-off w3-padding-large w3-round-large">
            <h1 class="w3-xlarge">Connexion</h1>
            <hr class="w3-opacity">
            <form action="" method="POST">
                <div class="w3-padding "><input type="text" class="w3-input" id="login" name="login" placeholder="Nom d'utilisateur" required autofocus > </div>
                <div class="w3-padding "><input type="password" class="w3-input" id="pwd" name="pwd" placeholder="mot de passe" required > </div>
                <div class="w3-padding ">
                    <button type="submit" class="w3-btn w3-deep-orange w3-border w3-border-white w3-hover-white w3-hover-border-deep-orange ">Connexion</button>
                    <a class="w3-margin-top w3-white" id="loading" style="display:none;"><i class='fa fa-spinner fa-pulse fa-2x fa-fw' ></i> Chargement ... </a>
                </div>
            </form>
            <?php
            if( isset($_POST['login']) or isset($_POST['pwd']) ):
                    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $preUser = Database::getDb()->all("user" , "login" , $_POST['login'] );

                    if( count($preUser) ==1 )

                        if( $preUser[0]['password'] == $_POST['pwd'] ){

                              $_SESSION['user'] = $preUser[0] ;
                              $service  = Database::getDb()->all("service" , "id" , $preUser[0]['service'] );
                              $_SESSION['service'] = $service[0];
                              $_SESSION['vr'] = Database::getDb()->all("formVrByDir");
                              $_SESSION['groupe_intervention'] = Database::getDb()->all("groupe_intervention");
                              $_SESSION['ui'] = Database::getDb()->all("ui");
                              $_SESSION['kpi'] = Database::getDb()->all("kpi");

                        ?>
                            <script>
                                document.getElementById("loading").style.display ="inline";
                                document.getElementById("login").classList.add("w3-pale-green");
                                document.getElementById("pwd").classList.add("w3-pale-green");
                                window.setTimeout(function(){
                                    document.location.href="<?= $actual_link; ?>"
                                } , 1000); </script>

                    <?php }  else { //password incorrect
                                 echo "<div class='w3-panel w3-red w3-display-container w3-round'><span onclick=\"this.parentElement.style.display='none'\" style='cursor:pointer' class=' w3-padding w3-button w3-display-topright'>X</span> <p>Le mot de passe est incorrect.</p> </div> ";
                             ?>
                            <script>
                                var login = document.getElementById("login");
                                    login.setAttribute("value"  , "<?= $_POST['login'] ?>") ;
                                    login.classList.add('w3-pale-green');
                                    document.getElementById("pwd").classList.add('w3-pale-red'); </script>

                    <?php }  else { //login incorrect
                                 echo "<div class='w3-panel w3-red w3-display-container w3-round'><span onclick=\"this.parentElement.style.display='none'\" style='cursor:pointer' class=' w3-padding w3-button w3-display-topright'>X</span> <p>Vérifier votre login. </p> </div> ";
                            ?>
                            <script>
                                document.getElementById("login").setAttribute("value"  , "<?= $_POST['login'] ?>");
                                document.getElementById("login").classList.add('w3-pale-red'); </script>
            <?php }
            endif;  ?>
        </div>
    </div>
</body>



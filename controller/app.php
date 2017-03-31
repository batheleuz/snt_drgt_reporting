<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 20/12/2016
 * Time: 11:15
 */

  if( $_SESSION['user'] != null ){

     ob_start();

         include "modele/".$_GET['page'];

     $content = ob_get_clean();
      
     include 'vue/template/app.php';

  }else{

     include "vue/app/login.php";

  }


<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 09/12/2016
 */

require_once  "config.php" ;
session_start();

if( isset($_SESSION['user']) ){

    if(isset($_GET['controller'])){

        include 'controller/'.$_GET['controller'];

    }else if (isset($_GET['page'])){

        include 'vue/'.$_GET['page'];

    }else{

         header  ("Location:app/reporting/nouveau/global");
    }
    
}else{

    include "vue/app/login.php";

}
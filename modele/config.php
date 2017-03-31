<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 06/02/2017
 * Time: 11:11
 */


function  all( $table , $where = null ){

    $rqt = "SELECT * FROM $table " ;

    if( $where != null )
        $rqt .= " WHERE $where " ;

    return Database::getDb()->rqt($rqt);
}

function count_code($colone){

    $n=Database::getDb()->rqt("select count(*) as n from code where colonne ='$colone' ") ;
    return $n[0]['n'];

}
/* =====================================================


  ==================================================== */

if( $_GET['page'] == "config.php" and isset ($_GET['param1'] ) ){

    include 'vue/ajax/'.$_GET['param1'].'.php';
}

else if( $_GET['controller'] == "ajax.php" ){
     
    extract($_POST);
    if( $action == "add_kpi" ){

        if($delai_time == "J" ) $delai_time = "DAY";
        if($delai_time == "H" ) $delai_time = "HOUR";

        $kpi = Database::getDb()->rqt( "SELECT *  FROM kpi WHERE abreviation='".trim($abreviation)."' " );

        if( !empty($kpi) ):
            
          echo  0 ;
        
        else:
            
          $id_ind = Database::getDb()->add( "pourcentage" , array( 'delai' => $delai, 'type_drgt' => trim(strtolower($type_drgt)), 'delai_time' => $delai_time ));
          if($id_ind >= 1 ){
              
            $id = Database::getDb()->add( "kpi" , array( 'abreviation' => $abreviation, 'type_kpi'=>'pourcentage' , 'id_indicateur' => $id_ind ));
            echo $id;
              
          }
        endif;

    }else if ( $action == "add_gi" ) {

        $gi = Database::getDb()->rqt("SELECT * FROM groupe_intervention WHERE  nom ='$nom_gi' and categorie ='$categorie' ");
        
        if (!empty($gi)):
            echo 0;
        else:
            
            $id_gi = Database::getDb()->add("groupe_intervention", array("nom" => strtoupper($nom_gi), "categorie" => $categorie ));
            echo $id_gi;
            
        endif;
        
    }else if( $action == "add_ui" ) {
        
        $ui = Database::getDb()->rqt("SELECT * FROM ui WHERE  nom='$nom_ui' ");
        if (!empty($ui)):
            
            echo 0;
        else:
            
            $arr = array('nom' => strtoupper($nom_ui), 'id_groupe' => implode(';', $groupes));
            $id_ui = Database::getDb()->add("ui", $arr);
            echo $id_ui;
            
        endif;
        
    }else if ( $action == "update_ui" ){

         if(Database::getDb()->modif("groupe_intervention" , "id_uis", implode(';', $liste_ui) , "id" , $id_gi ))
             echo 1;

         else
             echo 0;

    }else if($action == "suppr_kpi") {
        
        $kpi = all(kpi, "id ='$id' ");
        
        if (Database::getDb()->suppr($kpi[0]['type_kpi'], "id", $kpi[0]['id']) == true) {
            
            Database::getDb()->suppr("kpi", "id", $id);
            echo $kpi[0]['abreviation'];
        }
        
    }else if( $action == "get_file_code" ){
        
        foreach (all( "code" , "colonne ='$colonne' " ) as $code)
           $codes[] = array('signification' => utf8_encode($code['signification']) , 'code'=> $code['code'] );
        
        echo json_encode($codes);

    }else if( $action == "list_table"){
        
         echo json_encode( all( $table ) );

    }else if ($action == "list_files"){
        
         foreach (all( "colonnes" , "codification = 1 " ) as $file ){

             $file_list[] = array( 'col' => $file['col_label'] , 'nbre' =>count_code($file['col_label']) );
         }
        
        echo json_encode( $file_list );
        
    }

 }else{

   $menu_n = null ; $menu_l = null ; $menu_conf = "w3-text-blue";

     if( $_SESSION['service']['id_admin'] == $_SESSION['user']['id'] )
         include "vue/app/configuration.php";

     else
         print "<div class='w3-panel w3-pale-red w3-leftbar w3-sand w3-xxlarge w3-serif '>
                <p><i class='fa fa-warning'></i>
                <i>Vous ne disposer pas de privilèges pour entrer dans cette partie.</i></p>
               </div>     
              ";
 }

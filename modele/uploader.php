<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 15/02/2016
 *
 */

if ( $_POST['action'] == "upload_codif" ){

    $file = $_FILES['fichier_codif']; $inf_file = pathinfo($file['name']);

    if($inf_file['basename'] != ""){

        if ($inf_file['extension'] == "csv" )

            if (move_uploaded_file( $file['tmp_name'], "datas/uploads/codification/".$_POST['colonne'].".".$inf_file['extension'] )) {
                $upload = new UploadCodiFile( $_POST['colonne'].".".$inf_file['extension'] , "drgt_releves" , $_POST['colonne'] );
                $txt = $upload->enrg();

            } else
                $txt = "'".$file['name']."' ne peut pas être enregistré. Veillez réessez! <br> ";

        else
            $txt = "Extentension du fichier '" . $file_r['name'] ."' incorrect. Veuillez choisir un fichier csv.";
    }

}else{

    $file_r = $_FILES['fichier_releves']; $inf_file_r = pathinfo($file_r['name']);

    $file_c = $_FILES['fichier_encours']; $inf_file_c = pathinfo($file_c['name']);


    if( $inf_file_r['basename'] != "" ){ 

        if ($inf_file_r['extension'] == "csv" or $inf_file_r['extension'] == "lis" )
             
            if (move_uploaded_file($file_r['tmp_name'], "datas/uploads/drgt/" . $file_r['name'])) {
               $upload = new UploadDrgtFile ($file_r['name'], "drgt_releves" );
               $res_r = $upload->enrg($inf_file_r['extension']);

            } else 
               $res_r  = array('code' => 0 , 'texte' => "'".$file_r['name'] ."' ne peut pas être enregistré. Veillez réessez! <br> " );
            
        else
            $res_r = array ('code' => 0 , 'texte' => "Extentension du fichier '".$file_r['tmp_name']."' incorrect.<br> Veuillez choisir un fichier de type CSV ou LIS  ");

    }

    if( $inf_file_c['basename'] != ""){

        if( $inf_file_c['extension']  == "csv" or $inf_file_c['extension']  == "lis" )

            if( move_uploaded_file($file_c['tmp_name'] , "datas/uploads/drgt/".$file_c['name']) ){
                $upload = new UploadDrgtFile ( $file_c['name'] , "drgt_encours" ) ;
                $res_c =  $upload->enrg($inf_file_c['extension']);

            }else
               $res_c  = array('code'=>0 , 'texte' => "'".$file_c['name']."'ne peut pas être enregistré. Veillez réessez! ");
            
        else
           $res_c = array('code'=> 0 , 'texte' => "Extentension du fichier '".$file_c['tmp_name']."' incorrect.<br> Veuillez choisir un fichier de type CSV ou LIS ");

    }
    
    if( isset( $res_c)  and isset ( $res_r ) )
        echo json_encode( array( 'c' => $res_c , 'r' => $res_r ) );

    else if ( isset($res_c) )
        echo json_encode( array( 'c' => $res_c ));

    else if( isset( $res_r) )
        echo json_encode( array( 'r' => $res_r ) );

    else
        echo json_encode( array('r' => array('code' => 0 , 'texte' => 'Le fichier est trop lourd pour être chargé.' ) ) ) ;

    unset($upload);

}


<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 02/02/2017
 * Time: 10:40
 */

require_once 'date_functions.php';
require_once 'calcul_functions.php';

if( $_GET['controller']  == "ajax.php" ){

    extract($_POST);

    $_GLOBALS['vr'] = $_SESSION['vr'];
    $_GLOBALS['kpi'] = $_SESSION['kpi'];
    $_GLOBALS['service'] = $_SESSION['service'];

    if ( $action == "viewReporting" ){

        $dates = array( "start" => preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#", "$3-$2-$1", $debut  ),
                        "end" =>   preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#", "$3-$2-$1", $fin   ) );

        $rep = ReportingCRUD::getReportingById( $_SESSION['service']['nom'], trim($id) );

        $rep['contenue'] =  unserialize( $rep['contenue']);
        $lr = $rep['contenue'];

       if ($rep['type'] == "GlobalReportingBuilder")
            $reporting = new GlobalReportingBuilder($lr['name'], $lr['direction'], $lr['groupe_intervention'], $lr['column_kpi'], $dates, $lr['par'], $_GLOBALS);

       else if ($rep['type']== "AutreReportingBuilder") //$nom_reporting , $direction , $column, $column_kpi, $dates, $par , $_SESSION
            $reporting = new AutreReportingBuilder($lr['name'], $lr['direction'], $lr['column'], $lr['column_kpi'], $dates,$lr['par'], $_GLOBALS);
    }

    else {
        foreach( $kpi as $key ){
            $_kpi = Database::getDb()->rqt("SELECT * FROM kpi where id = '$key' "); $_kpi = $_kpi[0];
            $ind  = Database::getDb()->rqt("SELECT * FROM ".$_kpi['type_kpi']." where id ='".$_kpi['id_indicateur']."' ");
            $column_kpi[] = array_merge($ind[0]  , $_kpi );
        }

        $dates = array( "start" => preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#", "$3-$1-$2", $date_debut  )  ,
                        "end" =>   preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#", "$3-$1-$2", $date_fin   ) );

        if($action == "reporting_global"){

            if( !isset( $groupe_intervention) )
                $groupe_intervention = null;

            $reporting = new GlobalReportingBuilder($nom_reporting , $direction  , $groupe_intervention, $column_kpi, $dates, $par , $_GLOBALS);
        }
        else if( $action == "autre_reporting"){
            if( isset($colonnes_selected) ){
                $column = array();
                foreach ( $colonnes_selected as $col ){
                    $arr = $$col;
                    $column[$col] = $arr;
                }
            }
            $reporting = new AutreReportingBuilder($nom_reporting , $direction , $column, $column_kpi, $dates, $par , $_GLOBALS);
        }
        $serial =  $reporting->serialize();
    }

    $reporting->designTab();
    
}
    /*
        $monfichier = fopen("datas/services/PMT/tmp_file", 'r');
        $last_reporting = fgets($monfichier , 5000);
        $lr = json_decode( $last_reporting , TRUE );
        fclose($monfichier);
        $lreporting = new ReportingBuilder($lr['name'],$lr['direction'], $lr['groupe_intervention'] , $lr['column_kpi'], $lr['dates'] , $lr['par'], $_SESSION );
        $lreporting->designTab();
     */

     // Rwr::append( $_SESSION['service']['nom'] , $nom_reporting ,  "90" , $serial );

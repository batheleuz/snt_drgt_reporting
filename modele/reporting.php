<?php
/**
* Created by PhpStorm.
* User: Daboss
* Date: 09/02/2017
*
*/

if ( $_GET['controller']=="ajax.php" ){

    extract($_POST);

    if($action == "getDistincts") {
        
        $start = preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#", "$3-$1-$2", $date_debut);
        $end   = preg_replace("#^([0-9]{2})/([0-9]{2})/([0-9]{4})$#", "$3-$1-$2", $date_fin);
        $values = Database::getDb()->rqt("SELECT DISTINCT $colonne  as val from drgt_releves where date_rel BETWEEN '$start' AND '$end' ");
        print json_encode($values);
        
    }
    else if($action == "enrgLastReporting") {

        $monfichier = "datas/services/".$_SESSION['service']['nom']."/tmp_file.json";
        $last_reporting = file_get_contents($monfichier);
        $lr = json_decode($last_reporting, TRUE );
        fclose($monfichier);

        if (trim($classe) == "GlobalReportingBuilder")
            $rep = new GlobalReportingBuilder($lr['name'], $lr['direction'], $lr['groupe_intervention'], $lr['column_kpi'], $lr['dates'], $lr['par'], $_SESSION);
        else if (trim($classe) == "AutreReportingBuilder")
            $rep = new AutreReportingBuilder($lr['name'], $lr['direction'], $lr['column'], $lr['column_kpi'], $lr['dates'], $lr['par'], $_SESSION);

        if( ReportingCRUD::isExistant($_SESSION['service']['nom'] , $lr['name'] ,  $lr['par'] )  == TRUE )  echo -1;
            
        else {
            if ( ReportingCRUD::append($_SESSION['service']['nom'], $lr['name'], $lr['par'], $_SESSION['user']['login'], trim($classe), $rep->serialize(), Date('Y-m-d h:i:s')) > 0 )
                echo 1;
            else echo 0;
        }

    }
    else if($action == "supprReporting") {

       if( ReportingCRUD::remove( $_SESSION['service']['nom'] , $id ) > 0  )
           echo 1;
       else echo 0;
        
    }
    else if($action == "openReporting") {
        $data =  ReportingCRUD::getReportingById( $_SESSION['service']['nom'], $id );
        $data['contenue'] =  unserialize( $data['contenue'] );
        print json_encode($data);
    }
    else if($action == "getAvailableReportings") {

        $today = new DateTime( Date("Y-m-d") );  $start = new DateTime($dateD);  $end = new DateTime($dateF);

        $diff = $start->diff( $end, true ); $interval= $today->diff( $end, true );
        $pas = $diff->days + 1;

        $nb = round( $interval->days / $pas );

        $dates[] = array( 'start' => $start->format("d/m/Y"), 'end' =>$end->format("d/m/Y") );

        if( $par == "month" and $pas <= 31 ) {
            for ($i = 0; $i < $nb; $i++)
                $dates[] = array('start' => $start->modify(" +1 $par ")->format("d/m/Y"),
                    'end' => $end->modify(" +1 day ")->modify('last day of')->format("d/m/Y"));
        }else{
            for ($i= 0 ; $i < $nb  ; $i++ )
                $dates[] = array( 'start'=> $start->modify(" +$pas days ")->format("d/m/Y"),
                    'end' => $end->modify("+$pas days ")->format("d/m/Y"));
        }
        
        echo json_encode( array_reverse($dates) );
    }

}
else {
    require_once 'reporting_functions.php';
    $menu_n = null; $menu_lf = null; $menu_ld = null; $menu_conf = null;

    if (isset($_GET['param3']) && isset($_GET['param2']) && isset($_GET['param1'])){

        $menu_ld = "w3-deep-orange";
        include 'modele/calcule.php';

    } else if (isset($_GET['param2']) && isset($_GET['param1'])) {

        if ($_GET['param1'] == "liste" && $_GET['param2'] == "fichier") {
            
            $menu_lf = "w3-deep-orange";
            $reports = Database::getDb()->rqt("SELECT * FROM fichier");

            include 'vue/app/reportingFiles.php';
            
        } else if ($_GET['param1'] == "nouveau" && $_GET['param2'] == "global") {

            $menu_ng = "w3-deep-orange";
            $pageTitle = "Nouveau Reporting global";
            include 'vue/app/newReporting.php';

        } else if ($_GET['param1'] == "nouveau" && $_GET['param2'] == "autre") {

            $menu_na = "w3-deep-orange";
            $pageTitle = "Autre type de reporting";
            include 'vue/app/newReporting.php';

        }

    } else if (isset($_GET['param1'])) {

        if ($_GET['param1'] == "nouveau") {

            $menu_ng = "w3-deep-orange";
            $pageTitle = "Nouveau Reporting";
            include 'vue/app/newReporting.php';

        } else if ($_GET['param1'] == "upload") {

            $menu_up = "w3-deep-orange";
            include 'vue/app/uploadForm.php';
        }

    }

}
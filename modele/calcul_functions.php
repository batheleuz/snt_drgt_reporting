<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 07/02/2017
 * Time: 15:14
 */


/**
 * @param $champ
 * @return mixed
 */
function get_distinct($champ){

    $rqt="SELECT DISTINCT $champ FROM drgt  
          WHERE id_fichier='".$GLOBALS['id_fic']."' 
          AND date_orientation  != '0000-00-00' AND date_orientation  != ''
          AND id_fichier = '".$GLOBALS['id_fic']."'
          ORDER BY $champ ASC ";

    return Database::getDb()->rqt($rqt);
}


/**
 * Cette fonction permet de calculer le nombre de dérangement relevé dans un délais bien défini
 * @param $date_releve La date.
 * @param $nbre le délai entre la signalisation et le rélève peut prendre plus.
 * @param null $acces le type des dérangement ADSL ou TVO
 * @return le nombre de drgt relevé à la date $date_releve
 */
function  nbre_vr( $date_releve ,  $nbre , $acces=null ){

    $rqt = "SELECT COUNT(*) as n FROM drgt 
            WHERE TIMESTAMPDIFF(DAY, date_sig , date_releve ) < $nbre  
            AND drgt.date_releve='$date_releve'";

    if( $nbre == "+" ){

        $rqt = "SELECT COUNT(*) as n FROM drgt 
                WHERE TIMESTAMPDIFF(DAY, date_sig , date_releve ) >= 2  
                AND drgt.date_releve='$date_releve' ";
    }

    //Si la variable c'est le calcul d'un fichier.

    if( $GLOBALS['id_fic'] != null )
        $rqt .= "AND id_fichier ='".$GLOBALS['id_fic']."'" ;

    if( $acces == "acces_tv" )
        $rqt .= " AND acces_tv !='' ";
        
    elseif ($acces == "acces_adsl")
        $rqt .= " AND acces_tv ='' ";

    $n=Database::getDb()->rqt($rqt);
    return $n[0]['n'];
}


/**
 * @param $type_acces
 */
function create_reporting($type_acces){

    foreach ( get_distinct("date_releve")  as $date ){

        $vr2j    = nbre_vr($date['date_releve'] , 2 ,   $type_acces ) ;
        $vr_plus = nbre_vr($date['date_releve'] , "+" , $type_acces ) ;
        $vrTot =   (int)$vr2j + (int)$vr_plus;
        $prcnt =  ((int)$vr2j /(int)$vrTot  ) * 100;
        $taux  =  ((int) $prcnt/90) * 100 ;

        $report = new Reporting( $date['date_releve'] , $type_acces , $GLOBALS['id_fic'] , $vr_plus, $vrTot, $prcnt, $taux , $vr2j  );
        $report->report();
    }

}


/**
 * Cette fonction permet de sommer les differents valeur trouvée dans nos calcul dans
 * une variable global.
 * @param $vr2j
 * @param $vr_plus
 * @param $vrTot
 */
function vr_hydrate($vr2j ,$vr_plus , $vrTot ){

    $GLOBALS['vr2j'] = $GLOBALS['vr2j'] + $vr2j ;
    $GLOBALS['vr_plus'] = $GLOBALS['vr_plus'] + $vr_plus ;
    $GLOBALS['vrTot'] = $GLOBALS['vrTot'] + $vrTot ;
    $GLOBALS['p_vr2j'] = (int)$GLOBALS['vr2j'] / (int) $GLOBALS['vrTot'] * 100;
}


/**
 * @param $start
 * @param $end
 * @param $acces
 * @return string
 */
function tBodyByDate($start , $end , $acces  )
{


    $year = Date("Y");
    $week_num = Date( "W" , strtotime($end) );

    ob_start();

    for( $i=3 ; $i >= 0 ; $i-- ){
        $week = getWeek($week_num - $i , $year);
        $days = week_days($week['start'] , $week['end'] );

        $GLOBALS['vr2j'] = 0; $GLOBALS['vr_plus'] = 0; $GLOBALS['vrTot'] = 0;

        foreach ($days as $day){
            $report = new Reporting( $day, $acces);
            vr_hydrate( $report->getVr2j(), $report->getVrPlus() , $report->getTotal() , $report->getPVr2j() );
        }
        $taux  =  ( (int) $GLOBALS['p_vr2j'] / 90 ) * 100 ;
        echo    "<tr><td>".$week['start']."<br>-<br>".$week['end']."</td>".
                "<td>".$GLOBALS['vr2j']."</td> ".
                "<td>".$GLOBALS['vr_plus'] ."</td><td>".$GLOBALS['vrTot']."</td>".
                "<td>".round($GLOBALS['p_vr2j'] , 2)."</td>".
                "<td>".round($taux , 2)."</td></tr>";
    }

    $tab = ob_get_clean();

    return $tab;
}


/**
 * @param $acces
 * @return string
 */
function tBodyByAcces($acces){

    $GLOBALS['vr2j'] = 0 ;
    $GLOBALS['vr_plus'] = 0 ;
    $GLOBALS['vrTot'] = 0 ;

    ob_start();
    foreach ( get_distinct("date_releve")  as $date ){
        $report = new Reporting( $date['date_releve'] , $acces , $GLOBALS['id_fic'] );
        vr_hydrate( $report->getVr2j(), $report->getVrPlus() , $report->getTotal() , $report->getPVr2j() );
        echo $report;
    }
        echo  "<tr><td>Total</td><td >".$GLOBALS['vr2j']."</td> ".
              "<td>".$GLOBALS['vr_plus'] ."</td><td>".$GLOBALS['vrTot']."</td>".
              "<td>".round($GLOBALS['p_vr2j'] , 2 )."</td></tr>";

    $tab = ob_get_clean();
    return $tab;
}


/**
 * @param $acces
 * @return string
 */
function chartFromFile($acces){

    $data = null; $obj  = null;

    foreach ( get_distinct("date_releve")  as $date ){
        $report = new Reporting( $date['date_releve'] , $acces , $GLOBALS['id_fic'] );
        $xAxis[] = $date['date_releve'] ;
        $data .=  $report->getPVr2j() .",";
        $obj  .= "90 ,";
    }

    $series = array(
        array('name' => "Objectif" ,  'data' => $obj ),
        array('name' => "Vr 2 j" ,  'data' => $data )
    );
    
    return chart( $acces , $series , $xAxis );
}


/**
 * @param $start
 * @param $end
 * @param $acces
 * @return string
 */
function chartFromDates($start , $end , $acces ){

    $datas = null;  $obj  = null;
    $week_num = Date( "W" , strtotime($end) );

    for( $i=3 ; $i >= 0 ; $i-- ){

        $week = getWeek($week_num - $i , Date("Y") );
        $days = week_days($week['start'] , $week['end'] );
        $xAxis[] = $week['start']." ".$week['end'] ;
        $obj  .= "90 ,";
        $data = 0 ;

        foreach ($days as $day){
            $report = new Reporting( $day, $acces);
            $data = $data + (float)$report->getPVr2j();
        }

        $datas .=  $data/count($days) .",";
    }

    $series = array(
        array('name' => "Objectif" ,  'data' => $obj ),
        array('name' => "Vr 2 j" ,  'data' => $datas )
    );

    return chart( $acces , $series , $xAxis );
}


/**
 * @param $titre Le titre du graphe.
 * @param $series Les données dans un tableau de tableau : [name] => le nom de la serie et [data] => Les données
 * @param $xAxis L'axe x ici ce sont des dates
 * @return string le script du chart doit etre contenue par la balise <script> </script>
 */
function chart($titre, $series , $xAxis ){
    ob_start();

        echo "Highcharts.chart('container', { chart: {  type: 'spline' }, title: { text: '".$_POST['titre']." $titre' }, plotOptions: { series: { lineWidth: 5 } },".
             "xAxis: { categories: [ " ;

            foreach($xAxis as $x )
                echo "'".$x."',";

        echo " ], tickInterval: 1 }, yAxis:{ title: { text: 'Nombre de Dérangement Relevé ' } }, legend: { layout: 'vertical', align: 'right', ".
            " verticalAlign: 'middle',  borderWidth: 0 }, series: [";

            foreach($series as $serie )
                echo "{ name:' ".$serie['name']."'," .
                     "data : [ " .$serie['data']."] }, " ;

        echo "]});";

    $chart = ob_get_clean();

    return $chart;
}

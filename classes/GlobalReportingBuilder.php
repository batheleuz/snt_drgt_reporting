<?php

/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 20/02/2017
 * Time: 16:38
 */

class GlobalReportingBuilder implements Serializable {

    protected $name ,$direction ,$column_kpi ,$dates ,$par ,$groupe_intervention ;
    protected $_GLOBALS;

    public function __construct($name=null ,  $direction =null  , $gi=null  ,  $column_kpi=null , $dates=null , $par=null , $_GLOBALS=null ) {

            $this->name = $name ;
            $this->direction = strtoupper($direction);
            $this->groupe_intervention = $gi;
            $this->column_kpi = $column_kpi;
            $this->dates = $dates;
            $this->par = $par;
            $this->_GLOBALS = $_GLOBALS ;

    }

    public function designTab() {

        $this->enteteTab();
        print "<div class='w3-responsive'><table class='w3-table-all' id='tabl' border >";
        $this->thead();

        foreach ( $this->date_column() as  $day ){
             print "<tr>";
             print "<th style='vertical-align:middle;' rowspan='".(count($this->groupe_intervention)+1). "' >". $this->formate_fr($day['column']) ." </th> ";

                    if($this->groupe_intervention == null):
                        foreach ( $this->column_kpi  as $kpi){
                            $ndr =  $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi , $kpi['delai_time'] , $kpi['delai'] );
                            $total =  $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi );
                            $result = floatval( round( 100*($ndr / $total) , 2 ) ) ;
                            $result = ( is_nan($result)) ? 0 : $result;
                            echo "<td > $result </td>" ;
                        }

                    else:
                        foreach ( $this->groupe_intervention as $gi ):
                            print "<tr><td > ".$this->getGI($gi)['nom']." </td>";
                            foreach ( $this->column_kpi  as $kpi){
                                $ndr =  $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi , $kpi['delai_time'] , $kpi['delai'] );
                                $total =  $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi );
                                $result = floatval( round( 100*($ndr / $total) , 2 ) ) ;
                                $result = ( is_nan($result)) ? 0 : $result;
                                echo "<td > $result </td>" ;
                            }
                            print "</tr>";
                         endforeach;

                    endif;
             print "</tr>";
        }
        
        print "</table></div>";

        $tab = ob_get_clean();

        $tmp_file  =  fopen ( "datas/services/".$this->_GLOBALS['service']['nom']."/"."tmp_file.json"  , "w" );
        fwrite( $tmp_file , json_encode( $this->toArray() , JSON_PRETTY_PRINT ) );

        return $tab;
    }

    protected function date_column() {

        $dates = array();

        if( $this->par == "day") {
             
             foreach($this->days_between($this->dates['start'] , $this->dates['end'] ) as  $date )
                 $dates[]=array('column' => $date , 'rel' => $date );

        }else if ( $this->par == "week" ){

             if( date("W",strtotime( $this->dates['start'] ) ) == date("W",strtotime( $this->dates['end'] ) ) ) {
                 $dates[] = array('column' => $this->formate_fr($this->dates['start']) . " au " . $this->formate_fr($this->dates['end']) ,
                                  'rel' =>  date("W",strtotime( $this->dates['start'] ) )
                 );

             }else{
                 $wS =  new DateTime($this->dates['start']);
                 $wE =  new DateTime($this->dates['end']);
                 while (  $wS->format('W') != $wE->format('W') ){
                     $week = $this->getWeek( $wS->format('W') , $wS->format('Y') ) ;
                     $dates[] = array('column' => $this->formate_fr($week['start']) . " au " . $this->formate_fr($week['end']) ,
                         'rel' =>  $wS->format('W')
                     );
                     $wS->modify(" +1 week ");
                 }
             }

        }else if ( $this->par == "month" ){

            $m1 =  new DateTime($this->dates['start']);
            $m2 =  new DateTime($this->dates['end']  );

            if( $m1->format('m') == $m2->format('m') )
                $dates[] = array('column' =>    $this->getMonth( $m1->format('m') ) ,  'rel' => $m2->format('m') );

            else{
                $dates[] = array('column' => $this->getMonth($m1->format('m')) , 'rel' => $m1->format('m') ) ;
                while ( $m1->format('m') < $m2->format('m') ){
                    $m = $m1->modify(" +1 month ");
                    $dates[] = array('column'=> $this->getMonth($m->format('m'))  ,  'rel' => $m->format('m') );
                }
            }

        }else if($this->par == "trimester"){

            $m1 =  new DateTime($this->dates['start'])  ; $m2 =  new DateTime($this->dates['end']  );
            $trim1 =$this->trimestre($m1->format('m'))  ; $trim2 = $this->trimestre( $m2->format('m')) ;

            if( $trim1 == $trim2 )
                $dates[] = array( 'column' => "Trimeste " . $trim1, 'rel' => $trim1 );

            else{
                $dates[] = array('column' => "Trimeste ". $trim1, 'rel' => $trim1 ) ;
                while ( $m1->format('m') < $m2->format('m') ){
                    $m = $m1->modify(" +3 month ");
                    $dates[] = array('column' => "Trimeste ". $this->trimestre($m->format('m')), 'rel' => $this->trimestre($m->format('m')) );
                }
            }

        }
        return $dates;
    }

    protected function thead() {

        ob_start();
        print "<thead>";
        print "<th>Date</th>";

        if($this->groupe_intervention != null )
             print "<th>Groupe d'intervention</th>";

        foreach ( $this->column_kpi  as $kpi)
            print "<th>".$kpi['abreviation']."</th>";

        print "</thead>";

        $thead = ob_end_flush();
        return $thead;
    }

    protected function ndr( $date_releve , $produit , $gi = null , $tmpInd=null  , $valueTmpInd=null ){

        $rqt  = $this->ndrByTime( $this->par , $date_releve , date("Y") ) ;
        $rqt .= "AND ". $this->ndrByProd($produit) ." ";

        if($tmpInd != null  && $valueTmpInd != null)
            $rqt .= "AND  ". $this->ndrByDir($tmpInd   , $valueTmpInd);

        if( $gi != null )
            $rqt .= "AND ". $this->ndrByGI( $gi );

        //echo $rqt ;
        //echo "<br>" ;
        $n=Database::getDb()->rqt($rqt);
        return $n[0]['n'];
    }

    protected function ndrByTime($par , $value , $year ){

        $rqt = "SELECT COUNT(*) as n FROM drgt_releves  WHERE ";

        if($par  == "day") {

            $rqt .="drgt_releves.date_rel='$value' " ;

        }else if ($par  == "week" ){

            $rqt .="WEEK( drgt_releves.date_rel ) ='$value' AND YEAR (drgt_releves.date_rel) = '$year' " ;

        }else if ( $par == "month" ){

            $rqt .="MONTH(drgt_releves.date_rel) ='$value' AND YEAR (drgt_releves.date_rel) = '$year' " ;

        }else if($par  == "trimester" ){

            $rqt .="QUARTER(drgt_releves.date_rel) ='$value' AND YEAR (drgt_releves.date_rel) = '$year' " ;
        }
        return $rqt;
    }

    protected function ndrByProd($produit ){

        if( $produit == "tvo" )
            return  " acces_tv !='' ";

        elseif ($produit == "adsl")
            return  " acces_tv ='' ";

        elseif($produit == "bd")
            return " acces_tv ='' AND acces_adsl ='' ";

    }

    protected function ndrByDir( $tmpInd , $valueTmpInd ){

            $vr =   $this->getFromGlobal("vr" , "direction" ,  $this->direction ,"byTime", $tmpInd ) ;

            $rqt = "TIMESTAMPDIFF( ".$vr['byTime']." ,".$vr['col_start']." ,".$vr['col_end']." ) < $valueTmpInd ";

        return $rqt;
    }

    protected function ndrByGI( $id_gi = null ){

        $gi = $this->getFromGlobal("groupe_intervention", "id", $id_gi );

        $uis = explode( ";" , $gi['id_uis'] ) ;
        $rqt = null ;

        foreach ( $uis as $ui ){
            $mui  = $this->getFromGlobal("ui", "id", $ui );
            $rqt .= "OR ui='".$mui['nom']."' ";
        }


        return "(".substr($rqt , 2 ).")" ;

    }

    protected function days_between($start , $end ){

        $start = new DateTime($start);
        $end   = new DateTime($end);
        while ($start <= $end){
            $week[] = $start->format('Y-m-d') . "\n";
            $start->modify('+1 day');
        }

        return $week;
    }

    protected function getWeek($week, $year) {

        $dto = new DateTime();
        $result['start'] = $dto->setISODate($year, $week, 1)->format('Y-m-d');
        $result['end'] =   $dto->setISODate($year, $week, 8)->format('Y-m-d');
        return $result;

    }

    protected function formate_fr($date){
        return preg_replace("#^([0-9]{4})-([0-9]{2})-([0-9]{2})$#", "$3/$2/$1", $date );
    }

    protected function getMonth($index){
        
        $monthNames= array( "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Julliet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" );
        return $monthNames[$index-1];
    }

    protected function getFromGlobal ( ... $args ){

        if( count($args) == 5){

            foreach ($this->_GLOBALS[$args[0]] as $val)

                if ($val[$args[1]] == $args[2] &&  $val[$args[3]] == $args[4] )

                    return $val;
        }
        else if ( count($args) == 3 ){

            foreach ($this->_GLOBALS[$args[0]] as $val)

                if ($val[$args[1]] == $args[2])

                    return $val;

        }

    }

    protected function getGI( $id_gi ){

        foreach ( $this->_GLOBALS['groupe_intervention'] as  $vr )
            if ( $vr['id'] == $id_gi ){
                return $vr;
            }
        
        return null;
    }

    protected function trimestre ( $m ){

        $m = (int) $m ;
        $arr = array ( 1 => "1" , 2 => "1", 3 => "1" ,4 => "2" , 5 => "2"  , 6 => "2" ,
                       7 => "3" , 8 => "3", 9 => "3" ,10 => "4" ,11 => "4"  ,12 => "4"   );

        return $arr[$m];

    }

    protected function enteteTab(){

       print "<div class='w3-container w3-border w3-padding w3-white'>"
            ."<div class='w3-row'>"
            ."<div class='w3-col m1'>"
            ."<a class='w3-btn-floating w3-pale-green w3-text-teal w3-margin-top w3-hover-teal '"
            ." onclick='switcher(\"result\",\"content\");' >"
            ."<i class='fa fa-arrow-left'></i>"
            ."</a></div><div class='w3-col m9  w3-margin-top'>"
            ."<h4 class='w3-text-teal'>". $this->name." du ".
             $this->formate_fr($this->dates['start'])." au ".$this->formate_fr($this->dates['end'])
            ." - ".$this->direction . "</h4>"
            ."</div><div class='w3-col m2'>"
            ."<div class='w3-right w3-dropdown-hover' >"
            ."<a class='w3-btn w3-teal w3-margin-top' > <i class='fa fa-2x fa-bars'></i></a>"
            ."<div class='w3-dropdown-content w3-bar-block w3-border' style='width:250px;right:0'>"
            ."<a class='w3-white w3-hover-black w3-padding'  onclick='exporter(\"".$this->name."\")' "
            ."title ='Enregistrer sous format excel' ><i class='fa fa-file-excel-o'></i> Télécharger format excel </a>"
            ."<a class='w3-white w3-hover-black w3-padding' title ='Visualiser le graphe' "
            ." onclick='createCharts(\"".$this->name."\" ,\"".$this->dates['start']."\" , \"".$this->dates['end']."\" , \"area\")' >"
            ."<i class='fa fa-area-chart '></i> Créer un  Graphe Area </a>"
           ."<a class='w3-white w3-hover-black w3-padding' title ='Visualiser le graphe' "
           ." onclick='createCharts(\"".$this->name."\" ,\"".$this->dates['start']."\" , \"".$this->dates['end']."\" , \"column\")' >"
           ."<i class='fa fa-bar-chart '></i> Créer un  Graphe Colonne </a>"
           ."<a class='w3-white w3-hover-black w3-padding' title ='Visualiser le graphe' "
           ." onclick='createCharts(\"".$this->name."\" ,\"".$this->dates['start']."\" , \"".$this->dates['end']."\" , \"line\")' >"
           ."<i class='fa fa-line-chart '></i> Créer un  Graphe Line </a>"
            ."<a class='w3-white w3-hover-black w3-padding' title ='Enregistrer comme projet' "
            ."onclick='enrgReporting(\"".get_class($this)."\")' >   "
            ."<i class='fa fa-plus'></i> Enregistrer comme Projet "
            ."</a></div></div></div> "
            ."</div><hr class='w3-border w3-border-teal'> "
            ."</div>";
    }

    protected function toArray(){

        return  array(
            "name" => $this->name ,
            "direction" => $this->direction ,
            "column_kpi"  => $this-> column_kpi,
            "dates" => $this->dates ,
            "par" => $this->par ,
            "groupe_intervention" => $this->groupe_intervention
        );
    }

    public function tableForChart(){
        print "<table id='table_charts' style='display: none;' >" ;
        print "<thead> <th></th> ";
        foreach ( $this->column_kpi  as $kpi)
            print "<th>".$kpi['abreviation']."</th>";

        print "</thead><tbody>" ;
        foreach ( $this->date_column() as  $day ){
            if($this->groupe_intervention == null):
                print '<tr>';
                print '<th>'. $this->formate_fr($day['column']).'</th>';
                    foreach ( $this->column_kpi  as $kpi){
                        $ndr =  $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi , $kpi['delai_time'] , $kpi['delai'] );
                        $total =  $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi );
                        $result = floatval( round( 100*($ndr / $total) , 2 ) ) ;
                        $result = ( is_nan($result)) ? 0 : $result;
                        echo "<td > $result </td>" ;
                    }
                print '</tr>';
            else:
                foreach ( $this->groupe_intervention as $gi ):
                    print "<tr><th > ".$this->getGI($gi)['nom']." ". $this->formate_fr($day['column'])." </th>";
                    foreach ( $this->column_kpi  as $kpi){
                        $ndr = 1* (int) $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi , $kpi['delai_time'] , $kpi['delai'] );
                        $total = 1* (int) $this->ndr( $day['rel'] , $kpi['type_drgt'] , $gi );
                        $result = floatval( round( 100*($ndr / $total) , 2 ) ) ;
                        $result = ( is_nan($result)) ? 0 : $result;
                        echo "<td > $result </td>" ;
                    }
                    print "</tr>";
                endforeach;
            endif;
        }
        print "</tbody></table>";
    }

    public function serialize(){

        return serialize( $this->toArray() );

    }

    public function unserialize( $serialized ){

        return unserialize( $serialized ) ;

    }
    
}
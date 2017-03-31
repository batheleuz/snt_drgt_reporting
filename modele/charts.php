<?php

if( $_GET['controller']  == "ajax.php" ){

    extract($_POST);

    $_GLOBALS['vr'] = $_SESSION['vr'];
    $_GLOBALS['kpi'] = $_SESSION['kpi'];
    $_GLOBALS['service'] = $_SESSION['service'];

    $dates = array( "start" => $start ,  "end" =>  $end    );

    $repCharts = ReportingCRUD::getReportingByName($_SESSION['service']['nom'] , $name);

    $repCharts['contenue'] = unserialize( $repCharts['contenue']);

    $lr = $repCharts['contenue'];

    if ($repCharts['type'] == "GlobalReportingBuilder")
        $reportingCharts = new GlobalReportingBuilder($lr['name'], $lr['direction'], $lr['groupe_intervention'], $lr['column_kpi'], $dates, $lr['par'], $_GLOBALS);

    else if ($repCharts['type']== "AutreReportingBuilder")
        $reportingCharts = new AutreReportingBuilder($lr['name'], $lr['direction'], $lr['column'], $lr['column_kpi'], $dates,$lr['par'], $_GLOBALS);

    $reportingCharts->tableForChart();

    if( $type == "area"){
        $options = "plotOptions:{
                            area: {
                                pointStart: 1940,
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                       },";
    }else if($type == "line"){
        $type = "spline" ;
        $options = "plotOptions: {
                        spline: {
                            marker: {
                                enabled: false,
                                lineWidth: 10,
                            }
                        }
                    }, ";
    }else{
        $options = null;
    }

    include_once ("vue/app/createChartsScript.php");

}

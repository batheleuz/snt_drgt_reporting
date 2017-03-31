<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 17/02/2017
 * Time: 11:27
 */

function formate_fr($date){
    return preg_replace("#^([0-9]{4})-([0-9]{2})-([0-9]{2})$#", "$3/$2/$1", $date );
}

function getMonth($index){
    $monthNames= array( "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Julliet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" );
    return $monthNames[$index-1];
}

/**
 * @param $table
 * @param null $where
 * @return mixed array qui contient les données demandées
 */
function  all( $table , $where = null ){

    $rqt = "SELECT * FROM $table" ;

    if( $where != null )
        $rqt .= " WHERE $where" ;

    return Database::getDb()->rqt($rqt);
}
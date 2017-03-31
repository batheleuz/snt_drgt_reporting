<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 09/02/2017
 * Time: 14:19
 */

function getWeek($week, $year) {
    $dto = new DateTime();
    $result['start'] = $dto->setISODate($year, $week, 0)->format('Y-m-d');
    $result['end'] =   $dto->setISODate($year, $week, 6)->format('Y-m-d');
    return $result;
}

function days_between($start , $end ){  

    $start = new DateTime($start);
    $end   = new DateTime($end);
    while ($start <= $end){

        $week[] = $start->format('Y-m-d') . "\n";
        $start->modify('+1 day');
    }

    return $week;
}
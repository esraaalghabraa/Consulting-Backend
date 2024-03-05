<?php

function divideTime($start ,$end){
    $start=explodeTime($start);
    $end=explodeTime($end);
    $times=[];
    if($start[2]==$end[2]){
        for ($j=0,$i=$start[0];$i<$end[0];$j++,$i++){
            $times[$j]=$i.':'.$start[1].' '.$start[2];
        }
    }
    else{
        $j=0;
        for ($i=$start[0];$i<12;$j++,$i++){
            $times[$j]=$i.':'.$start[1].' '.$start[2];
        }
        $times[$j++]='12:'.$start[1].' '.$end[2];
        for ($i=1;$i<$end[0];$j++,$i++){
            $times[$j]=$i.':'.$start[1].' '.$end[2];
        }
    }
    return $times;
}

function explodeTime($time){
    $partTimes =explode(':',$time);
    $partTimes2 =explode(' ',$partTimes[1]);
    $explodeTime[0]=(int)$partTimes[0];
    $explodeTime[1]=$partTimes2[0];
    $explodeTime[2]=$partTimes2[1];
    return $explodeTime;
}



<?php

    include('config.php');
    require('racesiterators.class.php');
    
    $start = get_cgi_var("start", 0);
    $end = get_cgi_var("end", 0);
    //print ("Params".$start." ".$end."\n");
    
    if ($start)
    {
        $start = DateTime::createFromFormat('Y-m-d', $start);
        $start = $start->getTimestamp();
    }
    else
    {
        $start = time() - 15*24*3600;
    }
    if ($end)
    {
        $end = DateTime::createFromFormat('Y-m-d', $end);
        $end = $end->getTimestamp();
    }
    else
    {
        $end = $start + 30*24*3600;
    }
    header('Content-type: application/json; charset=UTF-8');
    //print ("Params".$start." ".$end."\n");
    new FullcalendarRacesIterator($start,$end);
?>

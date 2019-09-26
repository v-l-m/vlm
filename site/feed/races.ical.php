<?php

    include('config.php');
    require('racesiterators.class.php');
    
    $RaceTypeFilter=get_cgi_var('RaceType');
    header('Content-Type: text/calendar');
    new IcalRacesIterator($RaceTypeFilter);
?>

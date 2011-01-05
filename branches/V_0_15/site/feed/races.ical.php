<?php

    include('config.php');
    require('racesiterators.class.php');
    
    header('Content-Type: text/calendar');
    new IcalRacesIterator();
?>

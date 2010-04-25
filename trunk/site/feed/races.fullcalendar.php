<?php

    include('config.php');
    require('racesiterators.class.php');
    
    header('Content-type: application/json; charset=UTF-8');
    new FullcalendarRacesIterator();
?>

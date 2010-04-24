<?php

    include('config.php');
    require('racesiterators.class.php');
    include('../includes/strings.inc');
    
    header('Content-Type: application/rss+xml');
    new RssRacesIterator();
?>

<?php

    include('config.php');
    require('racesiterators.class.php');
    
    header('Content-Type: application/rss+xml');
    new RssRacesIterator();
?>

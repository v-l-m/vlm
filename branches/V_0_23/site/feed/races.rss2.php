<?php

    include('config.php');
    require_once('racesiterators.class.php');
    
    header('Content-Type: application/rss+xml');
    new RssRacesIterator();
?>

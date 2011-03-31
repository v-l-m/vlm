<?php
    include_once("includes/header.inc");
    include_once("players.class.php");
    include_once("config.php");
    include_once("playersiterators.class.php");

    //FIXME
    if (!isPlayerLoggedIn()) die('ERROR');

    $pl = new PlayersHtmlList();
    
?>

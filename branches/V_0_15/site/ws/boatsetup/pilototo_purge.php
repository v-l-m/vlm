<?php
    include_once("config.php");
    include_once("wslib.php");

    $ws = new WSBaseBoatsetup();

    $ws->fullusers->users->pilototoPurge(0); //0 = Purge ALL

    $ws->finish();
?>

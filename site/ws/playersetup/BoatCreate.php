<?php
    include_once("config.php");
    include_once("wslib.php");
    
    $ws = new WSBaseBoatCreate();

    $ws->CheckParams();
    
    $ws->finish();
?>
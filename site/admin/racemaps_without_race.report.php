<?php
    $PAGETITLE = "Report : Racemaps with no corresponding races";
    include ("htmlstart.php");
    include_once ("functions.php");

    echo "<h2>Note that it could be right (races not yet created)</h2>";

    htmlQuery("select idraces, concat('<img src=\"/minimap.php?idraces=', idraces, '\" />') as minimap from racesmap where idraces not in (select distinct idraces from races)");

    include ("htmlend.php");
?>

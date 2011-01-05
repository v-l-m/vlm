<?php
    $PAGETITLE = "Report : Races with no corresponding racemap";
    include ("htmlstart.php");
    include_once ("functions.php");

    echo "<h2>Could be old races...</h2>";

    htmlQuery("select idraces, racename from races where idraces not in (select distinct idraces from racesmap)");

    include ("htmlend.php");
?>
